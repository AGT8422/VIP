 
<div class="for_checker modal-dialog modal-xl" style="width:40%" role="document">
	<div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modalTitle">  <b>{!! " Change Disc For : " . $product->sku . " in line " . $row . " " !!}</b> </h4>
        </div>
        <div class="modal-body">
                 
              
              <input type="hidden" name="product_id"      id="product_id_desc" value="{{$product->id}}">
              <input type="hidden" name="row_pro"         id="product_id_row" value="{{$row}}">
              <input type="hidden" name="product_return"  id="product_return" value="{{$return??null}}">
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-12 ">
                    <div class="form-group">
                      {!! Form::textarea('product_description', $text, ['class' => 'form-control product_descriptions hide','id'=>'product_description']); !!}
                    </div>
                  </div>
             
            </div>
        </div>
        <div class="modal-footer">
            <button type="button"   class="btn btn-primary no-print update_desc">
                     @lang( 'messages.update' )
            </button>
            <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' ) 
            </button>
     
        </div>

    </div>
</div>
  
{{-- <script src="https://cdn.ckeditor.com/ckeditor5/38.0.1/classic/ckeditor.js"></script> --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/froala-editor/js/froala_editor.pkgd.min.js"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-lite.min.js"></script>

<script> 


$(document).ready(function() {
    /*..1..*/ 
    // $('.product_descriptions').each(()=>{
    //     var e = $(this);
    // });
    // setTimeout(() => { 
    //     new FroalaEditor('.product_descriptions');
    // },100);
    $('.update_desc').on('click',function(){
        console.log("########## hi ##########");
        product_id_desc  = $('#product_id_desc').val();
        product_id_row   = $('#product_id_row').val();
        text             = $('#product_description').val();
        vaReturn         = $('#product_return').val(); 

        console.log("## description ## " + product_id_desc);
        console.log("## row_index ## " + product_id_row);
        console.log("## text ## " + text);
        console.log("## vaReturn ## " + vaReturn);
        check   = 0 ;
        $('.products_details').each(function() {
            if( check   == 0){

                var e  = $(this); 
                parent = e.parent(); 
                
                console.log("## element ## " + e.html());
                console.log("## parent element ## " + parent.html());
                console.log("## parent element line ## " + parent.attr('data-line'));
                console.log("## parent element row ## " + product_id_row);
                console.log("## compare element ## " + (parent.attr('data-line') == product_id_row));
                if(parent.attr('data-line') == product_id_row){
                    check = 1;
                    html = '<pre style="white-space: nowrap;max-width:300px;max-height:150px" data-line="'+product_id_row+'" class="btn btn-modal products_details" data-href="" data-container=".view_modal">'+ text +'</pre>';
                    parent.html(html);
                    setTimeout(() => { 
                        $.ajax({
                            type: 'GET',
                            url: "/product/description-url",
                            dataType:"json",
                            data:{
                                text:text,
                            },
                            success: function(result) {
                                if(result.success == true){
                                    console.log("## success ## " + result.success);
                                    text_text = result.text;
                                    console.log("## text success ## " + result.text);
                                    if(vaReturn != ''){
                                        url      = "https://"+window.location.hostname+"/product/change-description/"+product_id_desc+"?text="+text_text+"&line="+product_id_row+"&return=return";
                                        console.log("## url 1 ## " + url);
                                        parent.find('pre').attr('data-href',url);
                                        console.log("## textarea 1 ## " + parent.find('pre').html());
                                    }else{
                                        url      = "https://"+window.location.hostname+"/product/change-description/"+product_id_desc+"?text="+text_text+"&line="+product_id_row;
                                        console.log("## url 2 ## " + url);
                                        console.log("## textarea 2 ## " + parent.find('.products_details').html());
                                        parent.find('.products_details').attr('data-href',url);
                                    }
                                    
                                }
                            }
                        });
                    }, 1000);
                }
            }
        });
        $('.control_products_details').each(function()  {
            var e = $(this); 
            console.log("#3#" + e.attr('data-line') + "__" + product_id_row);
            if(e.attr('data-line') == product_id_row){ 
                e.html($('#product_description').val());
            }
        });
        console.log("########## Good Evening ##########");
    });
   
    /*..2..*/
    // ClassicEditor
    //   .create(document.querySelector('#product_description'))
    //   .catch(error => {
    //     console.error(error);
    //   });

    /*..3..*/ 
    setTimeout(() => { 
        $('#product_description').summernote({
        height: 300, // Set the height of the editor
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
        }); 
    },1000);
    

  });
</script> 