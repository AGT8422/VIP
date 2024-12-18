<div class="for_checker modal-dialog modal-xl" style="width:40%" role="document">
	<div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="modalTitle"> @lang('home.attention')   </h4>
        </div>
        <div class="modal-body">
                @php
                    if($type == "Add Sale"){
                        $type = "Invoice";
                    }elseif($type == "Add Quotation"){
                        $type = "Quotation";
                    }elseif($type == "Add Approved Quotation"){
                        $type = "Approve Quotation"; 
                    }elseif($type == "Add Draft"){
                        $type = "Draft"; 
                    }elseif($type == "Edit Sale"){
                        $type = "Invoice"; 
                    }elseif($type == "Edit Quotation"){
                        $type = "Quotation"; 
                    }elseif($type == "Edit Approve Quotation"){
                        $type = "Approve Quotation"; 
                    }elseif($type == "Edit Draft"){
                        $type = "Draft"; 
                    }else{
                        $type = ""; 
                    }
                @endphp
              <h4 class="modal-title" id="modalTitle"> @lang('home.attention_msg1')  {{ " " . $type . " " }} @lang('home.attention_msg2')<b>{!! ($pattern_name)?  $pattern_name->name  : " Not Selected !! " !!}</b></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            
         
             
        </div>
        </div>
        <div class="modal-footer">
            {{-- <button type="button" onClick="click_function();" class="btn btn-primary no-print">
                     @lang( 'messages.update' )
            </button> --}}
            @if($pattern_name == null) 
                <b style="color: red"> {{ "Please Select Pattern !!"  }} </b>
            @elseif($complete != "undefined") 
                <button type="button" style="background-color:#ee680e !important;border-color:#ee680e !important" onClick="click_function();" class="btn btn-primary no-print">
                    @lang( 'messages.sure' )  
                </button>   
            @else 
               <b style="color: red"> {{  "THERE IS NO ITEM SELETED"  }} </b>
            @endif
            
            <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' ) 
            </button>
     
        </div>

    </div>
</div>
<script type="text/javascript">
   
    function click_function(){
        window.onbeforeunload = null;
        form  = $("#add_sell_form");
        old   = $("#add_sell_form").attr("action");
        url   = $("#add_sell_form").attr("action");
        form.attr("action",url);

        
        form.submit();
        
        form2  = $("#edit_sell_form");
        old2   = $("#edit_sell_form").attr("action");
        url2   = $("#edit_sell_form").attr("action");
        form2.attr("action",url2);
        form2.submit();
         
    }
</script>