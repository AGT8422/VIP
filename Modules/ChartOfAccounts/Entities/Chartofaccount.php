<?php

namespace Modules\ChartOfAccounts\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chartofaccount extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\ChartOfAccounts\Database\factories\ChartofaccountFactory::new();
    }
}
