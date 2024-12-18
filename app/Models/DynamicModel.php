<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicModel extends Model
{
    use HasFactory;
    protected $connection = 'business';
}
