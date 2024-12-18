<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class CloseController extends Controller
{

    public function close()
    {
        $activity = new Activity;
        $activity->business_id = 1 ;
        $activity->description = "logouts" ;
        $activity->subject_id  = 1 ;
        $activity->save();
    }
}
