 @if(count($module_permissions) > 0)
  @php
    $module_role_permissions = [];
    if(!empty($role_permissions)) {
      $module_role_permissions = $role_permissions;
    }
  @endphp
  @foreach($module_permissions as $key => $value)

  <div class="row check_group">
    <div class="col-md-2">
      <h4>@lang(strtolower($key).'::lang.'.strtolower($key))</h4>
    </div>
    <div class="col-md-2">
      <label class="ch_all">  
        <input type="checkbox" class="check_all input-icheck" > {{ __( 'role.select_all' ) }}
      </label>
    </div>
    <div class="col-md-8">
      @foreach($value as $module_permission)
      @php
        if(empty($role_permissions) && $module_permission['default']) {
          $module_role_permissions[] = $module_permission['value'];
        }
      @endphp
      <div class="col-md-4">
        <div class="checkbox">
          <label>
            {!! Form::checkbox('permissions[]', $module_permission['value'], in_array($module_permission['value'], $module_role_permissions), 
            [ 'class' => 'input-icheck']); !!} {{ $module_permission['label'] }}
          </label>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endforeach
@endif