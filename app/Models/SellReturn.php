<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellReturn extends Model
{
    use HasFactory;
    public function getDocumentAttribute()
    {
        return json_decode($this->attributes['document']);
    }
}
