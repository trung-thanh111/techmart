<?php
namespace App\Services\Interfaces\Auth;

use App\Http\Requests\Auth\AuthRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\Request;

interface AuthServiceInterface{
    public function authenticate(AuthRequest $request, string $guard = "api");
    public function register(RegisterRequest $request);
    public function getMe(string $guard = 'api');
    public function refreshToken(Request $request, string $guard = 'api');
    public function logout(string $guard = 'api');
}
