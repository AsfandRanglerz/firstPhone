<?php

namespace App\Models;

use App\Models\MobileModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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