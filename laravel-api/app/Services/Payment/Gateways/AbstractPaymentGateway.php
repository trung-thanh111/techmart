<?php

namespace App\Services\Payment\Gateways;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Exceptions\Payment\PaymentGatewayException;

/**
 * Abstract base class cho tất cả Payment Gateway
 *
 * Cung cấp:
 * - HTTP client với retry middleware
 * - Request logging (redact sensitive fields)
 * - Endpoint resolution (sandbox/production)
 * - Request ID generation cho idempotency
 */
abstract class AbstractPaymentGateway implements PaymentGatewayInterface
{
    protected Client $httpClient;
    protected array $config;
    protected string $baseUrl;

    public function __construct()
    {
        $gatewayName = $this->getGatewayName();
        $this->config = config("payment.gateways.{$gatewayName}", []);
        $this->baseUrl = $this->resolveBaseUrl();
        $this->httpClient = $this->createHttpClient();
    }

    // Abstract methods — mỗi gateway phải implement

    /**
     * Tạo chữ ký cho request
     *
     * @param string $rawData Chuỗi dữ liệu cần ký
     * @return string Chữ ký đã mã hóa
     */
    abstract protected function buildSignature(string $rawData): string;

    /**
     * Verify chữ ký từ response/webhook
     *
     * @param string $rawData Chuỗi dữ liệu gốc
     * @param string $signature Chữ ký cần verify
     * @return bool
     */
    abstract protected function verifySignature(string $rawData, string $signature): bool;

    // HTTP Client
    /**
     * Tạo Guzzle HTTP client với retry middleware
     * Retry khi gặp network timeout (ConnectException)
     */
    protected function createHttpClient(): Client
    {
        $maxRetries = config('payment.transaction.max_retries', 3);
        $retryDelay = config('payment.transaction.retry_delay', 2);

        $stack = HandlerStack::create();

        // Retry middleware — chỉ retry khi network error, không retry khi gateway trả lỗi logic
        $stack->push(Middleware::retry(
            function (int $retries, Request $request, ?Response $response, ?\Exception $exception) use ($maxRetries) {
                // Không retry quá số lần cho phép
                if ($retries >= $maxRetries) {
                    return false;
                }

                // Chỉ retry khi network timeout
                if ($exception instanceof ConnectException) {
                    Log::channel($this->getLogChannel())->warning(
                        "[{$this->getGatewayName()}] Retry lần {$retries} do network timeout",
                        ['uri' => (string) $request->getUri()]
                    );
                    return true;
                }

                return false;
            },
            function (int $retries) use ($retryDelay) {
                // Exponential backoff: 2s, 4s, 8s...
                return $retryDelay * 1000 * pow(2, $retries);
            }
        ));

        return new Client([
            'handler' => $stack,
            'base_uri' => $this->baseUrl,
            'timeout' => $this->config['timeout'] ?? 30,
            'connect_timeout' => 10,
            'http_errors' => false, // Không throw exception khi HTTP error, tự handle
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    // ============================================================
    // Request Helper
    // ============================================================

    /**
     * Gọi API gateway với try-catch, logging
     *
     * @param string $method HTTP method (POST, GET...)
     * @param string $path API path (VD: /v2/gateway/api/create)
     * @param array $payload Request body
     * @return array Response đã decode
     * @throws PaymentGatewayException
     */
    protected function makeRequest(string $method, string $path, array $payload = []): array
    {
        $gateway = $this->getGatewayName();

        // Log request (đã redact sensitive fields)
        $this->logRequest($method, $path, $payload);

        try {
            $response = $this->httpClient->request($method, $path, [
                'json' => $payload,
            ]);

            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody()->getContents(), true);

            // Log response
            $this->logResponse($path, $statusCode, $body);

            if ($statusCode >= 500) {
                throw new PaymentGatewayException(
                    "Gateway trả về HTTP {$statusCode}",
                    $gateway,
                    null,
                    ['http_status' => $statusCode, 'response' => $body]
                );
            }

            return $body ?? [];
        } catch (ConnectException $e) {
            Log::channel($this->getLogChannel())->error(
                "[{$gateway}] Network timeout sau {$this->config['timeout']}s",
                ['path' => $path, 'error' => $e->getMessage()]
            );

            throw new PaymentGatewayException(
                'Không thể kết nối đến payment gateway — vui lòng thử lại',
                $gateway,
                null,
                ['path' => $path],
                $e
            );
        } catch (RequestException $e) {
            Log::channel($this->getLogChannel())->error(
                "[{$gateway}] Request thất bại",
                ['path' => $path, 'error' => $e->getMessage()]
            );

            throw new PaymentGatewayException(
                'Lỗi kết nối payment gateway',
                $gateway,
                null,
                ['path' => $path],
                $e
            );
        }
    }

    // ============================================================
    // Helpers
    // ============================================================

    /**
     * Resolve base URL theo environment (sandbox/production)
     */
    protected function resolveBaseUrl(): string
    {
        $env = $this->config['environment'] ?? 'sandbox';
        $endpoints = $this->config['endpoints'] ?? [];

        return $endpoints[$env] ?? $endpoints['sandbox'] ?? '';
    }

    /**
     * Tạo unique request ID cho idempotency
     * Format: {gateway}_{timestamp}_{random}
     */
    public function generateRequestId(): string
    {
        $gateway = strtoupper($this->getGatewayName());
        $timestamp = now()->format('YmdHis');
        $random = Str::random(8);

        return "{$gateway}_{$timestamp}_{$random}";
    }

    /**
     * Log request, tự động redact sensitive fields
     */
    protected function logRequest(string $method, string $path, array $payload): void
    {
        Log::channel($this->getLogChannel())->info(
            "[{$this->getGatewayName()}] → {$method} {$path}",
            ['payload' => $this->redactSensitiveData($payload)]
        );
    }

    /**
     * Log response
     */
    protected function logResponse(string $path, int $statusCode, ?array $body): void
    {
        $level = $statusCode >= 400 ? 'error' : 'info';

        Log::channel($this->getLogChannel())->log(
            $level,
            "[{$this->getGatewayName()}] ← {$statusCode} {$path}",
            ['response' => $this->redactSensitiveData($body ?? [])]
        );
    }

    /**
     * Loại bỏ thông tin nhạy cảm khỏi data trước khi log
     * Các fields như secret_key, access_key, signature... sẽ bị mask
     */
    protected function redactSensitiveData(array $data): array
    {
        $redactFields = config('payment.logging.redact_fields', [
            'secret_key',
            'access_key',
            'signature',
            'card_number',
            'cvv',
        ]);

        foreach ($data as $key => $value) {
            if (in_array($key, $redactFields, true)) {
                $data[$key] = '***REDACTED***';
            } elseif (is_array($value)) {
                $data[$key] = $this->redactSensitiveData($value);
            }
        }

        return $data;
    }

    /**
     * Lấy log channel cho payment
     */
    protected function getLogChannel(): string
    {
        return config('payment.logging.channel', 'stack');
    }
}
