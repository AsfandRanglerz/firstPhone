<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'brands';

    protected $fillable = [
        'name',
        'slug',
    ];

    public function mobileModels()
    {
        return $this->hasMany(MobileModel::class, 'brand_id', 'id');
    }
}