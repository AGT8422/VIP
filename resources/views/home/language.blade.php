<div class="row languages_change"  style="@if(session()->get('user.language', config('app.locale'))=='ar') display:none;padding:10px;box-shadow:1px 1px 10px black;position: absolute;top:60px;left:10px; border:2px solid rgba(0, 0, 0, 0);z-index:1000;width:400px   @else display:none;padding:10px;box-shadow:1px 1px 10px black;position: absolute;top:60px;right:10px; border:2px solid rgba(0, 0, 0, 0);z-index:1000;width:400px    @endif !important">
    <div class="form-group col-md-6"   >
       @if(isset($user))
        {!! Form::open(['url' => action('UserController@updateLanguage'), 'method' => 'post', 'id' => 'edit_password_form',
        'class' => 'form-horizontal' ]) !!}
            {!! Form::label('language', __('business.language') . ':') !!}
            <div class="input-group" >
                <span class="input-group-addon">
                    <i class="fa fa-info"></i>
                </span>
                
                {!! Form::select('language',(isset($languages))?$languages:[$user->language], $user->language, ['class' => 'form-control ' ,"style" => "position:relative;width:300px"]); !!}
            </div>
        
            <div class="col-md-6" @if(session()->get('user.language', config('app.locale'))=='ar') style="margin-left:45px;padding:10px;" @else style="margin-right:45px;padding:10px;" @endif>
                <button type="submit" class="btn btn-primary pull-right">@lang('messages.update')</button>
            </div>
  
        {!! Form::close() !!}
       @endif
    </div>
    </div>