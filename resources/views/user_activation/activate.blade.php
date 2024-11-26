<div class="modal-dialog   " role="document" style="font-size:medium !important;width:50%">
    <div class="modal-content">
        
      <div class="modal-header">
        <h1>@lang("lang_v1.activate")    ## {{   ($device_no)?$device_no->device_no:null   }} ##  </h1>
      </div>
      <div class="modal-body">
        <div class="content">

            {!!  Form::open(["url"=> action("UserActivationController@store"), "id" => "activate_table" ,"method"=>"POST", "files" => true]) !!}
            <div class="row">
                <div class="col-md-4 p-10">
                    {!!  Form::label("user_name" , __("lang_v1.user_name"))!!}
                    {!!  Form::text("user_name",($device_no)?$device_no->name:null ,[ "readOnly", "class"=>"form-control","id"=>"user_name","placeholder"=>__("messages.enter_user_name")])!!}
                </div>
                <div class="col-md-4 p-10">
                    {!!  Form::label("user_email" , __("lang_v1.user_email"))!!}
                    {!!  Form::text("user_email",($device_no)?$device_no->email:null ,[ "readOnly", "class"=>"form-control","id"=>"user_email","placeholder"=>__("messages.enter_user_email")])!!}
                </div>
                <div class="col-md-4 p-10">
                    {!!  Form::label("user_address" , __("lang_v1.user_address"))!!}
                    {!!  Form::text("user_address",($device_no)?$device_no->address:null ,[ "readOnly", "class"=>"form-control","id"=>"user_address","placeholder"=>__("messages.enter_user_address")])!!}
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4 p-10">
                    {!!  Form::label("user_mobile" , __("lang_v1.user_mobile"))!!}
                    {!!  Form::text("user_mobile",($device_no)?$device_no->mobile:null ,[ "readOnly", "class"=>"form-control","id"=>"user_mobile","placeholder"=>__("messages.enter_user_mobile")])!!}
                </div>
                <div class="col-md-4 p-10">
                    {!!  Form::label("user_service" , __("lang_v1.user_services"))!!}
                    {!!  Form::text("user_service",($device_no)?$device_no->services:null ,[ "readOnly", "class"=>"form-control","id"=>"user_service","placeholder"=>__("messages.enter_user_services")])!!}
                </div>
                <div class="col-md-4 p-10">
                    {!!  Form::label("user_username" , __("lang_v1.user_username"))!!}
                    {!!  Form::text("user_username",($device_no)?$device_no->device_no:null ,[ "readOnly", "class"=>"form-control","id"=>"user_username","placeholder"=>__("messages.enter_user_username")])!!}
                </div>
                <div class="clearfix"></div>
                <div class="col-md-4 p-10">
                    {!!  Form::label("user_date" , __("messages.date"))!!}
                    {!!  Form::date("user_date",($device_no)?$device_no->created_at:null, [ "readOnly", "class"=>"form-control","id"=>"user_user_date"])!!}
                </div>
                <div class="col-md-4 p-10">
                    {!!  Form::label("user_period" , __("lang_v1.user_period"))!!}@show_tooltip(__('lang_v1.user_period_help_text'))
                    {!!  Form::number("user_period",1,["class"=>"form-control","min"=>1,"id"=>"user_period","placeholder"=>__("messages.enter_user_period")])!!}
                </div>
                <div class="col-md-4 p-10">
                    {!!  Form::label("user_number_device" , __("User Devices Number"))!!} 
                    {!!  Form::number("user_number_device",1,["class"=>"form-control","min"=>1,"id"=>"user_number_device","placeholder"=>__("enter user device number")])!!}
                </div>
                <div class="clearfix"></div>
                <h1>&nbsp;</h1>
               
                
                
            </div>
            <div class="modal-footer">
              <input type="submit" class="btn btn-primary "  value="{{__( 'messages.save' )}}"> 
            </div>
            {!!  Form::close() !!}
        </div>
      </div>
    </div>
  </div>
  
   