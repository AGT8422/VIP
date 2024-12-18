<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Software extends Model
{
    use HasFactory,SoftDeletes;
    protected $appends = ['image_url','alter_image_url'];
 
    public function getImageUrlAttribute()
    {
        $image_url ='';
        if (!empty($this->image)) {
            $image_url = asset('public/uploads/img/' . rawurlencode($this->image));
        } 
        return $image_url;
    }
    public function getAlterImageUrlAttribute()
    {
        $alter_image_url = [];
        if (!empty($this->alter_image) && $this->alter_image != "[]") {
            $alter = json_decode($this->alter_image);
            foreach($alter  as $ie){
                $image_url         = asset('public/uploads/img/' . rawurlencode($ie));
                $alter_image_url[] = $image_url ;
            }
        } 
        return $alter_image_url;
    }
}
