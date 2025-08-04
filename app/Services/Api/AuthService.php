<?php
namespace App\Services\Api;
use App\Repositories\Api\Interfaces\AuthRepositoryInterface;
class AuthService{
    protected $authRepo;
    public function __construct(AuthRepositoryInterface $authRepo)
    {
        $this->authRepo = $authRepo;
    }
    public function register(array $request){
        return $this->authRepo->register($request);
    }
}