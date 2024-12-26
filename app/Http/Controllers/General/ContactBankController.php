<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use  App\Models\ContactBank;
use  App\BusinessLocation;
use App\Contact;

class ContactBankController extends Controller
{
    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $allData =  ContactBank::OrderBy('id','desc')->where('business_id',$business_id)
                            ->where(function($query) use($request){
                                if ($request->location_id) {
                                    $query->where('location_id',$request->location_id);
                                }
                                if ($request->contact_id) {
                                    $query->where('contact_id',$request->contact_id);
                                }
                            })->paginate(30);
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $contacts = Contact::contactDropdown($business_id, false, false);
        return view('contact_banks.index')
                ->with(compact('allData','business_locations','contacts'));

    }
    public function add()
    {
        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $contacts = Contact::contactDropdown($business_id, false, false);
        return view('contact_banks.add')
                ->with(compact('business_locations','contacts'))
                ;
    }
    public function post_add(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $data = new ContactBank;
        $data->location_id = $request->location_id;
        $data->name = $request->name;
        $data->contact_id = $request->contact_id;
        $data->business_id = $business_id;
        $data->save();

        return redirect('contact-banks')
                ->with('yes',trans('home.Done Successfully'));
    }
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id, true);
        $contacts = Contact::contactDropdown($business_id, false, false);
        $data =  ContactBank::find($id);
        return view('contact_banks.edit')
                ->with(compact('business_locations','contacts','data'))
                ;
    }
    public function post_edit(Request $request,$id)
    {
        $business_id = request()->session()->get('user.business_id');
        $data =  ContactBank::find($id);
        $data->location_id = $request->location_id;
        $data->name = $request->name;
        $data->contact_id = $request->contact_id;
        $data->business_id = $business_id;
        $data->save();
        return redirect('contact-banks')
                ->with('yes',trans('home.Done Successfully'));
    }
    public function delete($id)
    {
        $sdata =  ContactBank::find($id);
        if ($data) {
            $data->delete();
        }
        return redirect('contact-banks')
                ->with('yes',trans('home.Done Successfully'));
    }
}
