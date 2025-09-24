<?php
namespace App\Services\Interfaces;

use Illuminate\Http\Request;

interface BaseServiceInterface {
    public function save(Request $request, ?int $id = null);
    public function restore(Request $request, int $id = 0);
    public function findByField(string $name = '', mixed $value = null);
    public function showBySlug(string $slug = '');
}
