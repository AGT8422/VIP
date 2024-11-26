<div class="modal-dialog modal-xl" style="width:40%" role="document">
	<div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="modalTitle"> @lang('home.change_supplier')  </h4>
        </div>
        <div class="modal-body">
              <h4 class="modal-title" id="modalTitle"> @lang('home.do_you_transfer') </h4>
        </div>
        <div class="modal-body">
          <div class="row">
            
         
             
        </div>
        </div>
        <div class="modal-footer">
            {{-- <button type="button" onClick="click_function();" class="btn btn-primary no-print">
                     @lang( 'messages.update' )
            </button> --}}
            <button type="button" style="background-color:#ee680e !important;border-color:#ee680e !important" onClick="click_function(1);" class="btn btn-primary no-print">
                     @lang( 'messages.yes' )
            </button>
            <button type="button" style="background-color:#414748 !important;border-color:#414748 !important" onClick="click_function(0);" class="btn btn-primary no-print">
                     @lang( 'messages.no' ) {{" & "}} @lang( 'messages.update' ) 
            </button>
            <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' ) 
            </button>
     
        </div>

    </div>
</div>
<script type="text/javascript">
    function click_function(check){
        form  = $("#add_purchase_form");
        old   = $("#add_purchase_form").attr("action");
        url   = $("#add_purchase_form").attr("action")+"?dialog=1&check="+check;
        form.attr("action",url);
        form.submit();
       }
</script>