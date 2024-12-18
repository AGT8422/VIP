
$(document).ready(function(){

   
    $(document).on('click', '.btn-modal', function(e) {
        e.preventDefault();
        var container = $(this).data('container');
        $.ajax({
            url: $(this).data('href'),
            dataType: 'html',
            success: function(result) {
                $(container)
                    .html(result)
                    .modal('show');
            },
        });
    });
    var product_id = $("#product_id").val();
    item_move  = $('#item_move').DataTable({
      
        processing: true,
        serverSide: true,
        ajax:{
            url:"/item-move/"+product_id ,
            data:function(d){
                d.product_id = $("#product_id").val();
                d = __datatable_ajax_callback(d);
            }
        },
        pageLength : -1,
        // aaSorting:[1,"desc"],
        columns:[
            {data:"product",name:"product"},
            {data:"account",name:"account"},
            {data:"state",name:"state"},
            {data:"references",name:"references"},
            {data:"qty_plus",name:"qty_plus"},
            {data:"qty_minus",name:"qty_minus"},
            {data:"store_id",name:"store_id"},
            {data:"row_price",name:"row_price" , class:"hi"},
            {data:"row_price_inc_exp",name:"row_price_inc_exp" , class:"hi"},
            {data:"unit_cost",name:"unit_cost",class:"hi"},
            {data:"current_qty",name:"current_qty"},
            {data:"created_at",name:"created_at"},
        ],
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#item_move'));
        },
         "footerCallback": function ( row, data, start, end, display ) {
            var total_plus  = 0;
            var total_minus = 0;
            for (var r in data){
                console.log(data[r].qty_minus);
                total_plus  += parseFloat(data[r].qty_plus) ;
                total_minus += parseFloat(data[r].qty_minus) ;
            }
            $('#footer_total').html(LANG.qty + " : " + (total_plus - total_minus));
            add_attribute();
            
        }

    });

    // function __currency_convert_recursively_EB(element, use_page_currency = true) {
    //     element.find('.display_currency').each(function() {
    //         var value = $(this).text();
    
    //         var show_symbol = $(this).data('currency_symbol');
    //         if (show_symbol == undefined || show_symbol != true) {
    //             show_symbol = false;
    //         }
    
    //         var highlight = $(this).data('highlight');
    //         if (highlight == true) {
    //             __highlight(value, $(this));
    //         }
    
    //         var is_quantity = $(this).data('is_quantity');
    //         if (is_quantity == undefined || is_quantity != true) {
    //             is_quantity = false;
    //         }
    
    //         if (is_quantity) {
    //             show_symbol = false;
    //         }
    
    //         $(this).text(__currency_trans_from_en(value, show_symbol, use_page_currency, __currency_precision, is_quantity));
    //     });
    // }
    // function __currency_trans_from_en_EB(
    //     input,
    //     show_symbol = true,
    //     use_page_currency = false,
    //     precision = __currency_precision,
    //     is_quantity = false
    // ) {
    //     console.log($(__p_currency_symbol).val());
    //     if (use_page_currency && __p_currency_symbol) {
    //         var s =$(__p_currency_symbol).val();
    //         var thousand = $(__p_currency_thousand_separator).val();
    //         var decimal =$( __p_currency_decimal_separator).val();
    //     } else {
    //         var s = $(__currency_symbol).val();
    //         var thousand = $(__currency_thousand_separator).val();
    //         var decimal = $(__currency_decimal_separator).val();
    //     }
    
    //     symbol = '';
    //     var format = '%s%v';
    //     if (show_symbol) {
    //         symbol = s;
    //         format = '%s %v';
    //         if (__currency_symbol_placement == 'after') {
    //             format = '%v %s';
    //         }
    //     }
    
    //     if (is_quantity) {
    //         precision = __quantity_precision;
    //     }
    
    //     return accounting.formatMoney(input, symbol, precision, thousand, decimal, format);
    // }

    function add_attribute(){
    
        var table = $('#item_move').DataTable();
       
        // Loop through each row in the table
        table.rows().every(function() {
            // Get the DOM element of the row
            var rowNode = this.node();
            // console.log($(rowNode));
             $(rowNode).find(".hi").addClass('display_currency');
             $(rowNode).find(".hi").attr('data-currency_symbol', true );
        
              
        });
    }
})