<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceLayout extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    protected $append  = ['logo_url'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'product_custom_fields' => 'array',
        'contact_custom_fields' => 'array',
        'location_custom_fields' => 'array',
        'common_settings' => 'array',
    ];

    /**
     * Get the location associated with the invoice layout.
     */
    public function locations()
    {
        return $this->hasMany(\App\BusinessLocation::class);
    }

    /**
     * Get the products image.
     *
     * @return string
     */
    public function getLogoUrlAttribute()
    {
        $logo_url ='';
        if (!empty($this->logo)) {
            $logo_url = asset('public/'. rawurlencode('uploads\img\\' .$this->logo));
        } 
        return $logo_url;
    }
 
    /**
     * Return list of invoice layouts for a business
     *
     * @param int $business_id
     *
     * @return array
     */
    public static function forDropdown($business_id)
    {
        $layouts = InvoiceLayout::where('business_id', $business_id)
                    ->pluck('name', 'id');

        return $layouts;
    }

    //**...........eb
    // /...............
    // /....................

    public static function allLocation($business_id)
    {
        $array = [];
        $InvoiceLayout  = InvoiceLayout::where("business_id",$business_id)->get();
        foreach($InvoiceLayout as $key => $value){$array[$value->id] = $value->name;}
        return $array;
        
    }
    public static function FirstLocation($business_id)
    {
        $InvoiceLayout  = InvoiceLayout::where("business_id",$business_id)->first();
        if($InvoiceLayout != null){ return $InvoiceLayout->id; }else{ return null; }
    }
}
