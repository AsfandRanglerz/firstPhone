<?php
namespace App\Repositories\Api;

use App\Models\User;
use App\Models\Vendor;
use App\Repositories\Api\Interfaces\AuthReposotoryInterface;
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
}