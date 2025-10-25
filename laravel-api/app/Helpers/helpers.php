<?php

use Illuminate\Support\Str;

if (!function_exists('camel_case_to_snake_case')) {
    /**
     * Convert a model class name to its corresponding table name in snake_case.
     *
     * @param string $name
     * @return string
     */
    function camel_case_to_snake_case(string $name = ''): string
    {
        return Str::snake($name);
    }
}

if (!function_exists('generate_slug')) {
    /**
     * Convert a model class name to its corresponding table name in snake_case.
     *
     * @param string $name
     * @return string
     */
    function generate_slug(string $name = ''): string
    {
        return Str::slug($name);
    }
}

if (!function_exists('generate_snake_case')) {
    /**
     * Convert a model class name to its corresponding table name in snake_case.
     *
     * @param string $name
     * @return string
     */
    function generate_snake_case(string $name = ''): string
    {
        $name = Str::ascii($name);
        return Str::snake($name);
    }
}

if (!function_exists('string_to_price')) {
    /**
     * Chuyển chuỗi giá (có dấu .) thành int
     *
     * @param string $price
     * @return int
     */
    function string_to_price(string $price): int
    {
        return (int) str_replace('.', '', $price);
    }
}

if (!function_exists('price_to_string')) {
    /**
     * Định dạng số thành chuỗi giá có dấu .
     *
     * @param int|float $price
     * @return string
     */
    function price_to_string(int|float $price): string
    {
        return number_format((float) $price, 0, '', '.');
    }
}
