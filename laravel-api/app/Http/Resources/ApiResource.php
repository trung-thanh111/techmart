<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class ApiResource extends JsonResource
{
    private const TIMESTAMP = 'Y-m-d\TH:i:s.v\Z';

    /**
     * Generate a standardized timestamp.
     *
     * @return string The formatted timestamp.
     */
    private static function generateTimestamp(): string
    {
        return now()->format(self::TIMESTAMP);
    }

    /**
     * Generate a successful JSON response.
     *
     * @param mixed $data The data to include in the response.
     * @param string $message A message to include in the response.
     * @param int $httpStatus The HTTP status code for the response.
     * @return JsonResponse The JSON response object.
     */
    public static function ok(mixed $data = null, string $message = '', int $httpStatus = Response::HTTP_OK): JsonResponse {
        return response()->json([
            'status' => true,
            'code' => $httpStatus,
            'message' => $message,
            'data' => $data,
            'timestamp' => self::generateTimestamp(),
        ], $httpStatus);
    }

    /**
     * Generate an error JSON response.
     *
     * @param mixed $errors The errors to include in the response.
     * @param string $message A message to include in the response.
     * @param int $httpStatus The HTTP status code for the response.
     * @return JsonResponse The JSON response object.
     */
    public static function error(mixed $errors = null, string $message = '', int $httpStatus = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse {
        return response()->json([
            'status' => false,
            'code' => $httpStatus,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => now()->format(self::TIMESTAMP),
        ], $httpStatus);
    }
    /**
     * Generate a JSON response with a message.
     *
     * @param string $message The message to include in the response.
     * @param int $httpStatus The HTTP status code for the response.
     * @return JsonResponse The JSON response object.
     */
    public static function message(string $message = '', int $httpStatus = Response::HTTP_OK){
        return response()->json([
            'status' => $httpStatus === Response::HTTP_OK || $httpStatus === Response::HTTP_CREATED || $httpStatus === Response::HTTP_ACCEPTED,
            'code' => $httpStatus,
            'message' => $message,
        ], $httpStatus);
    }
}
