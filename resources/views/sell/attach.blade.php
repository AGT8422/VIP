<div class="modal-dialog modal-xl" role="document" style="width:50%">
	<div class="modal-content">
		<div class="modal-header">
		    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		      <h4 class="modal-title" id="modalTitle">@lang("home.attachment")</h4>
	    </div>
	    <div class="modal-body text-center">
          @if(!empty($data) )
             @if( $data->document != null )
              @foreach($data->document as $doc)
                  <span>
                    <?php $ar =  explode('.',$doc)  ?>
                    {{-- <a onclick="$(this).parent().remove()" class="close_item">X</a> --}}
                    @if ($ar[1]  != 'pdf')
                      <a href="{{ URL::to($doc) }}" target="_blank">
                      <img src="{{ URL::to($doc) }}" class="img-thumbnail"> 
                      </a>
                    @endif
                    <input type="hidden" name="old_document[]" value="{{ $doc }}">
                  </span>
              
              @endforeach
              <br>
              @foreach($data->document as $doc)
                  <span>
                    <?php $ar =  explode('.',$doc)  ?>
                    {{-- <a onclick="$(this).parent().remove()" class="close_item">X</a> --}}
                    @if ($ar[1]  == 'pdf')
                      <a href="{{ URL::to($doc) }}" target="_blank">
                        <i class="fa fa-eye show_eye"></i>
                      </a>
                      <iframe  src="{{ URL::to($doc) }}" frameborder="0" width="100" height="100"></iframe>
                    @endif
                    <input type="hidden" name="old_document[]" value="{{ $doc }}">
                    </span>
              
              @endforeach
            @endif
          @endif
      </div>
        
  
        <div class="modal-footer">
            <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  </div>
</div>
