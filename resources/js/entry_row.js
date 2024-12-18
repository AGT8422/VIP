
$(document).ready(function(){

    var type = "percent";
    var discount = 0 ;
    //....................... unit price before dis count
    $(".purchase_unit_cost_without_discount").each(function(){
        var el = $(this);
        // alert($(".qty_" + el.data("number")).val());
        el.on("change",function(){
          
            var qty  = $(".qty_" + el.data("number")).val();
      
             $(".purchase_unit_cost_without_discount_inc_" + el.data("number")).val((( parseFloat(el.val())) * parseFloat($(".tax_row").val()) / 100 ) + parseFloat(el.val() ));
            //  alert($(".dis_" + el.data("number")).val());
             if(type == "percent"){
                if($(".dis_" + el.data("number")).val() == 0 || $(".dis_" + el.data("number")).val() == ""){
                     discount = 0;
                }else{
                    discount = (el.val() * $(".dis_" + el.data("number")).val() ) / 100;
                }
                $(".purchase_unit_cost_" + el.data("number")).val( parseFloat(el.val()) - discount );
                $(".purchase_unit_cost_inc_" + el.data("number")).val( ( ((parseFloat(el.val())) * parseFloat($(".tax_row").val()) / 100 ) + (parseFloat(el.val())) ) - discount );
            }else if(type == "fixed"){
                if($(".dis_" + el.data("number")).val() == 0 || $(".dis_" + el.data("number")).val() == ""){
                    discount = 0;
               }else{
                   discount =  $(".dis_" + el.data("number")).val() ;
               }
               $(".purchase_unit_cost_" + el.data("number")).val( parseFloat(el.val()) - discount );
               $(".purchase_unit_cost_inc_" + el.data("number")).val( (( (parseFloat( parseFloat(el.val()) - discount ) ) * parseFloat($(".tax_row").val()) / 100  ) + ( parseFloat(parseFloat(el.val()) - discount ) ))  );
            }
          
            var before_exc = $(".purchase_unit_cost_inc_" + el.data("number")).val() ;
            $(".total_inc_" + el.data("number")).html(  (before_exc * qty ));
            tot_();
 
        });
    });
    
    //....................... unit price before dis count inc vat   
    $(".purchase_pos_inc").each(function(){
        var el = $(this);
   
        el.on("change",function(){
            var qty  = $(".qty_" + el.data("number")).val();

            $(".purchase_pos_" + el.data("number")).val((el.val() * 100) / 105);
            if(type == "percent"){
                if($(".dis_" + el.data("number")).val() == 0 || $(".dis_" + el.data("number")).val() == ""){
                     discount = 0;
                }else{
                    discount = (((el.val() * 100) / 105 ) * $(".dis_" + el.data("number")).val() ) / 100;
                }
                $(".purchase_unit_cost_" + el.data("number")).val( ( (el.val() * 100) / 105 ) - discount );
                $(".purchase_unit_cost_inc_" + el.data("number")).val(  ( el.val() - discount )  );
               
            }else if(type == "fixed"){
                if($(".dis_" + el.data("number")).val() == 0 || $(".dis_" + el.data("number")).val() == ""){
                    discount = 0;
               }else{
                   discount = $(".dis_" + el.data("number")).val() ;
               }
               $(".purchase_unit_cost_" + el.data("number")).val(((el.val() * 100) / 105) - discount);
               $(".purchase_unit_cost_inc_" + el.data("number")).val( ( (((el.val() * 100) / 105) - discount) * parseFloat($(".tax_row").val()) / 100  ) + (((el.val() * 100) / 105) - discount) );
              
            }
          
            var before_exc = $(".purchase_unit_cost_inc_" + el.data("number")).val() ;
            $(".total_inc_" + el.data("number")).html(  (before_exc * qty ));
            tot_();
        });

        
    });

    //.......................   Quantity      
    $(".purchase_quantity").each(function(){
        var el = $(this);
        
        el.on("change",function(){

            var before_no = $(".purchase_pos_" + el.data("number")).val();
            var before_exc = $(".purchase_unit_cost_without_discount_inc_" + el.data("number")).val() ;
            if(type == "percent"){
                if($(".dis_" + el.data("number")).val() == 0 || $(".dis_" + el.data("number")).val() == ""){
                     discount = 0;
                }else{
                    discount = ( before_no * $(".dis_" + el.data("number")).val() ) / 100;
                }
                $(".purchase_unit_cost_" + el.data("number")).val( before_no -  discount );
                $(".purchase_unit_cost_inc_" + el.data("number")).val( (  before_exc - discount) );
               
            }else if(type == "fixed"){
                if($(".dis_" + el.data("number")).val() == 0 || $(".dis_" + el.data("number")).val() == ""){
                    discount = 0;
               }else{
                   discount = $(".dis_" + el.data("number")).val() ;
               }
               $(".purchase_unit_cost_" + el.data("number")).val( before_no - discount );
                $(".purchase_unit_cost_inc_" + el.data("number")).val( ((  before_no - discount ) * parseFloat($(".tax_row").val()) / 100  ) + (before_no - discount) );
             
            }
            var after_exc = $(".purchase_unit_cost_inc_" + el.data("number")).val() ;
            $(".total_inc_" + el.data("number")).html(  (after_exc * el.val())   );

            tot_();
        });

    });

    //.......................   Discount   %    
    
    $("#Discount").each(function(){
        var el = $(this);
        el.on("click",function(){

            var before_no = $(".purchase_pos_" + el.data("number")).val();
            var before_exc = $(".purchase_unit_cost_without_discount_inc_" + el.data("number")).val() ;

            type = "percent";

            if($(".dis_" + el.data("number")).val() == 0 || $(".dis_" + el.data("number")).val() == ""){
                discount = 0;
            }else{
                discount = ( before_no * $(".dis_" + el.data("number")).val() ) / 100;
            }
            $(".purchase_unit_cost_" + el.data("number")).val( before_no -  discount );
            $(".purchase_unit_cost_inc_" + el.data("number")).val( (  before_exc - discount) );
            

             var after_exc = $(".purchase_unit_cost_inc_" + el.data("number")).val() ;
             $(".total_inc_" + el.data("number")).html(  (after_exc * el.val())   );
             tot_();
        });
        
    });
    
    //.......................   Discount FIXED 
       
    $("#Discount1").each(function(){
        var el = $(this);
        el.on("click",function(){
           
            var before_no = $(".purchase_pos_" + el.data("number")).val();
            var before_exc = $(".purchase_unit_cost_without_discount_inc_" + el.data("number")).val() ;
           
            type = "fixed";

            if($(".dis_" + el.data("number")).val() == 0 || $(".dis_" + el.data("number")).val() == ""){
                discount = 0;
            }else{
                discount = $(".dis_" + el.data("number")).val();
            }

            $(".purchase_unit_cost_" + el.data("number")).val( before_no -  discount );
            $(".purchase_unit_cost_inc_" + el.data("number")).val( (  before_exc - discount) );
            

            var after_exc = $(".purchase_unit_cost_inc_" + el.data("number")).val() ;
            $(".total_inc_" + el.data("number")).html(  (after_exc * el.val())   );
            tot_();
        });

    });
    // ./................................... for sum total 
    function tot_(){
        var total_ = 0 ;
        var QTY_ = 0 ;
        $(".final").each(function(){
            var el = $(this);
            total_ =  total_ +  parseFloat(el.html());
        });
        $(".purchase_quantity").each(function(){
            var el = $(this);
            QTY_ =  QTY_ +  parseFloat(el.val());
        });
        $(".purchase_table #total_final_1").html( total_ );
        $(".purchase_table #tot_qty_final_1").html( QTY_ );
    }
 

    
});
