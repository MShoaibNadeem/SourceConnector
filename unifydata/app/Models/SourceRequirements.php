<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class SourceRequirements extends Model
{
    use HasFactory;
    protected $fillable = ['type', 'name', 'requirements'];
}
