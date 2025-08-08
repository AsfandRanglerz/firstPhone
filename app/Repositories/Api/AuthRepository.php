<?php
namespace App\Repositories\Api;

use App\Models\User;
use App\Models\Vendor;
use App\Repositories\Api\Interfaces\AuthRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthRepository implements AuthRepositoryInterface{
    public function register(array $request){
        if($request['type'] == 'customer'){
            $customer = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'phone' => $request['phone'],
                'password' => Hash::make($request['password']),
            ]);
            return $customer;
        }else if($request['type'] == 'vendor'){
            $vendor = Vendor::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'phone' => $request['phone'],
                'password' => Hash::make($request['password']),
            ]);
            return $vendor;
        }
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
    public function sendOtp(array $request){
        if ($request['type'] === 'customer') {
            $user = User::where('email', $request['email'])->first();
        } elseif ($request['type'] === 'vendor') {
            $user = Vendor::where('email', $request['email'])->first();
        }
        if (!$user) {
            return ['error' => ucfirst($request['type']) . ' not found'];
        }
        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->save();
        return [
            'otp'=> $otp,
            'email' => $user->email
        ];
    }
    public function verifyOtp(array $request){
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
    public function resetPassword(array $request){
        if ($request['type'] === 'customer') {
            $user = User::where('email', $request['email'])->first();
        } elseif ($request['type'] === 'vendor') {
            $user = Vendor::where('email', $request['email'])->first();
        } else {
            return ['error' => 'Invalid user type'];
        }
        if(Hash::check($request['password'], $user->password)){
            return ['error' => 'New password cannot be the same as the old password'];
        }
        $user->password = Hash::make($request['password']);
        $user->save();
        return $user;
    }

    public function logout(){
        $user = auth()->user();
        if ($user) {
            $user->tokens()->delete();
            return true;
        }
        return ['error' => 'User not authenticated'];
    }
    public function updateProfile(array $request){
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
    }
    public function changePassword(array $request){
        $user = auth()->user();
        if (!$user) {
            return ['error' => 'User not authenticated'];
        }
        if (!Hash::check($request['current_password'], $user->password)) {
            return ['error' => 'Current password is incorrect'];
        }
        if (Hash::check($request['new_password'], $user->password)) {
            return ['error' => 'New password cannot be the same as the old password'];
        }
        $user->password = Hash::make($request['new_password']);
        $user->save();
        return $user;
    }
}