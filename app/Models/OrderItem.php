<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    
    protected $guarded=[];

     public function product()
    {
        return $this->belongsTo(VendorMobile::class, 'product_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

  public function deviceReceipts()
{
    // ✅ recommended: use order_item_id in device_receipts table
    return $this->hasMany(DeviceReceipt::class, 'order_item_id', 'id');
}

}
