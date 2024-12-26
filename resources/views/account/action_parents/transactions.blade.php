<div class="modal-dialog modal-xl font_text" role="document">
	<div class="modal-content">
      <div class="modal-header">
         <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
         {{-- <h4 class="modal-title" id="modalTitle">  <b> @lang('home.ref_no_first'):</b>#{{ ($data->entries->first()!= null)?$data->entries->first()->refe_no_e:"" }} 
         </h4> --}}
         <h4 class="modal-title" id="modalTitle"  >  <b> @lang('home.ref_no_second'): 
         
         @if($data->type == "purchase")
            <button type="button" class="btn btn-link btn-modal font_number"
                  data-href="{{action('PurchaseController@show', [$data->id])}}" data-container=".view_modal"> 
                  # {{ $data->ref_no }}
               </button>
         @elseif($data->type == "sale")
         
            <button type="button" class="btn btn-link btn-modal font_number"
                     data-href="{{action('SellController@show', [$data->id]) }}" data-container=".view_modal"> 
                     # {{ $data->invoice_no }}
                  </button>

         @endif</b>
               </h4>
     </div>
		<div class="modal-body">
               <div class="row">
                  <div class="col-md-12">
                    @foreach($all as  $it)
                    @php
                        //.. Entry Number
                        $entry   = \App\Models\Entry::find($it);  
                        //... TOTAL DEBIT  
                        $debit   = 0;
                        //... TOTAL CREDIT
                        $credit  = 0;
                        //... check shipping
                        $check   = 0;
                    @endphp
                     <table class="table table-bordered table-striped dataTable no-footer" id="account_book" role="grid" aria-describedby="account_book_info" style="width: 1536px;">
                        <thead   >
                           <tr >
                              <th class="font_number" style="color:black !important ;background-color:#e0e0e0 !important;font-family:arial !important"   colspan="5">{{$entry->refe_no_e}}</th>
                           </tr>
                           <tr>
                              <th>@lang("account.account_name")</th>
                              <th>@lang("lang_v1.date")</th>
                              <th>@lang("account.credit")</th>
                              <th>@lang("account.debit")</th>
                              <th>@lang("home.Cost Center")</th>
                           </tr>
                        </thead>
                        
                        @if(isset($item[$it]))
                           @foreach($item[$it] as $ie)
                              @php  
                                    $accountT    = \App\AccountTransaction::find($ie);
                                    $name        = \App\Account::find($accountT->account_id);
                                    if($name){
                                       if($name->cost_center == 0){
                                          if($accountT->type == "debit"){
                                             //... TOTAL DEBIT  
                                             $debit  += $accountT->amount;
                                          }else{
                                             //... TOTAL CREDIT
                                             $credit += $accountT->amount;
                                          }
                                       }
                                    }
                              @endphp
                              @if($name)
                              @if($name->cost_center == 0)
                               @if($accountT->amount > 0)
                                 <tbody>
                                    <tr>
                                       <td>
                                          <a href="{{route("account.show",[$name->id])}}" >
                                             {{$name->name}}
                                          </a>
                                       </td>
                                       @php
                                          $dt =  $accountT->operation_date->format('Y-m-d');
                                          $formats = [
                                             'Y-m-d',       // 2024-12-25
                                             'd/m/Y',       // 25/12/2024
                                             'm/d/Y',       // 12/25/2024
                                             'd-m-Y',       // 25-12-2024
                                             'Y/m/d',       // 2024/12/25
                                             'Y-m-d H:i:s', // 2024-12-25 14:30:00
                                             'd-m-Y H:i:s', // 25-12-2024 14:30:00    
                                          ];
                                          $D_format = "ops";
                                          foreach ($formats as $format) {
                                             try {
                                                
                                                   $date = \Carbon::createFromFormat($format, $dt);
                                                   // Check if the parsed date matches the input date string
                                                   if ($date && $date->format($format) === $dt) {
                                                      $D_format = $format;
                                                   }
                                             } catch (\Exception $e) {
                                                   // Continue to the next format
                                             }
                                          } 
                                       @endphp 
                                    
                                       <td class="font_number">{{\Carbon::createFromFormat($D_format,$accountT->operation_date->format('Y-m-d'))->format(session()->get('business.date_format'))}}</td>
                                       {{-- <td class="font_number">{{$accountT->operation_date}}</td> --}}
                                       @if($accountT->type == "debit")
                                       <td></td>
                                       <td class="font_number">{{number_format($accountT->amount,config('constants.currency_precision'))}}</td>
                                       @elseif($accountT->type == "credit")
                                       <td class="font_number">{{number_format($accountT->amount,config('constants.currency_precision'))}}</td>
                                       <td></td>
                                       @endif
                                          
                                       @foreach($costs as $i)
                                          @if( $accountT->account_id == $setting->purchase || $accountT->account_id == $setting->sale )
                                          @if($i->note == "Add Purchase" || $i->note == "Add Sale" || $i->note == "Return purchase" || $i->note == "Return sales"  )
                                                <td>
                                                   <a  style="   "  href="{{route("account.show",[$i->account->id])}}" >{{ $i->account->name }}  : ({{ $i->note }})    </a><br>
                                                </td>
                                             @endif
                                          @elseif(( $accountT->account_id == $setting->purchase_discount )  || $accountT->account_id == $setting->sale_discount  )
                                             @if($i->note == "Add Discount" || $i->note == "Sales Discount"  || $i->note == "Return Discount"  )
                                                <td>
                                                   <a  style="  "  href="{{route("account.show",[$i->account->id])}}" >{{ $i->account->name }}  : ({{ $i->note }})    </a><br>
                                                </td>
                                             @endif
                                          
                                          @endif
                                       @endforeach
                                        @php 
                                          $expenses    =  \App\Account::main('Expenses');
                                          $array       = [];
                                          foreach ($expenses as $key => $value) {
                                             $array[]=$key;
                                          }
                                       @endphp 
                                       @if($shipItem)
                                          @foreach($shipItem as $items)
                                                @if($items->vat == 0)
                                                   @if($accountT->additional_shipping_item_id == $items->id  )
                                                      @if(in_array($accountT->account_id,$array))
                                                            <td>
                                                               <a  style="  "  href="{{route("account.show",[$items->cost_center->id])}}" >{{ $items->cost_center->name }}  : ({{ $items->cost_center->note }})    </a><br>
                                                            </td>
                                                      @endif
                                                   @endif
                                                @else
                                                 @if($accountT->additional_shipping_item_id == $items->id  )
                                                      @if(in_array($accountT->account_id,$array))
                                                               <td>
                                                                  <a  style="  "  href="{{route("account.show",[$items->cost_center->id])}}" >{{ $items->cost_center->name }}  : ({{ $items->cost_center->note }})    </a><br>
                                                               </td>
                                                      @endif
                                                   @endif 
                                                @endif
                                          @endforeach
                                       @endif
                                       
                                    </tr>
                                 </tbody>
                              @endif
                              @endif
                              @endif
                           @endforeach
                        @endif
                        <tfoot>
                           
                           <tr>
                              <td> </td>
                              <td> </td>
                              <td style="font-family:arial !important">{{number_format($credit,config('constants.currency_precision'))}}</td>
                              <td style="font-family:arial !important">{{number_format($debit,config('constants.currency_precision'))}}</td>
                              <td> </td>
                            </tr>
                        </tfoot>
                     </table>
                     @endforeach
                  </div>
               </div>
            
               <div class="col-sm-12">
               </div>
               <div class="row invoice-info">
                  
               </div>
             

      </div>
        
        
    </div>

    <div class="modal fade view_model" tabindex="-1" role="dialog" 
            aria-labelledby="gridSystemModalLabel">
      </div>
</div>

<script src="{{ asset('js/functions.js?v=' . $asset_v) }}"></script>


       		 
      	 							 
	
        
