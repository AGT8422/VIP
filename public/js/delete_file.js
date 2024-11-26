$(document).ready(function(){
    
          // * * *  & & * * * \\ 
     // * * * * * * * * * * * * * * \\ 
    // * * * * Delete Buttons * * * * \\ 
     // * * * * * * * * * * * * * * \\ 
        // * * * * * * * * * * * \\ 
        //     ^^^^\ | /^^^^     \\
        //       .**_|_**.        \\          

        //.01.//  ... reset numbers ;
        $('#reset_numbers').on('click', function(e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).data('href');
                    $.ajax({
                        method: 'GET',
                        url: href,
                        dataType: 'json',
                        success: function(result) {
                            if (result.success == 1) {
                                 toastr.success(result.msg);
                             } else {
                                 toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });



        //.02.//  ... delete customers and reset it count number to 0 ;
        $('#delete_customers').on('click', function(e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).data('href');
                    $.ajax({
                        method: 'GET',
                        url: href,
                        dataType: 'json',
                        success: function(result) {
                            if (result.success == 1) {
                                 toastr.success(result.msg);
                             } else {
                                 toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });
        //.03.//  ... delete suppliers and reset it count number to 0  ;
        $('#delete_suppliers').on('click', function(e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).data('href');
                    $.ajax({
                        method: 'GET',
                        url: href,
                        dataType: 'json',
                        success: function(result) {
                            if (result.success == 1) {
                                 toastr.success(result.msg);
                             } else {
                                 toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });
        //.04.//  ... delete items and every thing depending on it ;
        $('#delete_items').on('click', function(e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).data('href');
                    $.ajax({
                        method: 'GET',
                        url: href,
                        dataType: 'json',
                        success: function(result) {
                            if (result.success == 1) {
                                 toastr.success(result.msg);
                             } else {
                                 toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });
        //.05.//  ... delete all users except first user  ;
        $('#delete_users').on('click', function(e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).data('href');
                    $.ajax({
                        method: 'GET',
                        url: href,
                        dataType: 'json',
                        success: function(result) {
                            if (result.success == 1) {
                                 toastr.success(result.msg);
                             } else {
                                 toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });
        //.06.//  ... delete all acccount and every action on it ;
        $('#delete_accounts').on('click', function(e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).data('href');
                    $.ajax({
                        method: 'GET',
                        url: href,
                        dataType: 'json',
                        success: function(result) {
                            if (result.success == 1) {
                                 toastr.success(result.msg);
                             } else {
                                 toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });
        //.07.//  ... delete all purchase && all previous received ;
        $('#delete_purchases').on('click', function(e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).data('href');
                    $.ajax({
                        method: 'GET',
                        url: href,
                        dataType: 'json',
                        success: function(result) {
                            if (result.success == 1) {
                                 toastr.success(result.msg);
                             } else {
                                 toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });
        //.08.//  ... delete all sells && all previous delivered;
        $('#delete_sells').on('click', function(e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).data('href');
                    $.ajax({
                        method: 'GET',
                        url: href,
                        dataType: 'json',
                        success: function(result) {
                            if (result.success == 1) {
                                 toastr.success(result.msg);
                             } else {
                                 toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });
        //.09.//  ... reset program ** every thing in system without business ;
        $('#delete_all').on('click',function(e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).data('href');
                    $.ajax({
                        method: 'GET',
                        url: href,
                        dataType: 'json',
                        success: function(result) {
                            if (result.success == 1) {
                                 toastr.success(result.msg);
                             } else {
                                 toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });
        //.10.//  ...  delete all payments ;
        $('#delete_payments').on('click',function(e) {
            e.preventDefault();
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).data('href');
                    $.ajax({
                        method: 'GET',
                        url: href,
                        dataType: 'json',
                        success: function(result) {
                            if (result.success == 1) {
                                 toastr.success(result.msg);
                             } else {
                                 toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });

})