<div class="modal-dialog modal-xl" role="document">
	<div class="modal-content" style="width:50%;margin:auto 50%;transform:translateX(-50%)">
      <div class="modal-header">
         <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
         <h4 class="modal-title" id="modalTitle">  <b> @lang('Choose Type of Print'):</b>  
         </h4>
     </div>
		<div class="modal-body">
         <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    @php 
                          
                          $types_s  = ($item->contact->type == "customer")?0:1    
                    @endphp
                    @foreach($patterns as $pattern)
                        @if($types_s == 0)
                            <a href="{{ URL::to('reports/r-vh/'.$item->id.'?patten_id='.$pattern->id) }}" target="_blank"><i class="fas fa-print"></i> @lang('messages.print') {{ "By " . $pattern->name . " Pattern" }}</a>
                            <br>
                            <br>
                        @else
                            <a href="{{ URL::to('reports/p-vh/'.$item->id.'?patten_id='.$pattern->id) }}" target="_blank"><i class="fas fa-print"></i> @lang('messages.print') {{ "By " . $pattern->name . " Pattern" }}</a>
                            <br>
                            <br>
                        @endif 
                    @endforeach
                </div>
            </div>
             
         </div>
        </div>
    </div>
</div>
 
       		 
      	 							 
	
        
