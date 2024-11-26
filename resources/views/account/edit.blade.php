<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('AccountController@update',$account->id), 'method' => 'PUT', 'id' => 'edit_payment_account_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'account.edit_account' )</h4>
    </div>

    <div class="modal-body">
            <div class="form-group">
                {!! Form::label('name', __( 'lang_v1.name' ) .":*") !!}
                {!! Form::text('name', $account->name, ['class' => 'form-control', 'required','placeholder' => __( 'lang_v1.name' ) ]); !!}
            </div>

             <div class="form-group">
                {!! Form::label('account_number', __( 'account.account_number' ) .":*") !!}
                {!! Form::text('account_number', $account->account_number, ['class' => 'form-control', "id"=>"ai", 'required','placeholder' => __( 'account.account_number' ) ]); !!}
            </div>

            <div class="form-group">
                {!! Form::label('account_type_id', __( 'account.account_type' ) .":") !!}
                <select name="account_type_id" class="form-control select2">
                    <option>@lang('messages.please_select')</option>
                    {{-- @foreach($account_types as $account_type)
                        <optgroup label="{{$account_type->name}}"> --}}
                            {{-- <option value="{{$account_type->id}}" @if($account->account_type_id == $account_type->id) selected @endif >{{$account_type->name}}</option> --}}
                            {{-- @foreach($account_type->sub_types as $sub_type)

                                  @php
 
                                  $type = 0;
                                  @endphp 
                                 
                        
                                  @foreach($array_of_type  as  $key => $value)
                                    @php
                                        // dd($array_of_type);
                                        //  Find position of the $from_char
                                        $from_pos = strpos( $value, "/" );
                                        $from_pos_ = strpos( $value, "&" );
                                        
                                    @endphp
                                      @if(substr( $value,0,$from_pos_ ) == $sub_type->id )
                                          @php
                                              $type = 1;
                                          @endphp                   
                                      @endif
                                  @endforeach
                                  
                                  @if($type == 1 )
                                  
                                    <optgroup label="&nbsp;&nbsp;{{$sub_type->name}}">  
                                  
                                  @else
                                      {{-- <option value="{{$sub_type->id}}">{{$sub_type->name}}</option> --}}
                                      {{--<option value="{{$sub_type->id}}" @if($account->account_type_id == $sub_type->id) selected @endif >{{$sub_type->name}}</option>
                                  @endif

                                  @foreach($array_of_type  as  $key => $value)
                                      @php
                                        // dd($array_of_type);
                                        //  Find position of the $from_char
                                        $from_pos = strpos( $value, "/" );
                                        $from_pos_ = strpos( $value, "&" );
                                        
                                    @endphp
                                    @if(substr( $value,0,$from_pos_ ) == $sub_type->id )
                                        <option value="{{$key}}" @if($account->account_type_id == $sub_type->id) selected @endif >{{substr( $value,$from_pos_+1, $from_pos-3 )}}</option>
                                    @endif
                                  @endforeach

                                  @if($type == 1 )
                                      </optgroup>                 
                                  @endif
                        
                        
                            @endforeach
                        
                        
                          </optgroup> --}}
                <select name="account_type_id"  id="account_type_id" class="form-control select2">
                    <option>@lang('messages.please_select')</option>
                     @foreach($list_a as $key_ => $value)
                            @php $accounts = \App\AccountType::where("parent_account_type_id",$key_)->first(); @endphp
                            @if($accounts)
                                <option value="{{ $key_ }}"  @if($account->account_type_id == $key_) selected @else disabled @endif  >{{$value}}</option>
                            @else
                              <option value="{{ $key_ }}" @if($account->account_type_id == $key_) selected @endif >&nbsp;&nbsp;  {{$value}}</option>
                            @endif
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                {!! Form::label('note', __( 'brand.note' )) !!}
                {!! Form::textarea('note', $account->note, ['class' => 'form-control', 'placeholder' => __( 'brand.note' ), 'rows' => 4]); !!}
            </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
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
                var count   = 0;
                var zero    = "" ;
                var counter = ""; 
                var prefix  = ''; 
                var inside  = 0;
                // console.log(result.array.length);
                for(i in result.array){
                    if(i <= result.array.length ){
                        var str = result.array[i];
                        if(str === "0"){
                            count++;
                            inside = 1;
                        }
                        if(str !== "0" && inside == 0){
                            prefix += str; 
                        }
                        if(inside == 1){
                            counter += str;
                        }
                    }
                }
                console.log(result);
                var e = 0;
                while(e < count){
                    zero  = zero + "0";
                    e++;
                }
                if(result.type == "account"){
                    if(result.parent == "contact"){
                        $("#ai").val(prefix + zero + (parseInt(counter)+1));
                    }else{
                        $("#ai").val(zero + (parseInt(result.array)+1));
                    }
                }else{
                    $("#ai").val(zero + parseInt(result.array)+1);
                }
                $("#ai").attr("readOnly",true);
                }
            });
        });
</script>
