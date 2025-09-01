<?php

namespace App\Models;

use App\Models\VendorImage;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Vendor extends Authenticatable
{
    use HasApiTokens, HasFactory;
    protected $guarded = [];

     public function images()
    {
        return $this->hasMany(VendorImage::class);
    }
}
