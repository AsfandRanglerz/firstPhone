<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Farmer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Auth;
use App\Services\Api\AuthService;
class AuthController extends Controller
{
    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    public function register(Request $request)
    {
        if($request->type == 'customer'){
            $request->validate([
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
            ]);
        }elseif($request->type == 'vendor'){
            $request->validate([
                'email' => 'required|email|unique:vendors,email',
                'password' => 'required|string|min:8|confirmed',
            ]);}

        $user = $this->authService->register($request->all());

        return response()->json(['message' => 'Registration successful', 'user' => $user], 201);
    }
    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     // Try to find Farmer first
    //     $user = Farmer::where('email', $request->email)->first();

    //     // Check if user exists and password matches
    //     if (!$user || !Hash::check($request->password, $user->password)) {
    //         return response()->json(['message' => 'Invalid credentials'], 401);
    //     }

    //     // If it's a Farmer, check status (optional, if you need status check)
    //     if ($user->status != 1) {
    //         return response()->json(['message' => 'Your account is deactivated'], 403);
    //     }

    //     // Create token
    //     $token = $user->createToken('auth_token')->plainTextToken;

    //     return response()->json([
    //         'message' => 'Login Successful',
    //         'token' => $token,
    //         'user' => $user,
    //         'role' => 'farmer'
    //     ]);
    // }

    // public function sendResetLink(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //     ]);

    //     $farmer = Farmer::where('email', $request->email)->first();

    //     if (!$farmer) {
    //         return response()->json(['message' => 'Email not found.'], 404);
    //     }

    //     $email = $farmer->email;

    //     $existing = DB::table('password_resets')->where('email', $email)->first();
    //     if ($existing) {
    //         return response()->json(['message' => 'Reset link already sent.'], 200);
    //     }

    //     $token = Str::random(60);
    //     DB::table('password_resets')->insert([
    //         'email' => $email,
    //         'token' => $token,
    //         'created_at' => now()
    //     ]);

    //     $link = url("/api/verify-reset-token/{$token}");
    //     Mail::to($email)->send(new ResetPasswordMail(['url' => $link]));

    //     return response()->json(['message' => 'Reset link sent successfully.'], 200);
    // }

    // public function verifyResetToken($token)
    // {
    //     $record = DB::table('password_resets')->where('token', $token)->first();

    //     if (!$record) {
    //         return response()->json(['message' => 'Invalid or expired token.'], 404);
    //     }

    //     return response()->json(['message' => 'Token is valid.', 'email' => $record->email], 200);
    // }

    // public function resetPassword(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'token' => 'required',
    //         'password' => 'required|min:8|confirmed',
    //     ]);

    //     $record = DB::table('password_resets')->where([
    //         ['email', '=', $request->email],
    //         ['token', '=', $request->token],
    //     ])->first();

    //     if (!$record) {
    //         return response()->json(['message' => 'Invalid token or email.'], 400);
    //     }

    //     $hashedPassword = Hash::make($request->password);

    //     $farmer = Farmer::where('email', $request->email)->first();

    //     if ($farmer) {
    //         $farmer->update(['password' => $hashedPassword]);
    //     } else {
    //         return response()->json(['message' => 'User not found.'], 404);
    //     }

    //     DB::table('password_resets')->where('email', $request->email)->delete();

    //     return response()->json(['message' => 'Password has been reset successfully.'], 200);
    // }

    // public function getProfile(Request $request)
    // {
    //     // Check if the user is authenticated as Farmer
    //     $user = Auth::guard('api')->user(); // Retrieve authenticated user

    //     if (!$user) {
    //         return response()->json(['message' => 'Unauthorized'], 401); // If no user found, return 401
    //     }

    //     return response()->json([
    //         'data' => $user
    //     ]);
    // }

    // public function updateProfile(Request $request)
    // {
    //     // Validate the input
    //     $request->validate([
    //         'name' => 'required|string',
    //         'email' => 'required|email',
    //         'phone' => 'required|string',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
    //     ]);

    //     // Prepare the data for updating
    //     $data = $request->only(['name', 'email', 'phone']);

    //     if ($request->hasFile('image')) {
    //         $file = $request->file('image');
    //         $extension = $file->getClientOriginalExtension();
    //         $filename = time() . '.' . $extension;
    //         $file->move('public/farmers/assets/images', $filename);
    //         $data['image'] = 'public/farmers/assets/images/' . $filename;
    //     }

    //     // Check if the user is authenticated as Farmer
    //     $user = Auth::guard('api')->user(); // Retrieve authenticated user

    //     if ($user) {
    //         $user->update($data);
    //         return response()->json(['message' => 'Profile updated successfully']);
    //     } else {
    //         return response()->json(['error' => 'Unauthorized'], 401);
    //     }
    // }

}
