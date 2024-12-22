@extends('layouts.app')

@section('title', __( 'user.add_user' ))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>@lang( 'user.add_user' )</h1>
</section>

<!-- Main content -->
<section class="content">
{!! Form::open(['url' => action('ManageUserController@store'), 'method' => 'post', 'id' => 'user_add_form' ]) !!}
  <div class="row">
    <div class="col-md-12">
  @component('components.widget')
      <div class="col-md-2">
        <div class="form-group">
          {!! Form::label('surname', __( 'business.prefix' ) . ': *') !!}
            {!! Form::text('surname', null, ['class' => 'form-control', "id"=>"surname",'required', 'placeholder' => __( 'business.prefix_placeholder' ) ]); !!}
        </div>
      </div>
      <div class="col-md-5">
        <div class="form-group">
          {!! Form::label('first_name', __( 'business.first_name' ) . ':*') !!}
            {!! Form::text('first_name', null, ['class' => 'form-control',"id"=>"first_name", 'required', 'placeholder' => __( 'business.first_name' ) ]); !!}
        </div>
      </div>
      
      <div class="col-md-5">
        <div class="form-group">
          {!! Form::label('last_name', __( 'business.last_name' ) . ':') !!}
            {!! Form::text('last_name', null, ['class' => 'form-control', "id"=>"last_name",'placeholder' => __( 'business.last_name' ) ]); !!}
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="col-md-4">
        <div class="form-group">
           
            <div class="form-group">
              {!! Form::label('user_account_id', __( 'lang_v1.account' ) . ': ') !!}
                {!! Form::select('user_account_id', $accounts,null, ['class' => 'form-control select2',  "id"=>"user_account_id", 'placeholder' => __( 'messages.please_select' ) ]); !!}
            </div>
            
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('email', __( 'business.email' ) . ': ') !!}
            {!! Form::text('email', null, ['class' => 'form-control',  "id"=>"email", 'placeholder' => __( 'business.email' ) ]); !!}
        </div>
      </div>

       
      @if(count($allLocations) >= 1)
      @php 
          $default_location =  array_key_first($allLocations);
          $search_disable = false; 
        @endphp
      @else
        @php $default_location = null;
        $search_disable = true;
        @endphp
      @endif
    <div class="col-sm-4 hide">
      <div class="form-group">
        {!! Form::label('location', __('Location') . ':') !!} @show_tooltip(__('lang_v1.product_location_help'))
        {!! Form::select('location', $allLocations ,$default_location, ['class' => 'form-control select2' ,  'placeholder' => __( 'messages.please_select' ), 'required' ,'id' => 'location']); !!}
      </div>
    </div>

      <div class="col-md-4">
        <div class="form-group">
          <div class="checkbox">
            <br/>
            <label>
                 {!! Form::checkbox('is_active', 'active', true, ['id'=>'is_active' , "class" => 'input-icheck status']); !!} {{ __('lang_v1.status_for_user') }}
            </label>
            @show_tooltip(__('lang_v1.tooltip_enable_user_active'))
          </div>
        </div>
      </div>
      
  @endcomponent

  </div>
  <div class="col-md-12">
    @component('components.widget', ['title' => __('lang_v1.roles_and_permissions')])
      <div class="col-md-4">
        <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('allow_login', 1, true, 
                [ 'class' => 'input-icheck', 'id' => 'allow_login']); !!} {{ __( 'lang_v1.allow_login' ) }}
              </label>
            </div>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="user_auth_fields">
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('username', __( 'business.username' ) . ':') !!}
          @if(!empty($username_ext))
            <div class="input-group">
              {!! Form::text('username', null, ['id'=>'username','class' => 'form-control', 'placeholder' => __( 'business.username' ) ]); !!}
              <span class="input-group-addon">{{$username_ext}}</span>
            </div>
            <p class="help-block" id="show_username"></p>
          @else
              {!! Form::text('username', null, ['id'=>'username','class' => 'form-control', 'placeholder' => __( 'business.username' ) ]); !!}
          @endif
          <p class="help-block">@lang('lang_v1.username_help')</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('password', __( 'business.password' ) . ':*') !!}
            {!! Form::password('password', ['id'=>'password','class' => 'form-control', 'required', 'placeholder' => __( 'business.password' ) ]); !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('confirm_password', __( 'business.confirm_password' ) . ':*') !!}
            {!! Form::password('confirm_password', ['id'=>'confirm_password','class' => 'form-control', 'required', 'placeholder' => __( 'business.confirm_password' ) ]); !!}
        </div>
      </div>
    </div>
      <div class="clearfix"></div>
      <div class="col-md-6">
        <div class="form-group">
          {!! Form::label('role', __( 'user.role' ) . ':*') !!} @show_tooltip(__('lang_v1.admin_role_location_permission_help'))
            {!! Form::select('role', $roles, null, ['id'=>'role','class' => 'form-control select2']); !!}
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="col-md-3">
          <h4>@lang( 'role.access_locations' ) @show_tooltip(__('tooltip.access_locations_permission'))</h4>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
                <label>
                  {!! Form::checkbox('access_all_locations', 'access_all_locations', true, 
                ['class' => 'input-icheck' ,'id'=>'access_all_locations']); !!} {{ __( 'role.all_locations' ) }} 
                </label>
                @show_tooltip(__('tooltip.all_location_permission'))
            </div>
          </div>
          @foreach($locations as $location)
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('location_permissions[]', 'location.' . $location->id, false, 
                [ 'class' => 'input-icheck','id'=>'location_permissions']); !!} {{ $location->name }}
              </label>
            </div>
          </div>
          @endforeach
        </div>
    @endcomponent
  </div>

  <div class="col-md-12">
    @component('components.widget', ['title' => __('sale.sells')])
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('cmmsn_percent', __( 'lang_v1.cmmsn_percent' ) . ':') !!} @show_tooltip(__('lang_v1.commsn_percent_help'))
            {!! Form::text('cmmsn_percent', null, ['class' => 'form-control input_number','id'=>'cmmsn_percent', 'placeholder' => __( 'lang_v1.cmmsn_percent' ) ]); !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('max_sales_discount_percent', __( 'lang_v1.max_sales_discount_percent' ) . ':') !!} @show_tooltip(__('lang_v1.max_sales_discount_percent_help'))
            {!! Form::text('max_sales_discount_percent', null, ['class' => 'form-control input_number', 'id'=>'max_sales_discount_percent','placeholder' => __( 'lang_v1.max_sales_discount_percent' ) ]); !!}
        </div>
      </div>
      @if(count($patterns) >= 1)
        @php 
          $default_pattern =  array_key_first($patterns);
      
        @endphp
      @else
        @php $default_pattern = null;
        
        @endphp
      @endif
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('pattern_id', __( 'business.patterns' ) . ':*') !!}  
            {!! Form::select('pattern_id[]', $patterns , $default_pattern, ['class' => 'form-control select2 ',"required",'multiple'=>'true', 'id'=>'pattern_id'   ]); !!}
        </div>
      </div>
      <div class="clearfix"></div>
      
      <div class="col-md-4">
        <div class="form-group">
            <div class="checkbox">
            <br/>
              <label>
                {!! Form::checkbox('selected_contacts', 1, false, 
                [ 'class' => 'input-icheck', 'id' => 'selected_contacts']); !!} {{ __( 'lang_v1.allow_selected_contacts' ) }}
              </label>
              @show_tooltip(__('lang_v1.allow_selected_contacts_tooltip'))
            </div>
        </div>
      </div>
      <div class="col-sm-4 hide selected_contacts_div">
          <div class="form-group">
              {!! Form::label('selected_contacts', __('lang_v1.selected_contacts') . ':') !!}
              <div class="form-group">
                  {!! Form::select('selected_contact_ids[]', $contacts, null, ['class' => 'form-control select2', 'id'=>'selected_contact_ids', 'multiple', 'style' => 'width: 100%;' ]); !!}
              </div>
          </div>
      </div>

    @endcomponent
  </div>

  </div>
    @include('user.edit_profile_form_part')

    @if(!empty($form_partials))
      @foreach($form_partials as $partial)
        {!! $partial !!}
      @endforeach
    @endif

    @component("components.widget",["title"=>__("lang_v1.mobile_app"),"class"=>"box-primary"])
    <div class="col-md-4">
      <div class="form-group">
        
          <div class="form-group">
            {!! Form::label('user_account_id', __( 'lang_v1.cash' ) . ': ') !!}
              {!! Form::select('user_account_id', $accounts,null, ['class' => 'form-control select2',  "id"=>"user_account_id", 'placeholder' => __( 'messages.please_select' ) ]); !!}
          </div>
          
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        
          <div class="form-group">
            {!! Form::label('user_visa_account_id', __( 'lang_v1.visa_account' ) . ': ') !!}
              {!! Form::select('user_visa_account_id', $accounts,null, ['class' => 'form-control select2',  "id"=>"user_visa_account_id", 'placeholder' => __( 'messages.please_select' ) ]); !!}
          </div>
          
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        
          <div class="form-group">
            {!! Form::label('user_agent_id', __( 'home.Agent' ) . ': ') !!}
              {!! Form::select('user_agent_id', $agents,null, ['class' => 'form-control select2',  "id"=>"user_agent_id", 'placeholder' => __( 'messages.please_select' ) ]); !!}
          </div>
          
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        
          <div class="form-group">
            {!! Form::label('user_cost_center_id', __( 'home.Cost Center' ) . ': ') !!}
              {!! Form::select('user_cost_center_id', $cost_center,null, ['class' => 'form-control select2',  "id"=>"user_cost_center_id", 'placeholder' => __( 'messages.please_select' ) ]); !!}
          </div>
          
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        
          <div class="form-group">
            {!! Form::label('user_store_id', __( 'warehouse.nameW' ) . ': ') !!}
              {!! Form::select('user_store_id', $stores,null, ['class' => 'form-control select2',  "id"=>"user_store_id", 'placeholder' => __( 'messages.please_select' ) ]); !!}
          </div>
          
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        {!! Form::label('user_pattern_id', __( 'business.patterns' ) . ':') !!} 
        {!! Form::select('user_pattern_id', $patterns,  null, ['class' => 'form-control select2',"id"=>"user_pattern_id", 'placeholder' => __( 'messages.please_select' ), 'style' => 'width: 100%;']); !!}
      </div>
    </div>
    
    <div class="clearfix"></div>
