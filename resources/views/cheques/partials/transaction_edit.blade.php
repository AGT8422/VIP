<style>
    .bill_uls{padding: 15px 0;}
    .bill_uls li{display: initial;background: #313131;padding: 15px 20px;color: #fff;font-size: 22px;cursor: pointer;}
    .bill_uls span{
         color: red;
    }
    .choose_ul span{
        color: red;
    }
    .view_span{
        position: absolute;
        left: 0;
        top: 0;
    }
    .choose_ul li{display: inline-block;padding: 10px 21px;background: #1572e8;margin: 12px;position: relative;font-size: 20px;color: #fff;}
    .close_item{position: absolute;color: red;font-size: 24px;top: -17px;right: -6px;cursor: pointer;z-index: 100;}
</style>
<div class="modal-dialog" role="document" style="width:90%">
    <div class="modal-content">
       
      
  
      <div class="container" style="max-width: 1440px;width: 100%;">
        
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
                <table id="example" class="table table-bordered table-striped display" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>@lang('home.Check')</th>
                            <th>@lang('messages.date')</th>
                            <th>@lang('purchase.ref_no')</th>
                            <th>@lang('purchase.supplier')</th>
                            <th>@lang('purchase.purchase_status')</th>
                            <th>@lang('purchase.payment_status')</th>
                            <th>@lang('warehouse.nameW')</th>
                            <th>@lang('purchase.grand_total')</th>
                            <th>@lang('purchase.payment_due') &nbsp;&nbsp;<i class="fa fa-info-circle text-info no-print" data-toggle="tooltip" data-placement="bottom" data-html="true" data-original-title="{{ __('messages.purchase_due_tooltip')}}" aria-hidden="true"></i></th>
                            <th>@lang('lang_v1.added_by')</th>
                        </tr>
                    </thead>
                    
                </table>
            </div>
          </div>
      </div>
    </div>
      
      <div class="container">
        <div class="row row-1">
            <div class="col-md-6">
                {{ trans('home.Total') }} : <span id="total"></span>
            </div>
            <div class="col-md-6">
                {{ trans('home.Remain') }} : <span id="remain"></span>
            </div>
        </div>
        <div class="row row-2" hidden>
            <div class="col-md-6">
                {{ trans('home.Total') }} : <span id="total_edit"></span>
            </div>
            <div class="col-md-6">
                {{ trans('home.Remain') }} : <span id="remain_edit"></span>
            </div>
        </div>
        
      </div>
        <div class="modal-footer">
         <ul class="choose_ul">
                <div class="change_supplier">
                    @foreach ($data->payments as $item)
                        <li>
                            <a href="#" data-href="/{{ ($item->type == 0)?"sells":"purchase" }}/{{$item->transaction_id}}"
                                class="btn-modal view_span" data-container=".view_modal">
                                <i class="fas fa-eye" aria-hidden="true"></i>
                            </a>
                            <input type="hidden" name="payment_id[]" value="{{ $item->id }}"  > 
                            <input type="hidden" name="old_bill_id[]" value="{{ $item->transaction_id }}" />
                            <input class="old_bill_id" hidden="" name="old_bill_amount[]" data-id="{{ $item->transaction_id }}"
                            value="{{ $item->amount }}" /> 
                            {{ ($item->transaction->type == 'sale')?$item->transaction->invoice_no:$item->transaction->ref_no }}/<span>
                            {{ $item->amount }}</span> <a class="close_item" onclick="removeThis(this,{{$item->transaction_id}})">x</a>
                        </li>
                    @endforeach
                </div>
           
         </ul>    
         <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
        <input class="contact_i" hidden  value="{{$data->contact_id}}" >
  </div>

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
@section('child_script')
<script>
        var start_du  =  0;
        function remain(i=null,contact=null) {
            var amount  = parseFloat($('input[name="amount"]').val());
            var total_due =  0;
            total_due -= start_du;
            $('.bill_id').each(function(){
                var due  = parseFloat($(this).val());
                total_due  -= due;
            })
            $('.old_bill_id').each(function(){
                var due  = parseFloat($(this).val());
                total_due -= due;
            })
            amount = amount + total_due;
            if (amount > 0) {
                $('#remain').text(amount); 
            }else{
                $('#remain').text(0);
            }
            if(i != null){
                change_supplier(contact)
            }
            return amount;
        }
        function add_item(id,due) {
            var re  =  remain();
            if ($('.add_item[value="'+id+'"]:checked').val() > 0) {
                if (!$('.bill_id[data-id="'+id+'"]').length && re > 0) {
                    var diff =  re - due;
                    if (diff > 0) {
                        var  a =  due;
                    }else{
                        var a =  re;
                    }
                    var text =  '<li><input type="hidden" name="bill_id[]" value="'+id+'"><input class="bill_id" hidden name="bill_amount[]" data-id="'+id+'" value="'+a+'" > V2222/<span>'+a+'</span> <a class="close_item"\
                    onClick="removeThis(this,'+id+')">x</a>  </li>'
                    $('.choose_ul').prepend(text);
                    
                }else{
                    $('.add_item[value="'+id+'"]').prop( "checked", false )
                }
            }else{
                console.log('yarab');
                $('.bill_id[data-id="'+id+'"]').parent().remove();
            }
            remain();
        }
        function remain2() {
            var amount = parseFloat($('input[name="amount"]').val());
            var total_due =  0;
             $('.bill_id').each(function(){
                var due  = parseFloat($(this).val());
                total_due -= due;
            })
            amount = amount + total_due;
            if (amount > 0) {
                $('#remain').text(amount); 
            }else{
                $('#remain').text(0);
            }
            return amount;
        }
        function removeThis(trash,id) {
            $('.add_item[value="'+id+'"]').prop( "checked", false )
            trash.closest('li').remove();
            remain()
        }
        function change_supplier(id) {
            var old_id     = $(".contact_i").val();
            var old_total  = $("#total").html();
            var old_remain = $("#remain").html();
         
            if(id != old_id){
                $(".change_supplier").addClass("hide");
                $(".row-2").attr("hidden",false);
                $("#remain_edit").html($("#total").html());
                $("#total_edit").html("");
                $(".row-1").attr("hidden",true);
            }else{
                $(".change_supplier").removeClass("hide");
                $(".row-1").attr("hidden",false);
                $("#total").html(old_total);
                $("#remain").html(old_remain);
                $(".row-2").attr("hidden",true);

            }
        }
</script>
@endsection
 