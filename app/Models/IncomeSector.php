<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeSector extends Model
{
    protected $fillable = [
        'sector_name',
        'description',
        'business_id',
    ];
}
