<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Services\Api\AuthService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        try {
            $request->validate([
                // 'name' => 'required|string|max:255',
                'email' => 'required|email',
                // 'password' => 'required|string|min:8',
                // 'phone' => 'nullable|string|max:20',
                // 'location' => 'nullable|string|max:255',
                'type' => 'required|in:customer,vendor',
            ]);

            // ✅ Send OTP via repository
            $otpData = $this->authService->sendOtp($request->all());

            if (isset($otpData['error'])) {
                return ResponseHelper::error(null, $otpData['error'], 'error', 400);
            }

            return ResponseHelper::success([
                'message' => 'OTP sent to your email. Please verify to complete registration.',
                'email' => $request->email
            ], 'OTP sent successfully', 'success', 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseHelper::error($e->errors(), 'Validation failed', 'error', 422);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 'Error during registration', 'error', 500);
        }
    }



    public function login(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'password' => 'required|string',
                'phone' => 'nullable|string|max:20',
                'location' => 'nullable|string|max:255',
                'type' => 'required|in:customer,vendor',
            ]);
            $result = $this->authService->login($request->all());
            if (isset($result['error'])) {
                return ResponseHelper::error(null, $result['error'], 'error', 401);
            }
            return ResponseHelper::success($result, 'Login successful', 'success', 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseHelper::error($e->errors(), 'Validation failed', 'error', 422);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 'Something went wrong', 'error', 500);
        }
    }

    public function sendOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'type' => 'required|in:customer,vendor',
            ]);

            $data = $this->authService->sendOtp($request->all());

            if (isset($data['error'])) {
                return ResponseHelper::error(null, $data['error'], 'error', 400);
            }

            return ResponseHelper::success($data, 'OTP sent successfully', 'success', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 'Error sending OTP', 'error', 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'otp' => 'required|numeric',
                'name' => 'required|string|max:255',
                'password' => 'required|string|min:8',
                'phone' => 'required|string|max:20',
                'location' => 'nullable|string|max:255',
                'type' => 'required|in:customer,vendor',
            ]);

            $result = $this->authService->verifyOtp($request->all());

            if (isset($result['status']) && $result['status'] === 'error') {
                return ResponseHelper::error(null, $result['message'], 'error', 401);
            }

            return ResponseHelper::success($result['data'], $result['message'], 'success', 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseHelper::error($e->errors(), 'Validation failed', 'error', 422);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 'Error verifying OTP', 'error', 500);
        }
    }

    public function resendOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'type' => 'required|in:customer,vendor',
            ]);

            $result = $this->authService->resendOtp($request->all());

            if (isset($result['status']) && $result['status'] === 'error') {
                return ResponseHelper::error(null, $result['message'], 'error', 400);
            }

            return ResponseHelper::success(
                ['email' => $result['data']['email']],
                $result['message'],
                'success',
                200
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 'Error resending OTP', 'error', 500);
        }
    }



    public function forgotPasswordResendOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'type' => 'required|in:customer,vendor',
            ]);

            $response = $this->authService->forgotPasswordResendOtp($request->all());

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to resend OTP',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function forgotPasswordSendOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'type' => 'required|in:customer,vendor',
            ]);

            $result = $this->authService->forgotPasswordSendOtp($request->all());

            return ResponseHelper::success(
                $result['data'] ?? null,
                $result['message'] ?? 'OTP sent successfully',
                $result['status'] ?? 'success'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseHelper::error(
                $e->errors(),
                'Validation failed',
                422
            );
        } catch (\Exception $e) {
            Log::error('Forgot password send OTP error: ' . $e->getMessage());

            return ResponseHelper::error(
                null,
                'Something went wrong. Please try again later.',
                500
            );
        }
    }


    public function forgotPasswordVerifyOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'otp' => 'required|numeric',
                'type' => 'required|in:customer,vendor',
            ]);

            $result = $this->authService->forgotPasswordVerifyOtp($request->all());

            return ResponseHelper::success(
                null,
                $result['message'] ?? 'OTP verified successfully',
                $result['status'] ?? 'success'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseHelper::error(
                $e->errors(),
                'Validation failed',
                422
            );
        } catch (\Exception $e) {
            Log::error('Forgot password verify OTP error: ' . $e->getMessage());

            return ResponseHelper::error(
                null,
                'Something went wrong. Please try again later.',
                500
            );
        }
    }


    public function forgotPasswordReset(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:8',
                'type' => 'required|in:customer,vendor',
            ]);

            $result = $this->authService->forgotPasswordReset($request);

            return ResponseHelper::success(
                null,
                $result['message'] ?? 'Password reset successfully',
                $result['status'] ?? 'success'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseHelper::error(
                $e->errors(),
                'Validation failed',
                422
            );
        } catch (\Exception $e) {
            Log::error('Forgot password reset error: ' . $e->getMessage());

            return ResponseHelper::error(
                null,
                'Something went wrong. Please try again later.',
                500
            );
        }
    }


    public function logout()
    {
        try {
            $result = $this->authService->logout();
            if (isset($result['error'])) {
                return ResponseHelper::error(null, $result['error'], 'error', 401);
            }
            return ResponseHelper::success(null, 'Logout successful', 'success', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 'An error occurred during logout', 'error', 500);
        }
    }
    public function getProfile()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return ResponseHelper::error(null, 'User not authenticated', 'error', 401);
            }
            return ResponseHelper::success($user, 'Profile retrieved successfully', 'success', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 'An error occurred while retrieving profile', 'error', 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            if ($request->type == 'customer') {
                $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:users,email,' . auth()->id(),
                    'phone' => 'nullable|string|max:15',
                    'type' => 'required|in:customer,vendor',
                    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);
            } elseif ($request->type == 'vendor') {
                $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:vendors,email,' . auth()->id(),
                    'phone' => 'nullable|string|max:15',
                    'type' => 'required|in:customer,vendor',
                    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);
            }
            $result = $this->authService->updateProfile($request->all());
            if (isset($result['error'])) {
                return ResponseHelper::error(null, $result['error'], 'error', 401);
            }
            return ResponseHelper::success($result, 'Profile updated successfully', 'success', 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseHelper::error($e->errors(), 'Validation failed', 'error', 422);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 'An error occurred during profile update', 'error', 500);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $result = $this->authService->changePassword($request->all());
            if (isset($result['error'])) {
                return ResponseHelper::error(null, $result['error'], 'error', 401);
            }
            return ResponseHelper::success($result, 'Password changed successfully', 'success', 200);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 'An error occurred during password change', 'error', 500);
        }
    }
}
