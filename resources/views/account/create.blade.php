<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('AccountController@store'), 'method' => 'post', 'id' => 'payment_account_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'account.add_account' )</h4>
    </div>

    <div class="modal-body">
            <div class="form-group">
                {!! Form::label('name', __( 'lang_v1.name' ) .":*") !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'id'=>"name" , 'required','placeholder' => __( 'lang_v1.name' ) ]); !!}
            </div>
         

            @if(request()->input("type") != "" || request()->input("type") != null)
                @php
                    $account_parent = \App\AccountType::find(request()->input("type"));
                    $account_number = \App\Account::orderBy("account_number","desc")->whereHas("account_type",function($query){
                                                                 $query->where("id",request()->input("type"));
                                                    })->first();
             
                @endphp
                <div class="form-group">
                    {!! Form::label('account_number', __( 'account.account_number' ) .":*") !!}
                    {!! Form::text('account_number', ($account_number)?(intVal($account_number->account_number)+1):($account_parent->code."01"), ['class' => 'form-control',"id"=>"account_number", 'required',"readOnly",'placeholder' => __( 'account.account_number' ) ]); !!}
                </div>
            @else
                <div class="form-group">
                    {!! Form::label('account_number', __( 'account.account_number' ) .":*") !!}
                    {!! Form::text('account_number', null, ['class' => 'form-control', "id"=>"ai", 'required','placeholder' => __( 'account.account_number' ) ]); !!}
                </div>
            @endif
      
         
            @if(request()->input("type") != "" || request()->input("type") != null)
            <div class="form-group">
                {!! Form::label('account_type_id', __( 'account.account_type' ) .":") !!}
                {{-- <select name="account_type_id" id="account_type_id" class="form-control select2"> 
                    @foreach($$account_types as $account_type)
                        @foreach($account_type->sub_types as $sub_type) 
                            @if($sub_type->id == request()->input("type"))
                            <option value="{{request()->input("type")}}" selected="selected">{{$sub_type->name}}</option>
                            @endif
                        @endforeach
                    @endforeach
                </select> --}}
                <select name="account_type_id" id="account_type_id" class="form-control select2"> 
                    @foreach($account_types_all as $account_type)
                            @if($account_type->id == request()->input("type"))
                            <option value="{{request()->input("type")}}" selected="selected">{{$account_type->name}}</option>
                            @endif
                    @endforeach
                </select>
            </div>
            @else
            @php 
            // dd($account_types);
            @endphp
                <div class="form-group">
                    {!! Form::label('account_type_id', __( 'account.account_type' ) .":") !!}
                    @php  $array_i = [] ;@endphp
                    <select name="account_type_id" id="account_type_id" class="form-control select2">
                        <option>@lang('messages.please_select')</option>
                        {{-- @foreach($account_types as $account_type)
                        <optgroup label="{{$account_type->name}}"> --}}
                            {{-- <option value="{{$account_type->id}}">{{$account_type->name}}</option> --}}
                            {{-- @php  $array_i_sub = [] ;@endphp
                            @foreach($account_type->sub_types as $sub_type)
                                            @php $type = 0;   @endphp
                                            @foreach($array_of_type  as  $key => $value)
                                                @foreach($array_of_type_  as  $key_ => $value_)
                                                    @if($key == $key_ )
                                                        @if($value_ == $sub_type->id)
                                                            @php  $type = 1;  @endphp                   
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @endforeach
                                            @if($type == 1)
                                                     <optgroup label="&nbsp;&nbsp;{{$sub_type->name}}">  
                                                        @if(!in_array($sub_type->id,$array_i_sub))
                                                            @foreach($array_of_type  as  $key => $value)  
                                                                @foreach($array_of_type_  as  $key_ => $value_)
                                                                    @if($key == $key_ )
                                                                        @if($value_ == $sub_type->id )
                                                                                <option value="{{$array_of_type_id[$key_]}}">&nbsp;&nbsp;  {{$value}}</option>
                                                                                @php array_push($array_i,$account_type->id); @endphp 
                                                                                @php array_push($array_i_sub,$sub_type->id); @endphp 
                                                                        @endif
                                                                    @endif
                                                                @endforeach
                                                            @endforeach
                                                        @endif
                                                    </optgroup>   
                                            @else
                                                @if(!in_array($account_type->id,$array_i))
                                                    <option value="{{$sub_type->id}}">{{$sub_type->name}}</option>
                                                @endif
                                            @endif
                                @endforeach
                            </optgroup> --}}
                        @foreach($list_a as $key_ => $value)
                                @php $account = \App\AccountType::where("parent_account_type_id",$key_)->first(); @endphp
                                @if($account)
                                    <option value="{{ $key_ }}" disabled  >{{$value}}</option>
                                @else
                                  <option value="{{ $key_ }}">&nbsp;&nbsp;  {{$value}}</option>
                                @endif
                        @endforeach
                    </select>
                </div>
            @endif
            
          
            {{-- <div class="form-group">
                {!! Form::label('opening_balance', __( 'account.opening_balance' ) .":") !!}
                {!! Form::text('opening_balance', 0, ['class' => 'form-control input_number','placeholder' => __( 'account.opening_balance' ) ]); !!}
            </div> --}}

        
            <div class="form-group">
                {!! Form::label('note', __( 'brand.note' )) !!}
                {!! Form::textarea('note', null, ['class' => 'form-control', 'placeholder' => __( 'brand.note' ), 'rows' => 4]); !!}
            </div>
    </div>
 
    <div class="modal-footer">
      <button type="submit" id="sub_mit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>
    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script type="text/javascript">
        $(document).on("input","#name",function(){
          $("#name").css({"outline":"0px solid red","box-shadow":"1px 1px 10px transparent","color":"gray"})
          $("#sub_mit").attr("disabled",false);
        });

        $(document).on("change","#name",function(e){
          var name = $("#name").val();
          // $("#name_p").css({"outline":"0px solid red","box-shadow":"1px 1px 10px transparent"});
          $.ajax({
            method: 'GET',
            url: '/account/check-name/' + name,
            async: false,
            data: {
                name: name,
            },
            dataType: 'json',
            success: function(result) {
                $results = result.status;
                if($results == true){
                    toastr.error(LANG.product_name);
                    $("#name").css({"outline":"1px solid red","box-shadow":"1px 1px 10px red","color":"red"})
                    $("#sub_mit").attr("disabled",true);
                }else{
                    $("#sub_mit").attr("disabled",false);
                }
              }
            });
        });
        $(document).on("input","#ai",function(){
          $("#ai").css({"outline":"0px solid red","box-shadow":"1px 1px 10px transparent","color":"gray"})
          $("#sub_mit").attr("disabled",false);
        });

        $(document).on("change","#ai",function(e){
          var name = $("#ai").val();
          // $("#name_p").css({"outline":"0px solid red","box-shadow":"1px 1px 10px transparent"});
          $.ajax({
            method: 'GET',
            url: '/account/check-number/' + name,
            async: false,
            data: {
                name: name,
            },
            dataType: 'json',
            success: function(result) {
                $results = result.status;
                if($results == true){
                    toastr.error(LANG.account_number);
                    $("#ai").css({"outline":"1px solid red","box-shadow":"1px 1px 10px red","color":"red"})
                    $("#sub_mit").attr("disabled",true);
                }else{
                    $("#sub_mit").attr("disabled",false);
                }
              }
            });
        });
         $(document).on("change","#account_type_id",function(e){
          var id = $(this).find("option:selected");
          $.ajax({
            method: 'GET',
            url: '/account/get-num/' + id.val(),
            async: false,
            data: {
                name: name,
            },
            dataType: 'json',
            success: function(result) {
                var count = 0;
                var zero  = "" ;
                for(i in result.array){
                    if(i <= result.array.length ){
                        var str = result.array[i];
                 
                        if(str == "0" && i == 0){
                            count++
                        }
                    }
                }
                console.log(count);
                var e = 0;
                while(e < count){
                    zero  = zero + "0";
                    e++;
                }
                if(result.type == "account"){
                     
                    $("#ai").val(zero + (parseInt(result.array)+parseInt(1)));
                }else{ 
                    $("#ai").val(zero + (parseInt(result.array)+parseInt(1)));
                }
                $("#ai").attr("readOnly",true);
                }
            });
        });
</script>