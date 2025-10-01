<?php

namespace App\Services\Interfaces\Auth;

interface ChangePasswordServiceInterface
{
    /**
     * Change user password
     *
     * @param int $userId
     * @param array $data
     * @return array
     */
    public function changePassword(int $userId, array $data): array;
}
