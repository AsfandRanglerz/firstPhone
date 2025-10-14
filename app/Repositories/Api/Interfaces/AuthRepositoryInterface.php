<?php
namespace App\Repositories\Api\Interfaces;
interface AuthRepositoryInterface
{
    public function register(array $request);
    public function login(array $request);
    public function logout();
    public function sendOtp(array $request);
    public function verifyOtp(array $request);
    public function resendOtp(array $request);
    public function updateProfile(array $request);
    public function changePassword(array $request);
    public function forgotPasswordSendOtp(array $request);
    public function forgotPasswordVerifyOtp(array $request);
    public function forgotPasswordReset(array $request);
    public function forgotPasswordResendOtp(array $data);
}
