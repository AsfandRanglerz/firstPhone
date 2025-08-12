<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recentsearch extends Model
{
    use HasFactory;

    protected $table = "recentsearches";
    protected $guarded = [];
}