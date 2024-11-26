<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChequeAction extends Model
{
    use HasFactory;
    protected $appends = ['action'];
    public static  function types()
    {
        return [
            0=>trans('home.Added'),
            1=>trans('home.Collected'),
            2=>trans('home.refund'),
            
        ];
    }
    public function getActionattributes()
    {
        if ( array_key_exists($this->attributes['type'],ChequeAction::types()) ) {
            return ChequeAction::types()[$this->attributes['type']];
        }
    }
}
