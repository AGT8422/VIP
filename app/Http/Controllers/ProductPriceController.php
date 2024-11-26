<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductPriceController extends Controller
{
    //

    public function index(){
        return view("product.partials.prices");
    }


    public function store(Request $request){
        
    }

    public function update(Request $request,$id){
        
    }
}
