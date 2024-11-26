<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopCategory extends Model
{
    use HasFactory,SoftDeletes;

    protected $append = ["icon_url"];

  public function getIconUrlAttribute()
    {
        $icon_url ='';
        if (!empty($this->icon)) {
            $icon_url = asset('public/uploads/img/' . rawurlencode($this->icon));
        } 
        return $icon_url;
    }
}
