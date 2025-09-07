<?php
namespace App\Services\Interfaces\Auth;

use App\Http\Requests\Auth\AuthRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\Request;

interface AuthServiceInterface{
    public function authenticate(AuthRequest $request);
    public function register(RegisterRequest $request);
    public function getMe();
    public function refreshToken(Request $request);
    public function logout();
}
