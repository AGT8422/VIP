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
     bank_list  = $('#bank_list').DataTable({
      
        processing: true,
        serverSide: true,
        ajax:{
            url:"/account/bank" ,
            data:function(d){
                d.accounts = $("#accounts").val();
                d = __datatable_ajax_callback(d);
            }
        },
      
        columns:[
            {data:"number",name:"number" },
            {data:"name",name:"name" },
            {data:"debit",name:"debit",class:"hi"},
            {data:"credit",name:"credit",class:"hi" },
            {data:"status",name:"status" },
            {data:"balance",name:"row_price",class:"hi"},
            {data:"action",name:"action" },
        ],
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#bank_list'));
        },
         "footerCallback": function ( row, data, start, end, display ) {
            var total_debit  = 0;
            var total_credit = 0;
            var total_balance = 0;
            var i =  0;
            for (var r in data){
                i++;
               
                total_debit  += parseFloat(data[r].debit) ;
                total_credit += parseFloat(data[r].credit) ;
                total_balance += parseFloat(data[r].balance) ;

            }
            $("#footer_total_debit").html(__currency_trans_from_en(total_debit));
            $("#footer_total_credit").html(__currency_trans_from_en(total_credit));
            if(total_balance < 0){
                $("#footer_total_balance").html(__currency_trans_from_en(total_balance*-1) + " / Credit ");
            }else if(total_balance > 0){
                $("#footer_total_balance").html(__currency_trans_from_en(total_balance) + " / Debit ");
            }else if(total_balance == 0){
                $("#footer_total_balance").html(__currency_trans_from_en(total_balance));
            }
            $(".total").html(i);
            add_attribute();
              
         }
    });

    function add_attribute(){
    
        var table = $('#bank_list').DataTable();
        var cash_table = $('#cash_list').DataTable();
        // Loop through each row in the table
        table.rows().every(function() {
            // Get the DOM element of the row
            var rowNode = this.node();
            // console.log($(rowNode));
             $(rowNode).find(".hi").addClass('display_currency');
             $(rowNode).find(".hi").attr('data-currency_symbol', true );
        
              
        });
        cash_table.rows().every(function() {
            // Get the DOM element of the row
            var rowNode = this.node();
            // console.log($(rowNode));
             $(rowNode).find(".hi").addClass('display_currency');
             $(rowNode).find(".hi").attr('data-currency_symbol', true );
        
              
        });
    }



    cash_list  = $('#cash_list').DataTable({
      
        processing: true,
        serverSide: true,
        ajax:{
            url:"/account/cash" ,
            data:function(d){
                d.accounts = $("#accounts").val();
                d = __datatable_ajax_callback(d);
            }
        },
      
        columns:[
            {data:"number",name:"number" },
            {data:"name",name:"name" },
            {data:"debit",name:"debit",class:"hi"},
            {data:"credit",name:"credit",class:"hi"},
            {data:"status",name:"status"},
            {data:"balance",name:"row_price",class:"hi"},
            {data:"action",name:"action"  },
        ],
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#cash_list'));
        },
         "footerCallback": function ( row, data, start, end, display ) {
            var total_debit  = 0;
            var total_credit = 0;
            var total_balance = 0;
            var i =  0;
            for (var r in data){
                i++;
                total_debit  += parseFloat(data[r].debit) ;
                total_credit += parseFloat(data[r].credit) ;
                total_balance += parseFloat(data[r].balance) ;

            }
            $("#footer_total_debit").html(__currency_trans_from_en(total_debit));
            $("#footer_total_credit").html(__currency_trans_from_en(total_credit));
            if(total_balance < 0){
                $("#footer_total_balance").html(__currency_trans_from_en(total_balance) + " / Credit ");
            }else if(total_balance > 0){
                $("#footer_total_balance").html(__currency_trans_from_en(total_balance) + " / Debit ");
            }else if(total_balance == 0){
                $("#footer_total_balance").html(__currency_trans_from_en(total_balance));
            }
            $(".total").html(i);
            add_attribute();
         }

         

    });

    $(document).on("change","#accounts",function(){
        bank_list.ajax.reload();
        cash_list.ajax.reload();
    });


})