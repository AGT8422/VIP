<div class="modal-dialog modal-xl" role="document">
	<div class="modal-content" style="width:50%;margin:auto 50%;transform:translateX(-50%)">
      <div class="modal-header">
         <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
         <h4 class="modal-title" id="modalTitle">  <b> @lang('Send This Voucher To Contact'):</b>  
         </h4>
     </div>
		<div class="modal-body">
         <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    @php 
                            if($contact != null){
                                if($contact->mobile != null){
                                    if($contact->mobile != ""){
                                        $mobile =   $contact->mobile   ; 
                                    }else{
                                        $mobile =   "Empty Number Field" ;         
                                    }
                                }else{
                                    $mobile =   "Empty Number Field" ;         
                                }
                            }else{
                                $mobile =   "Empty Number Field" ;         
                            }
                    @endphp
                    <label> Contact Number : {{ $mobile }} </label>
                    <select id="countryList" class="form-control" style="position-relative;max-width:20%;" name="country_id" required>    
                         @foreach ($countries as $country)
                            <option phonecode="{{ $country["id"] }}" 
                                    value="{{ $country["id"] }}" 
                                    id="shop-country">{{ $country["code"] }}
                           </option>
                         @endforeach
                    </select>
                    <input class="form-control"   style="width:80%;margin-left:20%;margin-top:-34px;" name="number_phone" id="number_phone" value="" placeholder="{{'your-number'}}" />
                </div>
            </div>
            <div class="col-sm-12">
                    <div class="form-group">
                     <span><div data-href="{{\URL::to('payment-voucher/whatsapp-save',$data->id)}}" class="btn btn-primary send-whatsapp pull-right">Send</div></span>
                   </div>
           </div>
         </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('div.send-whatsapp').on('click',  function(e) {
                    e.preventDefault()
              url   = $(this).data('href');
             code   = $("#countryList").val();
            mobile  = $("#number_phone").val();
            $.ajax({
                method: 'POST',
                url: url,
                data: {
                    code:   code,
                    mobile: mobile,
                     
                },
                dataType: 'html',
                success: function(result) {
                     
                },
            });
        });
</script>
       		 
      	 							 
	
        
