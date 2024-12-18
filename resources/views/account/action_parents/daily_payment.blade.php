<div class="modal-dialog modal-xl no-print " role="document">
	<div class="modal-content dummy">
		<div class="modal-header">
			<button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			<h4 class="modal-title" id="modalTitle">@lang('home.Journal Voucher') (<b>@lang('home.Ref No').: {{ $data->ref_no }}</b> )</h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-xs-12">
					<p class="pull-right"><b>@lang('home.Date'):</b> {{ $data->date }} </p>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<b>@lang('home.Ref No').:</b> {{ $data->ref_no }}<br />
					<b>@lang('home.Amount'):</b> {{ $data->amount }} <br />
				</div>
				<div class="col-sm-12">
                    <table class="table">
                        <thead>
                          <tr>
                            <th>{{ trans('home.Account') }}</th>
                            <th>{{ trans('home.Credit') }}</th>
                            <th>{{ trans('home.Debit') }}</th>
                            <th>{{ trans('home.Note') }}</th>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody>
                          @php $debit  = 0; $credit  = 0;  @endphp
                          @foreach ($data->items as $key=>$item)
                             <tr >
                                  <td class="col-xs-3">
                                    {{ ($item->account)?$item->account->name:'' }}
                                   </td>
                                  
                                  <td class="col-xs-3">
                                    @format_currency($item->credit)
                                  </td>
                                  <td class="col-xs-3">
                                    @format_currency($item->debit)
                                  </td>
                                  <td class="col-xs-3">
                                    {{ $item->text }}
                                  </td>
                            </tr>
                            @php $debit += $item->debit; $credit += $item->credit;  @endphp
                          @endforeach
                        </tbody>
                        <tfoot>
                          <tr >
                            <td class="col-xs-3"> </td>
                            <td class="col-xs-3">@format_currency($credit)</td>
                            <td class="col-xs-3">@format_currency($debit)</td>
                            <td class="col-xs-3"> </td>
                      </tr>
                        </tfoot>
                      </table>
                </div>
			</div>
			<br />
			
		</div>
		<div class="modal-footer">
                <div class="btn-primary pull-right" style="text-algin:center;width:70px;border-radius:5px;padding:5px 10px;color:#fff;margin:0px 10px;"><a style=" color:#fff" href="{{ URL::to('daily-payment/edit/'.$data->id) }}"><i class="fas fa-edit"></i>@lang('home.Edit')</a></div>
            <button type="button" class="btn btn-primary no-print" aria-label="Print" onclick="$(this).closest('div.dummy').printThis();">
                <i class="fa fa-print"></i> Print      </button>
			<button type="button" class="btn btn-default no-print" data-dismiss="modal">Close</button>
		</div>
	</div>
</div>
