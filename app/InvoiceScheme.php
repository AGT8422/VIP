<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceScheme extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Returns list of invoice schemes in array format
     */
    public static function forDropdown($business_id)
    {
        $dropdown = InvoiceScheme::where('business_id', $business_id)
                                ->pluck('name', 'id');

        return $dropdown;
    }

    /**
     * Retrieves the default invoice scheme
     */
    public static function getDefault($business_id)
    {
        $default = InvoiceScheme::where('business_id', $business_id)
                                ->where('is_default', 1)
                                ->first();
        return $default;
    }



    //**...........eb
    // /...............
    // /....................

    public static function allLocation($business_id)
    {
        $array = [];
        $InvoiceScheme  = InvoiceScheme::where("business_id",$business_id)->get();
        foreach($InvoiceScheme as $key => $value){$array[$value->id] = $value->name;}
        return $array;
        
    }
    public static function FirstLocation($business_id)
    {
        $InvoiceScheme  = InvoiceScheme::where("business_id",$business_id)->first();
        if($InvoiceScheme->id != null){ return $InvoiceScheme->id; }else{ return null; }
    }
}
