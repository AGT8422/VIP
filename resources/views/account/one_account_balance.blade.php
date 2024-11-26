<div class="modal-dialog" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.balance' )</h4>
    </div>
    <div class="modal-body">
             
      <div class="row">
        <div class="col-sm-1"></div>
        <div class="col-sm-10">
          <div class="form-group text-3xl" style="font-size: 20px">
           Account   @lang( 'lang_v1.balance' ) :  {{ ($balance)?(($balance <0)?abs($balance). " Debit":abs($balance). " Credit"):"" }}
          </div>
        </div>
        <div class="col-sm-1"></div>
      </div>
             
          
 
    </div>

    <div class="modal-footer"> 
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

 

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script type="text/javascript">
  $(document).ready( function(){
   
  });
</script>