<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Return list of units for a business
     *
     * @param int $business_id
     * @param boolean $show_none = true
     *
     * @return array
     */
    public static function forDropdown($business_id, $show_none = false, $only_base = true)
    {
        $query = Unit::where('business_id', $business_id);
        // if ($only_base) {
        //     $query->whereNull('base_unit_id');
        // }
        $units    = $query->select(DB::raw('CONCAT(actual_name, " (", short_name, ") ") as name'), 'id')->whereNull("product_id")->get();
        $dropdown = $units->pluck('name', 'id');
        if ($show_none) {
            $dropdown->prepend(__('messages.please_select'), '');
        }
        return $dropdown;
    }
    public static function forDropdownInPrice($business_id, $show_none = false, $only_base = true)
    {
        $query = Unit::where('business_id', $business_id)->orderBy("default","desc")->where("in_product","!=",0);
        // if ($only_base) {
        //     $query->whereNull('base_unit_id');
        // }
        $units    = $query->select(DB::raw('CONCAT(actual_name, " (", short_name, ") ") as name'), 'id')->whereNull("product_id")->get();
        $dropdown = $units->pluck('name', 'id');
        if ($show_none) {
            $dropdown->prepend(__('messages.please_select'), '');
        }
        return $dropdown;
    }

    public function sub_units()
    {
        return $this->hasMany(\App\Unit::class, 'base_unit_id');
    }

    public function base_unit()
    {
        return $this->belongsTo(\App\Unit::class, 'base_unit_id');
    }
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
