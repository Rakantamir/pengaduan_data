<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    protected $fillable = [
        'report_id',
        'status',
        'pesan',
    ]; 

    public function report ()
    {
    return $this->belongsTo
    (Report::class);
    }
}
