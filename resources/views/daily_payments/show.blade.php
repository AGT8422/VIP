<div class="modal-dialog modal-xl" role="document">
	<div class="modal-content">
      <div class="modal-header">
         <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
         <h4 class="modal-title" id="modalTitle">  <b> @lang('home.Ref No'):</b> #{{ $data->ref_no }}
         </h4>
     </div>
		<div class="modal-body">
         <div class="row">
            <div class="col-md-6">
               <h4>{{ trans('home.Amount') }} : {{ $data->amount }}</h4>
            </div>
            @if($data->image)
            <div class="col-md-6">
               <a href="{{ URL::to($data->image) }}" target="_blank">
                  <i class="fas fa-eye"></i>
                  <iframe src="{{ URL::to($data->image) }}" height="150" width="150" frameborder="0"></iframe>
               </a>
            </div>
            @endif
         </div>
         <div class="col-sm-12">
            <p class="pull-right"><b>@lang('home.Date'):</b> {{  date('d/m/Y',strtotime($data->created_at)) }}</p>
          </div>
          <div class="row invoice-info">
            @php $debit = 0; $credit = 0;@endphp
            @foreach ($data->items as $item)
               <div class="col-sm-6 invoice-col">
                  <h4>{{ trans('home.Account') }} : {{ $item->account->name }}</h4>
                  <h4>{{ trans('home.Credit') }} : {{ $item->credit }}</h4>
                  <h4>{{ trans('home.Debit') }} : {{ $item->debit }}</h4>
               </div> 
               @php $debit += $item->debit; $credit += $item->credit; @endphp
            @endforeach
         </div>
        </div>
        <div class="row">
         <div class="container">
            <span class="col-xd-4" style="font-size:20px;font-weight:bold">
                  @lang('Total Credit') : {{$credit}}
            </span>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <span class="col-xd-4" style="font-size:20px;font-weight:bold">
               @lang('Total Debit') : {{$debit}}
            </span>
         </div>
        <div class="btn-primary pull-right" style="text-algin:center;width:70px;border-radius:5px;padding:5px 10px;color:#fff;margin:10px 10px;"><a style=" color:#fff" href="{{ URL::to('daily-payment/edit/'.$data->id) }}"><i class="fas fa-edit"></i>@lang('home.Edit')</a></div>
        </div>

    </div>
</div>

       		 
      	 							 
	
        
