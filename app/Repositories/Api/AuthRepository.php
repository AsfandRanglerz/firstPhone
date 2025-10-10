<?php

namespace App\Repositories\Api;

use App\Models\User;
use App\Models\Vendor;
use App\Mail\ForgotOTPMail;
use App\Models\VendorImage;
use App\Mail\UserEmailOtp;
use App\Mail\UserCredentials;
use Illuminate\Support\Facades\Hash;
use App\Models\EmailOtp;
use Illuminate\Support\Facades\Mail;
use App\Repositories\Api\Interfaces\AuthRepositoryInterface;
use Illuminate\Support\Str;


class AuthRepository implements AuthRepositoryInterface
{


public function register(array $request)
{
    // ✅ Generate OTP and Token
    $otp = rand(1000, 9999);
    $otpToken = Str::random(40); // unique token

    // ✅ Handle CNIC Front Image
    $cnicFrontPath = null;
    if (!empty($request['cnic_front'])) {
        $cnicFront = $request['cnic_front'];
        $filename = time() . '_cnic_front_' . uniqid() . '.' . $cnicFront->getClientOriginalExtension();
        $cnicFront->move(public_path('admin/assets/images/users/'), $filename);
        $cnicFrontPath = 'public/admin/assets/images/users/' . $filename;
    }

    // ✅ Handle CNIC Back Image
    $cnicBackPath = null;
    if (!empty($request['cnic_back'])) {
        $cnicBack = $request['cnic_back'];
        $filename = time() . '_cnic_back_' . uniqid() . '.' . $cnicBack->getClientOriginalExtension();
        $cnicBack->move(public_path('admin/assets/images/users/'), $filename);
        $cnicBackPath = 'public/admin/assets/images/users/' . $filename;
    }

    // ✅ Handle Profile Image (single or multiple)
    $imagePath = null;
    if (!empty($request['image'])) {
        $file = is_array($request['image']) ? $request['image'][0] : $request['image'];
        $filename = time() . '_profile_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('admin/assets/images/users/'), $filename);
        $imagePath = 'public/admin/assets/images/users/' . $filename;
    }

    // ✅ Store Data in email_otp Table
    $record = EmailOtp::create([
        'name'           => $request['name'] ?? null,
        'email'          => $request['email'] ?? null,
        'phone'          => $request['phone'] ?? null,
        'password'       => isset($request['password']) ? Hash::make($request['password']) : null,
        'image'          => $imagePath,
        'cnic_front'     => $cnicFrontPath,
        'cnic_back'      => $cnicBackPath,
        'location'       => $request['location'] ?? null,
        'repair_service' => $request['repair_service'] ?? 0,
        'otp'            => $otp,
        'otp_token'      => $otpToken,
        'type'           => $request['type'] ?? null,
    ]);

    // ✅ Send OTP Email using your template Mailable
    if (!empty($request['email'])) {
        Mail::to($request['email'])->send(new UserEmailOtp($request['name'] ?? 'User', $otp));
    }

    return $record;
}


public function emailOtpVerify(Request $request)
{
    // ✅ Validate input
    $request->validate([
        'email' => 'required|email',
        'otp'   => 'required|digits:4',
    ]);

    // ✅ Find record in email_otp table
    $otpRecord = EmailOtp::where('email', $request->email)
        ->where('otp', $request->otp)
        ->first();

    if (!$otpRecord) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Invalid OTP or Email. Please try again.',
        ], 400);
    }

    // ✅ Check user type and insert accordingly
    if ($otpRecord->type === 'vendor') {
        // Insert into Vendor table
        Vendor::create([
            'name'       => $otpRecord->name,
            'email'      => $otpRecord->email,
            'phone'      => $otpRecord->phone,
            'password'   => $otpRecord->password,
            'image'      => $otpRecord->image,
            'cnic_front' => $otpRecord->cnic_front,
            'cnic_back'  => $otpRecord->cnic_back,
            'location'   => $otpRecord->location,
            'service'    => $otpRecord->repair_service,
        ]);
    } elseif ($otpRecord->type === 'customer') {
        // Insert into User table
        User::create([
            'name'     => $otpRecord->name,
            'email'    => $otpRecord->email,
            'phone'    => $otpRecord->phone,
            'password' => $otpRecord->password,
        ]);
    } else {
        return response()->json([
            'status'  => 'error',
            'message' => 'Invalid user type found.',
        ], 400);
    }

    // ✅ Optional: delete OTP record after verification
    $otpRecord->delete();

    // ✅ Success response
    return response()->json([
        'status'  => 'success',
        'message' => 'OTP verified successfully! Account created.',
    ], 200);
}



    public function login(array $request)
    {
        if ($request['type'] === 'customer') {
            $user = User::where('email', $request['email'])->first();
        } elseif ($request['type'] === 'vendor') {
            $user = Vendor::where('email', $request['email'])->first();
        } else {
            return ['error' => 'Invalid user type'];
        }
        if (!$user) {
            return ['error' => ucfirst($request['type']) . ' not found'];
        }
        if ((isset($user->status) && $user->status == 0)) {
            return ['error' => 'Your account has been deactivated.'];
        }
        if (!Hash::check($request['password'], $user->password)) {
            return ['error' => 'Invalid credentials'];
        }
        $token = $user->createToken($request['type'] . '_token')->plainTextToken;
        return [
            'user' => $user,
            'token' => $token
        ];
    }
    public function sendOtp(array $request)
    {
        if ($request['type'] === 'customer') {
            $user = User::where('email', $request['email'])->first();
        } elseif ($request['type'] === 'vendor') {
            $user = Vendor::where('email', $request['email'])->first();
        }
        if (!$user) {
            return ['error' => ucfirst($request['type']) . ' not found'];
        }
        $otp = rand(1000, 9999);
        $user->otp = $otp;
        $user->save();
        Mail::to($user->email)->send(new ForgotOTPMail($otp));
        return [
            'otp' => $otp,
            'email' => $user->email
        ];
    }
    public function verifyOtp(array $request)
    {
        if ($request['type'] === 'customer') {
            $user = User::where('email', $request['email'])->first();
        } elseif ($request['type'] === 'vendor') {
            $user = Vendor::where('email', $request['email'])->first();
        } else {
            return ['error' => 'Invalid user type'];
        }
        if (!$user || $user->otp !== $request['otp']) {
            return ['error' => 'Invalid OTP'];
        }
        $user->otp = null;
        $user->save();
        return ['email' => $user->email];
    }
    public function resetPassword(array $request)
    {
        if ($request['type'] === 'customer') {
            $user = User::where('email', $request['email'])->first();
        } elseif ($request['type'] === 'vendor') {
            $user = Vendor::where('email', $request['email'])->first();
        } else {
            return ['error' => 'Invalid user type'];
        }
        if (Hash::check($request['password'], $user->password)) {
            return ['error' => 'New password cannot be the same as the old password'];
        }
        $user->password = Hash::make($request['password']);
        $user->save();
        return $user;
    }

    public function logout()
    {
        $user = auth()->user();
        if ($user) {
            $user->tokens()->delete();
            return true;
        }
        return ['error' => 'User not authenticated'];
    }
    public function updateProfile(array $request)
    {
        if ($request['type'] === 'customer') {
            $user = User::where('email', $request['email'])->first();
        } elseif ($request['type'] === 'vendor') {
            $user = Vendor::where('email', $request['email'])->first();
        } else {
            return ['error' => 'Invalid user type'];
        }
        if (!$user) {
            return ['error' => ucfirst($request['type']) . ' not found'];
        }
        $user->name = $request['name'] ?? $user->name;
        $user->phone = $request['phone'] ?? $user->phone;
        $user->email = $request['email'] ?? $user->email;
        if (isset($request['image']) && $request['image'] instanceof \Illuminate\Http\UploadedFile) {
            $file = $request['image'];
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $file->move(public_path('admin/assets/images/users'), $filename);
            $user->image = 'public/admin/assets/images/users/' . $filename;
        }

        $user->save();
    }
    public function changePassword(array $request)
    {
        $user = auth()->user();
        if (!$user) {
            return ['error' => 'User not authenticated'];
        }
       if ($request['new_password'] !== $request['confirm_password']) {
        return ['error' => 'New password and confirm password do not match'];
    }
    if (Hash::check($request['new_password'], $user->password)) {
        return ['error' => 'New password cannot be the same as the old password'];
    }
        $user->password = Hash::make($request['new_password']);
        $user->save();
        return $user;
    }
}
