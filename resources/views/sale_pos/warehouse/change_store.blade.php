<div class="modal-dialog modal-xl no-print" style="width: 50%" role="document">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title" id="modalTitle"> 
            @lang('Change Warehouse')  
      </h4>
    </div>
    <div class="modal-body">
        <select class="form-control" name="warehouse_id" id="warehouse_id">
            @foreach($stores as $k => $one)
                <option value="{{$k}}" @if($k == $store) selected @endif >{{$one}}</option>
            @endforeach
        </select>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-primary no-print" data-transaction="{{$id}}" data-store="{{$store}}" data-new_store="{{$store}}"  id="change_store_btn" data-dismiss="modal">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>
</div>