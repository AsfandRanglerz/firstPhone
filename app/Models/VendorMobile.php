<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorMobile extends Model
{
    use HasFactory;

    protected $table = 'vendor_mobiles';
    protected $guarded = [];

     public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function model()
    {
        return $this->belongsTo(MobileModel::class, 'model_id');
    }

    // VendorMobile.php
		public function vendor()
		{
			return $this->belongsTo(Vendor::class, 'vendor_id', 'id'); 
			// assuming VendorMobile me vendor_id field hai
		}

}
