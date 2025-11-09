<?php
namespace App\Services\Interfaces\User;

use Illuminate\Http\Request;

interface UserCatalogueServiceInterface
{
    public function getPermissions(int $id): array;
}
