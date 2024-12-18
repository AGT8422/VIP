<div class="for_attach modal-dialog modal-xl" style="width:40%" role="document">
	<div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="modalTitle"> @lang('Attachments')   </h4>
        </div>
        <div class="modal-body">
                @php 
                    $type = request()->input('type');
                    $id   = request()->input('id');
                @endphp
              <h4 class="modal-title" id="modalTitle"> @lang('messages.add')  {{ " Attachment " }}   :</h4>
        </div>
        <form action="{{\URL::to('/save-attachment')}}" method="POST" id="save_attachment" enctype="multipart/form-data" >
            @csrf
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">&nbsp;</div>
                    <div class="col-md-4">
                        <input type="hidden"  value="{{$type}}" name="type_of_attachment">
                        <input type="hidden"  value="{{$id}}" name="source_id">
                        @php
                        $accept =  implode(',', array_keys(config('constants.document_upload_mimes_types')));
                        @endphp
                        <input type="file" required  name="attach[]" multiple id="attachment_file" accept="{{$accept}}">
                        <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)]) <br></small>
                    </div>
                    <div class="col-md-4">&nbsp;</div>
                </div>
            </div>
            <div class="modal-footer"> 
                
                <button type="submit"  class="btn btn-primary no-print">
                    @lang( 'messages.save' )  
                </button>   
                
                <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' ) 
                </button>
                
            </div>
        </form>

    </div>
</div>
<script type="text/javascript">
   
    // function click_function(){
    //     window.onbeforeunload = null;
    //     form  = $("#add_sell_form");
    //     old   = $("#add_sell_form").attr("action");
    //     url   = $("#add_sell_form").attr("action");
    //     form.attr("action",url);

    //     form.submit();
        
    //     form2  = $("#edit_sell_form");
    //     old2   = $("#edit_sell_form").attr("action");
    //     url2   = $("#edit_sell_form").attr("action");
    //     form2.attr("action",url2);

    //     form2.submit();
         
    // }
</script>