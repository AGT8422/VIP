@extends('layouts.app')

@section('title', __( 'user.edit_user' ))
@section('special_css')
@php
  $left_shift             = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? '20px' : 'initial';
  $right_shift            = in_array(session()->get('lang', config('app.locale')), config('constants.langs_rtl')) ? 'initial' : '20px';
@endphp
<style>
    .pas {
      position: relative;
    }
    .toggle-password {
        position: absolute;
        top: 40px;
        left: {{$left_shift}};
        right: {{$right_shift}};
        z-index:10000;
        transform: translateY(-50%);
        cursor: pointer;
    }

    /* Optional: Style for the eye icon */
    .eye-icon::before {
        content: '\1F441'; /* Unicode character for an eye symbol */
        font-size: 1.5em;
    }
</style>
@endsection
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="font_text">@lang( 'user.edit_user' )</h1>
    @php $mainUrl = '/users';  @endphp  
    <h5 class="font_text"><i><b class="font_text"><a class="font_text" href="{{\URL::to($mainUrl)}}">{{ __('izo.user_management') }} {{__("izo.>") . " "}}</a></b>{{ __("user.edit_user")   }} <b> {{"   "}} </b></i></h5>
  
</section>

<!-- Main content -->
<section class="content font_text">
    {!! Form::open(['url' => action('ManageUserController@update', [$user->id]), 'method' => 'PUT', 'id' => 'user_edit_form' ]) !!}
    <div class="row">
        <div class="col-md-12">
          @component('components.widget', ['title' => __('lang_v1.roles_and_permissions')])
              <div class="col-md-12"> 
                <div class="col-md-4">
                    <div class="form-group">
                      <div class="checkbox">
                        <label>
                          {!! Form::checkbox('allow_login', 1, !empty($user->allow_login), 
                          [ 'class' => 'input-icheck', 'id' => 'allow_login']); !!} {{ __( 'lang_v1.allow_login' ) }}
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <div class="checkbox">
                        <br>
                        <label>
                              {!! Form::checkbox('is_active', $user->status, $is_checked_checkbox, ['class' => 'input-icheck status']); !!} {{ __('lang_v1.status_for_user') }}
                        </label>
                        @show_tooltip(__('lang_v1.tooltip_enable_user_active'))
                      </div>
                    </div>
                  </div>
              </div>
              <div class="clearfix"></div>
              <div class="col-md-3">
                <div class="form-group">
                  {!! Form::label('surname', __( 'business.prefix' ) . ':*') !!}
                    {!! Form::text('surname', $user->surname, ['class' => 'form-control', 'required', 'placeholder' => __( 'business.prefix_placeholder' ) ]); !!}
                </div>
              </div>
              <div class="col-md-3">
                  <div class="form-group">
                    {!! Form::label('first_name', __( 'business.first_name' ) . ':*') !!}
                      {!! Form::text('first_name', $user->first_name, ['class' => 'form-control', 'required', 'placeholder' => __( 'business.first_name' ) ]); !!}
                  </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  {!! Form::label('last_name', __( 'business.last_name' ) . ':') !!}
                  {!! Form::text('last_name', $user->last_name, ['class' => 'form-control', 'placeholder' => __( 'business.last_name' ) ]); !!}
                </div>
              </div>
              <div class="form-group col-md-3">
                {!! Form::label('contact_number', __( 'lang_v1.mobile_number' ) . ': *') !!}
                @php
                $old = !empty($user->contact_number) ? $user->contact_number : null
                @endphp
                <input type="text" hidden name="old_number" id="old_number" value="{{$old }}">
                {!! Form::text('contact_number', !empty($user->contact_number) ? $user->contact_number : null, ['class' => 'form-control font_number','required', 'placeholder' => __( 'lang_v1.mobile_number') ]); !!}
              </div>
              <div class="col-md-3 hide">
                  <div class="form-group">
                    {!! Form::label('email', __( 'business.email' ) . ': ') !!}
                      {!! Form::text('email', $user->email, ['class' => 'form-control', "id" => "email",  'placeholder' => __( 'business.email' ) ]); !!}
                  </div>
              </div>
              <div class="col-md-3"> 
                  <div class="form-group">
                    {!! Form::label('user_account_id', __( 'lang_v1.account' ) . ': ') !!}
                    {!! Form::select('user_account_id', $accounts,$user->cash_account_id, ['class' => 'form-control select2',  "id"=>"user_account_id", 'placeholder' => __( 'messages.please_select' ) ]); !!}
                  </div> 
              </div>
              <div class="col-md-3">
                  <div class="form-group">
                    {!! Form::label('role', __( 'user.role' ) . ':*') !!} @show_tooltip(__('lang_v1.admin_role_location_permission_help'))
                      {!! Form::select('role', $roles, !empty($user->roles->first()->id) ? $user->roles->first()->id : null, ['class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
                  </div>
              </div>
              
              <div class="user_auth_fields @if(empty($user->allow_login)) hide @endif">
                @if(empty($user->allow_login))
                    <div class="col-md-4">
                        <div class="form-group">
                          {!! Form::label('username', __( 'business.username' ) . ':') !!}
                          @if(!empty($username_ext))
                            <div class="input-group">
                              {!! Form::text('username', null, ['class' => 'form-control', 'placeholder' => __( 'business.username' ) ]); !!}
                              <span class="input-group-addon">{{$username_ext}}</span>
                            </div>
                            <p class="help-block" id="show_username"></p>
                          @else
                              {!! Form::text('username', null, ['class' => 'form-control', 'placeholder' => __( 'business.username' ) ]); !!}
                          @endif
                          <p class="help-block">@lang('lang_v1.username_help')</p>
                        </div>
                    </div>
                @endif
                <div class="col-md-3">
                    <div class="form-group pas">
                      {!! Form::label('password', __( 'business.password' ) . ':') !!}
                        {!! Form::password('password', ['class' => 'form-control', 'placeholder' => __( 'business.password'),'id'=>'password', 'required' => empty($user->allow_login) ? true : false ]); !!}
                        <p class="help-block">@lang('user.leave_password_blank')</p>
                        <span class="toggle-password">
                          <i class="eye-icon" id="togglePassword"></i>
                      </span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                      {!! Form::label('confirm_password', __( 'business.confirm_password' ) . ':') !!}
                        {!! Form::password('confirm_password', ['class' => 'form-control', 'placeholder' => __( 'business.confirm_password' ),'id'=>'confirm_password', 'required' => empty($user->allow_login) ? true : false ]); !!}
                      
                    </div>
                </div>
              </div>
              <div class="clearfix"></div>
              <div class="col-md-3 hide">
                  <h4>@lang( 'role.access_locations' ) @show_tooltip(__('tooltip.access_locations_permission'))</h4>
              </div>
              <div class="col-md-9 hide">
                  <div class="col-md-12">
                      <div class="checkbox">
                          <label>
                            {!! Form::checkbox('access_all_locations', 'access_all_locations', !is_array($permitted_locations) && $permitted_locations == 'all', 
                          [ 'class' => 'input-icheck']); !!} {{ __( 'role.all_locations' ) }} 
                          </label>
                          @show_tooltip(__('tooltip.all_location_permission'))
                      </div>
                    </div>
                @foreach($locations as $location)
                  <div class="col-md-12">
                      <div class="checkbox">
                        <label>
                          {!! Form::checkbox('location_permissions[]', 'location.' . $location->id, is_array($permitted_locations) && in_array($location->id, $permitted_locations), 
                          [ 'class' => 'input-icheck']); !!} {{ $location->name }}
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
                    {!! Form::text('cmmsn_percent', !empty($user->cmmsn_percent) ? @num_format($user->cmmsn_percent) : 0, ['class' => 'form-control input_number font_number', 'placeholder' => __( 'lang_v1.cmmsn_percent' )]); !!}
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                  {!! Form::label('max_sales_discount_percent', __( 'lang_v1.max_sales_discount_percent' ) . ':') !!} @show_tooltip(__('lang_v1.max_sales_discount_percent_help'))
                    {!! Form::text('max_sales_discount_percent', !is_null($user->max_sales_discount_percent) ? @num_format($user->max_sales_discount_percent) : null, ['class' => 'form-control input_number font_number', 'placeholder' => __( 'lang_v1.max_sales_discount_percent' ) ]); !!}
                </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                {!! Form::label('pattern_id', __( 'business.patterns' ) . ':') !!} 
                @if($list_patterns !=null) 
                <select name="pattern_id[]" required class="form-control select2" multiple>
                  @php $array = []; @endphp
                  @foreach($patterns as $id => $name)
                    @foreach($list_patterns as $it)
                          @php  $check = 0; @endphp
                          @if($id == $it)
                            @if(!in_array($id,$array))
                                <option value="{{ $id }}" selected="selected">{{ $name }}</option>
                                @php $check = 1; array_push($array,$id);  @endphp
                            @endif
                          @endif
                    @endforeach
                    @if($check == 0)
                        @if(!in_array($id,$array))
                          <option value="{{ $id }}">{{ $name }}</option>
                          @php array_push($array,$id); @endphp
                        @endif
                    @endif
                  @endforeach
                </select>
                @else
                  {!! Form::select('pattern_id[]', $patterns,  null, ['class' => 'form-control select2','multiple', 'style' => 'width: 100%;']); !!}
                @endif
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-4">
                <div class="form-group">
                    <div class="checkbox">
                    <br/>
                      <label>
                        {!! Form::checkbox('selected_contacts', 1, 
                        $user->selected_contacts, 
                        [ 'class' => 'input-icheck', 'id' => 'selected_contacts']); !!} {{ __( 'lang_v1.allow_selected_contacts' ) }}
                      </label>
                      @show_tooltip(__('lang_v1.allow_selected_contacts_tooltip'))
                    </div>
                </div>
            </div>
            
            <div class="col-sm-4 selected_contacts_div @if(!$user->selected_contacts) hide @endif">
                <div class="form-group">
                  {!! Form::label('selected_contacts', __('lang_v1.selected_contacts') . ':') !!}
                    <div class="form-group">
                      {!! Form::select('selected_contact_ids[]', $contacts, $contact_access, ['class' => 'form-control select2', 'multiple', 'style' => 'width: 100%;' ]); !!}
                    </div>
                </div>
            </div>
            

            <div class="clearfix"></div>
             
            @endcomponent
        </div>
    </div>
    @include('user.edit_profile_form_part', ['bank_details' => !empty($user->bank_details) ? json_decode($user->bank_details, true) : null])

    @if(!empty($form_partials))
      @foreach($form_partials as $partial)
        {!! $partial !!}
      @endforeach
    @endif
    <div class="col-md-12">
        @component("components.widget",["title"=>__("lang_v1.mobile_app"),"class"=>"box-primary"])
          <div class="col-md-4">
            <div class="form-group">
              
                <div class="form-group">
                  {!! Form::label('user_account_id', __( 'lang_v1.cash' ) . ': ') !!}
                    {!! Form::select('user_account_id', $accounts,$user->user_account_id, ['class' => 'form-control select2',  "id"=>"user_account_id", 'placeholder' => __( 'messages.please_select' ) ]); !!}
                </div>
                
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              
                <div class="form-group">
                  {!! Form::label('user_visa_account_id', __( 'lang_v1.visa_account' ) . ': ') !!}
                    {!! Form::select('user_visa_account_id', $accounts,$user->user_visa_account_id, ['class' => 'form-control select2',  "id"=>"user_visa_account_id", 'placeholder' => __( 'messages.please_select' ) ]); !!}
                </div>
                
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              
                <div class="form-group">
                  {!! Form::label('user_agent_id', __( 'home.Agent' ) . ': ') !!}
                    {!! Form::select('user_agent_id', $agents,$user->user_agent_id, ['class' => 'form-control select2',  "id"=>"user_agent_id", 'placeholder' => __( 'messages.please_select' ) ]); !!}
                </div>
                
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              
                <div class="form-group">
                  {!! Form::label('user_cost_center_id', __( 'home.Cost Center' ) . ': ') !!}
                    {!! Form::select('user_cost_center_id', $cost_center,$user->user_cost_center_id, ['class' => 'form-control select2',  "id"=>"user_cost_center_id", 'placeholder' => __( 'messages.please_select' ) ]); !!}
                </div>
                
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              
                <div class="form-group">
                  {!! Form::label('user_store_id', __( 'warehouse.nameW' ) . ': ') !!}
                    {!! Form::select('user_store_id', $stores,$user->user_store_id, ['class' => 'form-control select2',  "id"=>"user_store_id", 'placeholder' => __( 'messages.please_select' ) ]); !!}
                </div>
                
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('user_pattern_id', __( 'business.patterns' ) . ':') !!} 
              {!! Form::select('user_pattern_id', $patterns,  $user->user_pattern_id, ['class' => 'form-control select2', 'placeholder' => __( 'messages.please_select' ),"id"=>"user_pattern_id", 'style' => 'width: 100%;']); !!}
            </div>
          </div>
            <div class="col-md-4">
              <div class="form-group">
                {!! Form::label('tax_id', __( 'Vat') . ':') !!} 
                {!! Form::select('tax_id',$taxes,  $user->tax_id, ['class' => 'form-control select2',"id"=>"tax_id", 'placeholder' => __( 'messages.please_select' ), 'style' => 'width: 100%;']); !!}
              </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                  {!! Form::label('include', __( 'Product Price' ) . ':') !!} 
                  {!! Form::select('include',["0"=>"Exclude Tax","1"=>"Include Tax"],  $user->include, ['class' => 'form-control select2',"id"=>"include", 'placeholder' => __( 'messages.please_select' ), 'style' => 'width: 100%;']); !!}
                </div>
            </div>
            <div class="clearfix"></div>
        @endcomponent
    </div>

    <div class="row">
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary pull-right" id="submit_user_button">@lang( 'messages.update' )</button>
        </div>
    </div>
    {!! Form::close() !!}
  @stop
@section('javascript')
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function () {
          const togglePassword = document.querySelector('#togglePassword');
          const password = document.querySelector('#password');
          const confirm_password = document.querySelector('#confirm_password');

          togglePassword.addEventListener('click', function () {
              // Toggle the type attribute using getAttribute() and setAttribute()
              const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
              const confirm_type = confirm_password.getAttribute('type') === 'password' ? 'text' : 'password';
              password.setAttribute('type', type);
              confirm_password.setAttribute('type', confirm_type);

              // Toggle the eye icon
              this.classList.toggle('eye-icon--active');
          });
        });
  $(document).ready(function(){
    __page_leave_confirmation('#user_edit_form');
    $('#username').on('input', function(){
      $("#email").val($(this).val());
    });
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
  $.validator.addMethod("noPlusFirst", function(value, element) {
            return this.optional(element) || value.charAt(0) === '+';
        }, "Invalid Mobile Number Should Start with (+)");
        
  $('form#user_edit_form').validate({
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
                                },
                                user_id: {{$user->id}}
                            }
                        }
                    },
                    contact_number: {
                        noPlusFirst: true,
                        required: true,
                        minlength: 6,
                        remote: {
                            url: "/business/register/check-mobile?edit=1",
                            type: "post",
                            data: {
                              contact_number: function() {
                                    return $( "#contact_number" ).val();
                              },
                              old_number: function() {
                                    return $( "#old_number" ).val();
                              },
                              edit:1
                            }
                        }
                    },
                    password: {
                       
                      minlength: 6,
                      remote: {
                          url: "/business/register/check-password",
                          type: "post",
                          data: {
                              password: function() {
                                  return $( "#password" ).val();
                              },
                          }
                      }
                    },
                    confirm_password: {
                        equalTo: "#password",
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
                  contact_number: {
                        minlength: 'Should be at least 6 digits',
                        remote: 'Mobile Number Already Exist',
                        noPlusFirst:'Invalid Mobile Number Should Start with (+)',
                    },
                    password: {
                        minlength: '{!!__("izo.desc_password")!!}',
                        remote: '{!!__("izo.desc_password")!!}',
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
</script>
@endsection