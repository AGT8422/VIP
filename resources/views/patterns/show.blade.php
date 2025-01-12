<div class="modal-dialog modal-xl" style="width:40%" role="document">
	<div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="modalTitle"> @lang('business.patterns_name') : ( {{ $pattern->name }} )</h4>
        </div>
        <div class="modal-body">
              <h4 class="modal-title" id="modalTitle"> @lang('business.business_location') :  " {{ $pattern->location->name }} "</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            
            <div class="col-sm-6">
                <strong> @lang('lang_v1.added_by') : </strong>    {{ $pattern->user->username }}  
            </div>
            <div class="col-sm-6">
                <strong> @lang('messages.date') : </strong>    {{ $pattern->created_at }}  
            </div>
            <div class="col-sm-6">
                <strong>@lang('invoice.invoice_scheme') :  </strong>  {{ $pattern->scheme->name }}  
            </div>
            <div class="col-sm-6">
                <strong>@lang('invoice.invoice_layout') :  </strong>  {{ $pattern->layout->name }} 
            </div>
            <div class="col-sm-6">
                <strong>@lang('business.pos') :  </strong>   {{ $pattern->pos }}  
            </div>
            <div class="col-sm-6">
                <strong>@lang('business.type') :  </strong>   {{ $pattern->type }}  
            </div>
            <div class="col-sm-6">
                <strong>@lang('business.printer_type') :  </strong>   {{ $pattern->type }}  
            </div>
            <div class="col-sm-6">
                <strong>@lang('business.printer_type') :  </strong>   {{ $pattern->printer->name_template }}  
            </div>
        </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary no-print" 
                    aria-label="Print" 
                    onclick="$(this).closest('div.modal').printThis();">
                    <i class="fa fa-print"></i> @lang( 'messages.print' )
            </button>
            <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )
            </button>
     
        </div>

    </div>
</div>