@endcomponent

  <div class="row">
    <div class="col-md-12">
      <button type="submit"   class="btn btn-primary pull-right" id="submit_user_button">@lang( 'messages.save' )</button>
    </div>
  </div>
{!! Form::close() !!}
  @stop
@section('javascript')
<script type="text/javascript">
function register_user(){
        var surname  = $("#surname").val();
        var first_name = $("#first_name").val();
        var last_name = $("#last_name").val();
        var email = $("#email").val();
        var is_active = $("#is_active").val();
        var allow_login = $("#allow_login").val();
        var username = $("#username").val();
        var password = $("#password").val();
        var confirm_password = $("#confirm_password").val();
        var role = $("#role").val();
        var access_all_locations = $("#access_all_locations").val();
        var location_permissions = $("#location_permissions").val();
        var cmmsn_percent = $("#cmmsn_percent").val();
        var max_sales_discount_percent = $("#max_sales_discount_percent").val();
        var selected_contact_ids= $("#selected_contact_ids").val();
        
        axios.post('/users-store', {
          surname: surname,
          first_name: first_name,
          last_name: last_name,
          email : email ,
          is_active: is_active,
          allow_login: allow_login,
          username: username,
          password: password,
          confirm_password: confirm_password,
          role: role,
          access_all_locations: access_all_locations,
          location_permissions: location_permissions,
          cmmsn_percent: cmmsn_percent,
          max_sales_discount_percent: max_sales_discount_percent,
          selected_contact_ids: selected_contact_ids,
        }).then(response => {
          alert(response);
          localStorage.setItem('access_token', response.data.access_token);
        });
    }
  __page_leave_confirmation('#user_add_form');
  $(document).ready(function(){
    
    $('#selected_contacts').on('ifChecked', function(event){
      $('div.selected_contacts_div').removeClass('hide');
    });
    $('#selected_contacts').on('ifUnchecked', function(event){
      $('div.selected_contacts_div').addClass('hide');
    });

    $('#allow_login').on('ifChecked', function(event){
      $('div.user_auth_fields').removeClass('hide');
    });
    $('#allow_login').on('ifUnchecked', function(event){
      $('div.user_auth_fields').addClass('hide');
    });
  });


  






  


  $('form#user_add_form').validate({
                rules: {
                    first_name: {
                        required: true,
                    },
                    email: {
                        email: true,
                        remote: {
                            url: "/business/register/check-email",
                            type: "post",
                            data: {
                                email: function() {
                                    return $( "#email" ).val();
                                }
                            }
                        }
                    },
                    password: {
                        required: true,
                        minlength: 5
                    },
                    confirm_password: {
                        equalTo: "#password"
                    },
                    username: {
                        minlength: 3,
                        remote: {
                            url: "/business/register/check-username",
                            type: "post",
                            data: {
                                username: function() {
                                    return $( "#username" ).val();
                                },
                                @if(!empty($username_ext))
                                  username_ext: "{{$username_ext}}"
                                @endif
                            }
                        }
                    }
                },
                messages: {
                    password: {
                        minlength: 'Password should be minimum 3 characters',
                    },
                    confirm_password: {
                        equalTo: 'Should be same as password'
                    },
                    username: {
                        remote: 'Invalid username or User already exist'
                    },
                    email: {
                        remote: '{{ __("validation.unique", ["attribute" => __("business.email")]) }}'
                    }
                }
            });
  $('#username').change( function(){
    if($('#show_username').length > 0){
      if($(this).val().trim() != ''){
        $('#show_username').html("{{__('lang_v1.your_username_will_be')}}: <b>" + $(this).val() + "{{$username_ext}}</b>");
      } else {
        $('#show_username').html('');
      }
    }
  });
</script>
@endsection
