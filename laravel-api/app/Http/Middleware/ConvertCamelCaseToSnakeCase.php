<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConvertCamelCaseToSnakeCase
{
    private array $preserveValueKeys = [
        'attributeData',
        'attribute_data',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $this->convertKeysToSnakeCase($request->all());
        $request->replace($input);

        return $next($request);
    }

    /**
     * Recursively convert array keys from camelCase to snake_case
     * @param array $data
     * @param bool $preserveKeys Nếu true, không convert các key bên trong, chỉ giữ nguyên
     */
    private function convertKeysToSnakeCase(array $data, bool $preserveKeys = false): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            $newKey = $preserveKeys ? $key : Str::snake($key);

            if (is_array($value)) {
                $shouldPreserve = in_array($key, $this->preserveValueKeys, true);
                $value = $this->convertKeysToSnakeCase($value, $shouldPreserve);
            }

            $result[$newKey] = $value;
        }
        return $result;
    }
}
