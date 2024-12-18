<div class="modal-dialog modal-xl no-print " role="document">
	<div class="modal-content dummy">
		<div class="modal-header">
			<button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			<h4 class="modal-title" id="modalTitle">@lang('home.Cheque') (<b>@lang('home.Ref No').: {{ $data->ref_no }}</b> )</h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-xs-12">
					<p class="pull-right"><b>@lang('home.Date'):</b> {{ date('Y-m-d h:i a',strtotime($data->created_at)) }} </p>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<b>@lang('home.Ref No').:</b> {{ $data->ref_no }}<br />
					<b>@lang('home.Amount'):</b> {{ $data->amount }} <br />
					@php 
						if($data->account_type == 0){
							$account = \App\Account::where("contact_id",$data->contact_id)->first();
							if($account){
							$name = $account->name; 
							}else{
								$name = "--"; 
							}
						}else{
							$account = \App\Account::where("id",$data->contact_id)->first();
							if($account){
							$name = $account->name; 
							}else{
							$name = "--"; 
							}
							
						}
					@endphp
					<b>@lang('home.Contact'):</b>{{ $name }}<br />
					
					<b>@lang('home.Type'):</b>{{ isset($types[$data->type])?$types[$data->type]:'' }}<br />
				</div>
				<div class="col-sm-4">
					
					<b>@lang('home.Write Date'):</b> {{ $data->write_date }} <br />
					<b>@lang('home.Due Date'):</b> {{ $data->due_date }} <br />
					<b>@lang('home.Bank'):</b> {{ $data->account?$data->account->name:'--' }} <br />
					<b>@lang('home.Collecting Account'):</b> {{ $data->collecting_account?$data->collecting_account->name:'--' }} <br />
				</div>
			</div>
			<br/>
			
		</div>
		<div class="modal-footer">
            <button type="button" class="btn btn-primary no-print" aria-label="Print" onclick="$(this).closest('div.dummy').printThis();">
                <i class="fa fa-print"></i> Print      </button>
			<button type="button" class="btn btn-default no-print" data-dismiss="modal">Close</button>
		</div>
	</div>
</div>
