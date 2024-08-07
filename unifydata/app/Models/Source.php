<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Source extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'config'];

    protected $casts = [
        'config' => 'array',
    ];
}
