<?php

namespace App\Services\Interfaces\System;

interface SystemServiceInterface
{
    public function getStructure(): array;

    public function saveSettings(array $settings, ?int $userId = null): bool;

    public function getPublicConfigs(): array;

    public function showByKeyword(string $keyword = '');
}
