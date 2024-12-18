<?php

namespace App\Models\POS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Uploads extends Model
{
    use HasFactory,SoftDeletes;
    // protected $connection = 'sqlite';
    protected $appends = ['file_url'];
    // protected $table = "wait_entity";
    
    // /**
    //  * Get the products image.
    //  *
    //  * @return string
    //  */
    public function getFileUrlAttribute()
    {
        $file_url ='';
        if (!empty($this->file)) {
            $file_url = asset('storage/izo_db/' . rawurlencode($this->file));
        } 
        return $file_url;
    }
}
