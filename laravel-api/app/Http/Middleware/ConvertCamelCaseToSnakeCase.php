<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConvertCamelCaseToSnakeCase
{
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
     */
    private function convertKeysToSnakeCase(array $data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            $newKey = Str::snake($key);
            if (is_array($value)) {
                $value = $this->convertKeysToSnakeCase($value);
            }
            $result[$newKey] = $value;
        }
        return $result;
    }
}
