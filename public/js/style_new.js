$(document).ready(function() {
     $('.treeview').on('click',function(){
        // alert($(this).find('a').find('.pull-right-container').find('.fa-angle-left').html() == null);
            if($(this).find('a').find('.fa-angle-left').html() != null){
                var e = $(this).find('a').find('.fa-angle-left');
                e.addClass('fa-angle-down');
                e.removeClass('fa-angle-left');
                $(this).find('a').find('fa-angle-left').removeClass('fa-angle-left');
                $(this).find('.treeview-menu').css({"display":"block"}); 
                $(this).css({"background-color":"blue !important"}); 
            }else{
                var e = $(this).find('a').find('.fa-angle-down');
                e.addClass('fa-angle-left');
                e.removeClass('fa-angle-down');
                $(this).find('.treeview-menu').css({"display":"none"}); 
                $(this).css({"background-color":"red !important"}); 
                  
        }
     });
});