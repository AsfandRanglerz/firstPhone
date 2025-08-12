<?php

namespace App\Models;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MobileModel extends Model
{
    use HasFactory;

    protected $table = 'models';
    protected $guarded = [];

 public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }
}