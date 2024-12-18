<div class="modal-dialog modal-xl no-print " role="document">
	<div class="modal-content dummy">
		<div class="modal-header">
			<button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			<h4 class="modal-title" id="modalTitle">@lang('home.Expenses Journal') (<b>@lang('home.Ref No').: {{ $data->ref_no }}</b> )</h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-xs-12">
					<p class="pull-right"><b>@lang('home.Date'):</b> {{ $data->date }} </p>
				</div>
			</div>
			<div class="row">
				
				<div class="col-sm-12">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ trans('home.Credit') }}</th>
                                <th>{{ trans('home.Debit') }}</th>
                                <th>{{ trans('home.Amount') }}</th>
                                <th>{{ trans('home.Tax Account') }}</th>
                                <th>{{ trans('home.Tax Percentage') }}</th>
                                <th>{{ trans('home.Tax Amount') }}</th>
                                <th>{{ trans('home.Net Amount') }}</th>
                                <th>{{ trans('home.Date') }}</th>
                                <th>{{ trans('home.Note') }}</th>
                              <th></th>
                            </tr>
                        </thead>
                        <tbody>
                          
                          @foreach ($data->items as $key=>$item)
                          <tr >
							<td class="col-xs-2">
								{{ $item->credit_account?$item->credit_account->name:' '  }}
							  </td>
							  <td class="col-xs-2">
                                {{ $item->debit_account?$item->debit_account->name:' '  }}
							  </td>
							  <td class="col-xs-1">
                                {{ $item->amount }}
							  </td>
							  <td class="col-xs-1">
                                {{ $item->tax_account?$item->tax_account->name:' '  }}
							  </td>
							  <td class="col-xs-1">
                                {{ $item->tax_percentage }}
							  </td>
							  <td class="col-xs-1">
                                {{ $item->tax_amount }}
							  </td>
							  <td class="col-xs-1">
                                {{  ($item->amount - $item->tax_amount) }}
							  </td>
							  <td class="col-xs-1">
                                {{ $item->date }}
							  </td>
							  <td class="col-xs-2">
								{{ $item->text }}
							  </td>
						  </tr>
                          @endforeach
                        </tbody>
                      </table>
                </div>
			</div>
			<br />
			
		</div>
		<div class="modal-footer">
            <button type="button" class="btn btn-primary no-print" aria-label="Print" onclick="$(this).closest('div.dummy').printThis();">
                <i class="fa fa-print"></i> Print      </button>
			<button type="button" class="btn btn-default no-print" data-dismiss="modal">Close</button>
		</div>
	</div>
</div>
