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
                    }elseif($type == "Add Purchase"){
                        $type = "Add Purchase"; 
                    }elseif($type == "Edit Purchase"){
                        $type = "Edit Purchase"; 
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
                <button type="button" style="background-color:#ee680e !important;border-color:#ee680e !important" onClick="click_function();" class="save_submit_button btn btn-primary no-print">
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
        form   = $("#add_sell_form");
        old    = $("#add_sell_form").attr("action");
        url    = $("#add_sell_form").attr("action");
        form.attr("action",url);

        $(".save_submit_button").attr('disabled', true);
        
        form.submit();
        
        form2  = $("#edit_sell_form");
        old2   = $("#edit_sell_form").attr("action");
        url2   = $("#edit_sell_form").attr("action");
        form2.attr("action",url2);
        form2.submit();
 
        form3  = $("#add_purchase_form");
        old3   = $("#add_purchase_form").attr("action");
        url3   = $("#add_purchase_form").attr("action");
        form3.attr("action",url3);
        $('form#add_purchase_form').validate({
            rules: {
                ref_no: {
                    remote: {
                        url: '/purchases/check_ref_number',
                        type: 'post',
                        data: {
                            ref_no: function() {
                                return $('#ref_no').val();
                            },
                            contact_id: function() {
                                return $('#supplier_id').val();
                            },
                            purchase_id: function() {
                                if ($('#purchase_id').length > 0) {
                                    return $('#purchase_id').val();
                                } else {
                                    return '';
                                }
                            },
                        },
                    },
                },
            },
            messages: {
                ref_no: {
                    remote: LANG.ref_no_already_exists,
                },
            },
        });
        if ($('form#add_purchase_form').valid()) {
            form3.submit();
        }

    }
</script>