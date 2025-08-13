<?php

namespace App\Models;

use App\Models\Brand;
use App\Models\MobileModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MobileListing extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function model()
{
    return $this->belongsTo(MobileModel::class, 'model_id');
}

 public function brand()
{
    return $this->belongsTo(Brand::class, 'brand_id');
}

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }
}
