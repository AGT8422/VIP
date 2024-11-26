<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agent;

class ChangeController extends Controller
{
    public function change($id)
    {
        if(request()->ajax()){
            
            session()->put('locale', $id);
            session()->put('user.language', $id);
            return redirect()->back();
        }
    }
}
