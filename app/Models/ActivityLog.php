<?php

// app/Models/ActivityLog.php

// app/Models/ActivityLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = "activity_logs";
    protected $fillable = [
        'sub_admin_id',
        'performed_by_sub_admin_id',
        'action',
        'description',
    ];

    public function target()
    {
        return $this->belongsTo(SubAdmin::class, 'sub_admin_id');
    }

    public function performer()
    {
        return $this->belongsTo(SubAdmin::class, 'performed_by_sub_admin_id');
    }

    public function performedBy()
{
    return $this->belongsTo(SubAdmin::class, 'performed_by_sub_admin_id');
}

}