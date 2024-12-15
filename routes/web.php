<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

include_once('install_r.php');
    Route::middleware(['setLanguage' ])->group(function () {
        Route::get('/forget-password','IzoUserController@forgetPassword')->name('izoForgetPassword');
        Route::get('/login-account','IzoUserController@loginPage')->name('izoLogin');
        Route::get('/login-account-redirect/{id}','IzoUserController@loginPageRedirect')->name('izoRedirectLogin');
        Route::get('/register-account','IzoUserController@register')->name('izoRegister');
        Route::get('/check-company','IzoUserController@checkCompanyName')->name('checkCompanyName');
        Route::get('/check-domain','IzoUserController@checkDomainName')->name('checkDomainName');
        Route::get('/check-email','IzoUserController@checkEmail')->name('checkEmail');
        Route::get('/check-mobile','IzoUserController@checkMobile')->name('checkMobile');
        Route::post('/save-account','IzoUserController@saveCompany')->name('izoSaveAccount');
        Route::post('/account/login','IzoUserController@login')->name('izoLoginAccount');
        Route::get('/account/logout','IzoUserController@logoutIzo')->name('izoLogout');
        Route::post('/create-company','IzoUserController@createCompany')->name('izoCompanyRegister');
        Route::middleware(['mainAuth' ])->group(function () {
            Route::get('/panel-account','IzoUserController@panel')->name('izoPanel');
        });
    });
    // 13 ***
        Route::get('/qrcode','GenQrcode@index');
        Route::post('/qrcode','GenQrcode@test');
        Route::get('/backup', function () {
            $exitCode = Artisan::call('backup:run --only-db');
            echo 'DONE'; //Return anything
        });
        Route::get('/clear-cache', function () {
            $exitCode = Artisan::call('cache:clear');
            $exitCode = Artisan::call('view:clear');
            $exitCode = Artisan::call('route:clear');
            $exitCode = Artisan::call('config:clear');
            echo 'DONE'; //Return anything
        });
    // 13 ***
     
    /*
    Route::get('addtodb',function (){
        DB::statement("ALTER TABLE transactions CHANGE COLUMN payment_status payment_status ENUM('paid','due','partial','installmented','new') NOT NULL DEFAULT 'paid'");
    });*/
    // Route::get('test-os',function(){
    //         $allData =  \App\Models\DailyPaymentItem::OrderBy('id','desc')->paginate(100);
    //         echo $allData->count();
    //         foreach ($allData as $item) {
    //             $tr =  \App\AccountTransaction::where('daily_payment_item_id',$item->id)->first();
    //             if ($item) {
    //                 if($item->daily_payment->date){
    //                      $it =   \App\AccountTransaction::where('daily_payment_item_id',$item->id)->update([
    //                     'operation_date' =>$item->daily_payment->date
    //                  ]);
    //                 }
    //             }
    //         }
    // });

    // 13 ***
        Route::middleware(['setData', 'setLanguage','language','FirstLogin' ])->group(function () {
            Auth::routes();
            Route::get('/business/register', 'BusinessController@getRegister')->name('business.getRegister');
            Route::post('/business/register', 'BusinessController@postRegister')->name('business.postRegister');
            Route::post('/business/register/check-username', 'BusinessController@postCheckUsername')->name('business.postCheckUsername');
            Route::post('/business/register/check-email', 'BusinessController@postCheckEmail')->name('business.postCheckEmail');
            Route::get('/invoice/{token}', 'SellPosController@showInvoice')->name('show_invoice');
            Route::get('/quote/{token}', 'SellPosController@showInvoice')->name('show_quote');
        });
    // 13 ***

    // 5 ***
        Route::get('/auth/status', function () {
            return response()->json(['authenticated' => auth()->check()]);
        })->name('auth.status');
        Route::get('/lang/{locale}','General\ChangeController@change');
        Route::get("/user/log-out","ManageUserController@log_out");
    // 5 ***

    Route::get('/home/change-lang-app', 'HomeController@changeLanguageApp');
// ***********************************************************************
    //Routes for authenticated users only
    Route::middleware(['SetDatabaseConnection',  'setData', 'authIzo', 'SetSessionData',  'language', 'timezone', 'AdminSidebarMenu', 'CheckUserLogin'])->group(function () {
        // 10 ***
            Route::get('/', 'HomeController@index');
            Route::get('/main', 'HomeController@index')->name('home');
            Route::get('/home', 'HomeController@index')->name('home');
            Route::get('/home/get-totals', 'HomeController@getTotals');
            Route::get('/home/get-expense', 'HomeController@getExpense');
            Route::get('/home/change-lang', 'HomeController@changeLanguage');
            Route::get('/home/product-stock-alert', 'HomeController@getProductStockAlert');
            Route::get('/home/purchase-payment-dues', 'HomeController@getPurchasePaymentDues');
            Route::get('/home/sales-payment-dues', 'HomeController@getSalesPaymentDues');
            Route::post('/attach-medias-to-model', 'HomeController@attachMediasToGivenModel')->name('attach.medias.to.model');
            Route::get('/calendar', 'HomeController@getCalendar')->name('calendar');
            Route::post('/save-attachment', 'HomeController@saveAttach');
            Route::get('/form-attachment', 'HomeController@formAttach');
            Route::get('/update-session-home', 'HomeController@updateSessionHome');
        // 10 ***
        // 1 **
            Route::get('/download-sql', 'SqlDownloadController@download');
        // 1 **
        // 10 ***
            Route::post('/test-email', 'BusinessController@testEmailConfiguration');
            Route::post('/test-sms', 'BusinessController@testSmsConfiguration');
            Route::get('/business/settings', 'BusinessController@getBusinessSettings')->name('business.getBusinessSettings');
            Route::post('/business/update', 'BusinessController@postBusinessSettings')->name('business.postBusinessSettings');
            Route::get('/user/profile', 'UserController@getProfile')->name('user.getProfile');
            Route::post('/user/update', 'UserController@updateProfile')->name('user.updateProfile');
            Route::post('/user/update/language', 'UserController@updateLanguage')->name('user.updateLanguage');
            Route::post('/user/update-password', 'UserController@updatePassword')->name('user.updatePassword');
            Route::resource('tax-rates', 'TaxRateController');
            Route::resource('brands', 'BrandController');
        // 10 ***
        /*Route::resource('payment-account', 'PaymentAccountController');*/

        // 6 ***
            Route::get('units/add', 'UnitController@Add');
            Route::post('units/update/{$id}', 'UnitController@updateUnit');
            Route::get('units/default', 'UnitController@Default');
            Route::get('units/in-price', 'UnitController@InPrice');
            Route::get('units/change-units', 'UnitController@Change');
            Route::resource('units', 'UnitController');
        // 6 ***

        // 13 ***
            Route::get('/contacts/payments/{contact_id}', 'ContactController@getContactPayments');
            Route::get('/contacts/map', 'ContactController@contactMap');
            Route::get('/contacts/repeat', 'ContactController@repeat_name');
            Route::get('/contacts/check/{name}', 'ContactController@check');
            Route::get('/contacts/update-status/{id}', 'ContactController@updateStatus');
            Route::get('/contacts/stock-report/{supplier_id}', 'ContactController@getSupplierStockReport');
            Route::get('/contacts/ledger', 'ContactController@getLedger');
            Route::post('/contacts/send-ledger', 'ContactController@sendLedger');
            Route::get('/contacts/import', 'ContactController@getImportContacts')->name('contacts.import');
            Route::post('/contacts/import', 'ContactController@postImportContacts');
            Route::post('/contacts/check-contact-id', 'ContactController@checkContactId');
            Route::get('/contacts/customers', 'ContactController@getCustomers');
            Route::resource('contacts', 'ContactController');
            // 13 ***
            
            Route::get('/expense/change-account', 'PurchaseController@changeAccountExpense');
            
        // 3 ***
            Route::get('taxonomies-ajax-index-page', 'TaxonomyController@getTaxonomyIndexPage');
            Route::resource('taxonomies', 'TaxonomyController');
            Route::resource('variation-templates', 'VariationTemplateController');
        // 3 ***
        
            
        
        // 19 ***
            Route::get('/test-login','ProductController@testLogin');
            Route::get('/products/add', 'BrandController@create1');
            Route::get('/product/sub-main/{id}','ProductController@subOfMain');
            Route::get('/product/check/{name}','ProductController@check');
            Route::get('/products/add/sub', 'BrandController@createSub');
            Route::get('/products/movement/{id}', 'ProductController@movement');
            Route::get('/products/Information/{id}', 'ProductController@show_Global');
            Route::get('/products/stock-history/{id}', 'ProductController@productStockHistory');
            Route::get('/delete-media/{media_id}', 'ProductController@deleteMedia');
            Route::post('/products/mass-deactivate', 'ProductController@massDeactivate');
            Route::get('/products/activate/{id}', 'ProductController@activate');
            Route::get('/products/view-product-group-price/{id}', 'ProductController@viewGroupPrice');
            Route::get('/products/add-selling-prices/{id}', 'ProductController@addSellingPrices');
            Route::post('/products/save-selling-prices', 'ProductController@saveSellingPrices');
            Route::post('/products/mass-delete', 'ProductController@massDestroy');
            Route::get('/products/view/{id}', 'ProductController@view');
            Route::get('/products/view/details/{id}', 'ProductController@viewStockDetails');
            Route::get('/products/view-Stock/{id}', 'ProductController@viewStock');
            Route::get('/products/view-Stock_status/{id}', 'ProductController@viewStatusReport');
            Route::get('/product/remove-image', 'ProductController@removeImage');
        // 19 ***
        
        // 3 ***
            // ..... E-commerce 
            Route::get('/products/get-pro', 'ProductController@getEProduct');
            Route::get('/sections/all', 'ProductController@getESection');
            Route::get('/sections/create', 'ProductController@createSection');
            Route::get('/sections/edit/{id}', 'ProductController@editSection');
            Route::post('/sections/save', 'ProductController@saveSections');
            Route::post('/sections/update/{id}', 'ProductController@updateSections');
            Route::get('/products/add-view', 'ProductController@addFView');
            Route::get('/products/cancel-view', 'ProductController@cancelFView');
        // 3 ***
        
        // 3 ***
            /* os */
            Route::get('/products/unrecieved-Stock/{id}', 'ProductController@viewUnrecieved');
            //... eb
            Route::get('/products/undelivered-Stock/{id}/{f}', 'ProductController@viewDeliveredf');
            Route::get('/products/transfer/{id}', 'ProductController@getTrans');
            //............. eb
            Route::get('/products/undelivered-Stock/{id}', 'ProductController@viewDelivered');
            //end
        // 3 ***
        // 4 ***
            Route::get('/products/addbarcode/{id}', 'ProductController@addbarcode');
            Route::get('/products/savebarcode', 'ProductController@savebarcode');
            Route::get('/products/getproductbarcode', 'ProductController@getproductbarcode');
            Route::get('/products/deletebarcode', 'ProductController@deletebarcode');
        // 4 ***
        
        // 6 ***
            Route::get('/products/list', 'ProductController@getProducts');
            Route::get('/products/list-no-variation', 'ProductController@getProductsWithoutVariations');
            Route::post('/products/bulk-edit', 'ProductController@bulkEdit');
            Route::post('/products/bulk-update', 'ProductController@bulkUpdate');
            Route::post('/products/bulk-update-location', 'ProductController@updateProductLocation');
            Route::get('/products/get-product-to-edit/{product_id}', 'ProductController@getProductToEdit');
        // 6 ***
        
        // 20 ***
            Route::post('/products/get_sub_categories', 'ProductController@getSubCategories');
            Route::get('/products/get_sub_units', 'ProductController@getSubUnits');
            Route::post('/products/product_form_part', 'ProductController@getProductVariationFormPart');
            Route::post('/products/get_product_variation_row', 'ProductController@getProductVariationRow');
            Route::post('/products/get_variation_template', 'ProductController@getVariationTemplate');
            Route::get('/products/get_variation_value_row', 'ProductController@getVariationValueRow');
            Route::post('/products/check_product_sku', 'ProductController@checkProductSku');
            Route::get('/products/unchangeFeature', 'ProductController@unChangeFeature');
            Route::get('/products/changeFeature', 'ProductController@changeFeature');
            Route::get('/products/quick_add', 'ProductController@quickAdd');
            Route::post('/products/save_quick_product', 'ProductController@saveQuickProduct');
            Route::get('/products/get-combo-product-entry-row', 'ProductController@getComboProductEntryRow');
            Route::get('/products/add-Opening-Product', 'ProductController@AddOpeningProduct');
            Route::get('/products/Opening_product/destroy/{id}', 'OpeningQuantity\HomeController@destroy'); 
            Route::get('/products/Opening_product/edit/{id}', 'OpeningQuantity\HomeController@edit');
            Route::post('/products/Opening_product/edit/{id}', 'OpeningQuantity\HomeController@update');
            Route::get('/products/Opening_product/view/{id}', 'ProductController@ViewOpeningProduct');
            Route::get('/products/Opening_product', 'ProductController@OpeningProduct');
            Route::post('/products/open-stock', 'ProductController@updateProduct');
            Route::resource('products', 'ProductController');
        // 20 ***
        // 2 ***
            /* osama  */
            Route::post('opening-quantity/add','OpeningQuantity\HomeController@add');
            Route::post('/opening-quantity/update/','OpeningQuantity\HomeController@update');
        // 2 ***
        
        // 6 ***
            Route::get('contact-banks','General\ContactBankController@index');
            Route::get('contact-banks/add','General\ContactBankController@add');
            Route::post('contact-banks/add','General\ContactBankController@post_add');
            Route::get('contact-banks/delete/{id}','General\ContactBankController@delete');
            Route::get('contact-banks/edit/{id}','General\ContactBankController@edit');
            Route::post('contact-banks/edit/{id}','General\ContactBankController@post_edit');
        // 6 ***
            Route::get('/set/pnt', 'Report\PrinterSettingController@printer_setting');
            Route::get('/printer/header/style', 'Report\PrinterSettingController@header_style');
            Route::get('/printer/footer/style', 'Report\PrinterSettingController@footer_style');
            Route::get('/printer/body/style', 'Report\PrinterSettingController@body_style');
            Route::get('/printer/header/content', 'Report\PrinterSettingController@header_content');
            Route::get('/printers/print/p', 'Report\PrinterSettingController@generatePdf');
            Route::get('/printers/print/del/{id}', 'Report\PrinterSettingController@destroy');

        // 13 ***
            Route::get('cheque','General\CheckController@index');
            Route::get('cheque/add','General\CheckController@add');
            Route::get('cheque/show/{id}','General\CheckController@show');
            Route::get('cheque/entry/{id}','General\CheckController@entry');
            Route::post('cheque/add','General\CheckController@post_add');
            Route::get('cheque/delete/{id}','General\CheckController@delete');
            Route::get('cheque/edit/{id}','General\CheckController@edit');
            Route::post('cheque/collect/{id}','General\CheckController@collect');
            Route::get('cheque/delete-collect/{id}','General\CheckController@delete_collect');
            Route::get('cheque/un-collect/{id}','General\CheckController@un_collect');
            Route::get('cheque/refund/{id}','General\CheckController@refund');
            Route::post('cheque/edit/{id}','General\CheckController@post_edit');
            Route::get('cheque/bills','General\CheckController@cheque_bills');
        // 13 ***
        // End  Cheque 
  
        // 6 ***
            //agents
            Route::get('agents','General\AgentController@index');
            Route::get('agents/add','General\AgentController@add');
            Route::post('agents/add','General\AgentController@post_add');
            Route::get('agents/delete/{id}','General\AgentController@delete');
            Route::get('agents/edit/{id}','General\AgentController@edit');
            Route::post('agents/edit/{id}','General\AgentController@post_edit');
        // 6 ***
    
        // 1 ***
            //.... alert 
            Route::get('alert-payment/show','PurchaseController@payment_msg');
            Route::get('alert-check/show','SellController@check_msg');
        // 1 ***
        
        // 3 ***
            //.... product price 
            Route::get('product_price/','ProductPriceController@index');
            Route::post('product_price/save','ProductPriceController@store');
            Route::post('product_price/update/{id}','ProductPriceController@update');
        // 3 ***
       
       
        // 16 ***
            // payment-voucher
            Route::get('payment-voucher','General\PaymentVoucherController@index');
            Route::get('payment-voucher/add','General\PaymentVoucherController@add');
            Route::get('payment-voucher/show/{id}','General\PaymentVoucherController@show');
            Route::post('payment-voucher/add','General\PaymentVoucherController@post_add');
            Route::get('payment-voucher/attachment/{id}','General\PaymentVoucherController@attach');
            Route::get('payment-voucher/delete/{id}','General\PaymentVoucherController@delete');
            Route::get('payment-voucher/edit/{id}/{type}','General\PaymentVoucherController@edit');
            Route::get('payment-voucher/edit/{id}','General\PaymentVoucherController@edit');
            Route::get('payment-voucher/entry/{id}','General\PaymentVoucherController@entry');
            Route::get('payment-voucher/return/{id}','General\PaymentVoucherController@returnVoucher');
            Route::post('payment-voucher/collect/{id}','General\PaymentVoucherController@collect');
            Route::get('payment-voucher/delete-collect/{id}','General\PaymentVoucherController@delete_collect');
            Route::get('cheque/refund/{id}','General\CheckController@refund');
            Route::get('cheque/attachment/{id}','General\CheckController@attach');
            Route::get('payment-voucher/un-collect/{id}','General\PaymentVoucherController@un_collect');
            Route::get('payment-voucher/refund/{id}','General\PaymentVoucherController@refund');
            Route::post('payment-voucher/edit/{id}','General\PaymentVoucherController@post_edit');
            Route::post('payment-voucher/whatsapp-save/{id}','General\PaymentVoucherController@post_message');
            Route::get('payment-voucher/whatsapp/{id}','General\PaymentVoucherController@set_message');
            Route::get('payment-voucher/choose-pattern/{id}','General\PaymentVoucherController@choosePattern');
        //   16 ***
        
        // 9 ***
            // daily-payment
            Route::get('daily-payment','General\DailyPaymentController@index');
            Route::get('daily-payment/show/{id}','General\DailyPaymentController@show');
            Route::get('daily-payment/add','General\DailyPaymentController@add');
            Route::get('daily-payment/attachment/{id}','General\DailyPaymentController@attach');
            Route::get('daily-payment/entry/{id}','General\DailyPaymentController@entry');
            Route::post('daily-payment/add','General\DailyPaymentController@post_add');
            Route::get('daily-payment/delete/{id}','General\DailyPaymentController@delete');
            Route::get('daily-payment/edit/{id}','General\DailyPaymentController@edit');
            Route::post('daily-payment/edit/{id}','General\DailyPaymentController@post_edit');
        // 9 *** 
        
        // 9 ***
            //................21-12-2022
            // gournal-voucher
            Route::get('gournal-voucher','General\GournalVoucherController@index');
            Route::get('gournal-voucher/add','General\GournalVoucherController@add');
            Route::get('gournal-voucher/attachment/{id}','General\GournalVoucherController@attach');
            Route::post('gournal-voucher/add','General\GournalVoucherController@post_add');
            Route::get('gournal-voucher/delete/{id}','General\GournalVoucherController@delete');
            Route::get('gournal-voucher/entry/{id}','General\GournalVoucherController@entry');
            Route::get('gournal-voucher/view/{id}','General\GournalVoucherController@view');
            Route::get('gournal-voucher/edit/{id}','General\GournalVoucherController@edit');
            Route::post('gournal-voucher/edit/{id}','General\GournalVoucherController@post_edit');
        // 9 ***
        
        
        // 14 ***
            //product Gallery
            Route::get('/purchases/delivered_page/{id}', 'PurchaseController@delivered_page');
            Route::get('/purchases/recieved_page', 'PurchaseController@recieved_page');
            Route::get('/purchases/get-balance', 'PurchaseController@getAccountBalance');
            Route::get('/purchases/update_recieved/{id}/{trn}', 'PurchaseController@update_recieved');
            Route::post('/purchases/update-status', 'PurchaseController@updateStatus');
            Route::get('/purchases/get_products/open/{open}/{edit}', 'PurchaseController@getProductsOpen');
            Route::get('/purchases/get_products/open/{open}', 'PurchaseController@getProductsOpen');
            Route::get('/purchases/get_products', 'PurchaseController@getProducts');
            Route::get('/purchases/get_suppliers', 'PurchaseController@getSuppliers');
            Route::post('/purchases/sup_refe', 'PurchaseController@Sup_refe');
            Route::post('/purchases/get_purchase_entry_row/{open}/{edit}', 'PurchaseController@getPurchaseEntryRow_open');
            Route::post('/purchases/get_purchase_entry_row/{open}', 'PurchaseController@getPurchaseEntryRow_open');
            Route::post('/purchases/get_purchase_entry_row', 'PurchaseController@getPurchaseEntryRow');
            Route::post('/purchases/check_ref_number', 'PurchaseController@checkRefNumber');
            Route::resource('purchases', 'PurchaseController')->except(['show']);
        // 14 ***
        // 3 ***
            /*osama routes */ 
            Route::post('recive/purchase/{id}','Recieved\HomeController@index');
            Route::post('recive/purchase/edit/{id}','Recieved\HomeController@update');
            Route::get('general/recieved','Recieved\CreateController@index');
            /* end my rout  */
        // 3 ***
        
        // 2 ***
            /*return  routes */ 
            Route::post('recive/purchase-return/{id}','Recieved\HomeReturnController@index');
            Route::post('recive/purchase-return/edit/{id}','Recieved\HomeReturnController@update');
            /* end my rout  */
        // 2 ***
        
        // 16 ***
            Route::get('/toggle-subscription/{id}', 'SellPosController@toggleRecurringInvoices');
            Route::post('/sells/pos/get-types-of-service-details', 'SellPosController@getTypesOfServiceDetails');
            Route::get('/sells/subscriptions', 'SellPosController@listSubscriptions');
            Route::get('/sells/duplicate/{id}', 'SellController@duplicateSell');
            Route::get('/sells/drafts', 'SellController@getDrafts');
            Route::get('/sells/QuatationApproved', 'SellController@getApproved');
            Route::get('/sells/convert-to-invoice/{id}', 'SellPosController@convertToInvoice');
            Route::get('/sells/convert-to-draft/{id}', 'SellPosController@convertToQoutation');
            Route::get('/sells/convert-to-proforma/{id}', 'SellPosController@convertToProforma');
            Route::get('/sells/quotations', 'SellController@getQuotations');
            Route::get('/sells/quatation-dt', 'SellController@getQuatationList');
            Route::get('/sells/draft-dt', 'SellController@getDraftDatables');
            Route::get('/sells/draft-dt1', 'SellController@getQuatationApproved');
            Route::get('/sells/change-warehouse/{id}', 'SellController@changeStore');
            Route::get('/sells/change-ware/by', 'SellController@changeByStore');
            Route::get('/sells/destroy/{id}', 'SellPosController@destroy');
            Route::get('sells/attachment/{id}','SellController@attach');
            Route::resource('sells', 'SellController')->except(['show']);
        // 16 ***
        
        // 7 ***
            Route::get('/sells/terms', 'QuotationController@index');
            Route::get('/sells/add-terms', 'QuotationController@create');
            Route::post('/sells/store-terms', 'QuotationController@store');
            Route::get('/sells/edit-terms/{id}', 'QuotationController@edit');
            Route::get('/sells/show-terms/{id}', 'QuotationController@show');
            Route::post('/sells/update-terms/{id}', 'QuotationController@update');
            Route::get('/sells/terms/destroy/{id}', 'QuotationController@destroy');
        // 7 ***

        // 1 ***
            Route::get('/sells/pos/search-for-transaction/{invoice_number}', 'SellPosController@getRecentTransactions');
        // 1 ***

        // 4 ***
            Route::get('/import-sales', 'ImportSalesController@index');
            Route::post('/import-sales/preview', 'ImportSalesController@preview');
            Route::post('/import-sales', 'ImportSalesController@import');
            Route::get('/revert-sale-import/{batch}', 'ImportSalesController@revertSaleImport');
        // 4 ***
        
        // 11 ***
            Route::get('/sells/pos/get_product_row/{variation_id}/{location_id}/{store_id}/{status}/{contact_id}', 'SellPosController@getProductRow');
            Route::get('/sells/pos/get_product_row/{variation_id}/{location_id}/{store_id}/{status}', 'SellPosController@getProductRow');
            Route::get('/sells/pos/get_product_row/{variation_id}/{location_id}/{store_id}', 'SellPosController@getProductRow');
            Route::get('/sells/pos/get_product_row/{variation_id}/{location_id}', 'SellPosController@getProductRow');
            Route::post('/sells/pos/get_payment_row', 'SellPosController@getPaymentRow');
            Route::post('/sells/pos/get-reward-details', 'SellPosController@getRewardDetails');
            Route::get('/sells/pos/get-recent-transactions', 'SellPosController@getRecentTransactions');
            Route::get('/sells/pos/get-product-suggestion', 'SellPosController@getProductSuggestion');
            Route::get('/sells/pos/get-featured-products/{location_id}', 'SellPosController@getFeaturedProducts');
            Route::get('/sell/project_no', 'SellController@Project_no');
            Route::resource('pos', 'SellPosController');
        // 11 ***
        
        // 3 ***
            Route::resource('roles', 'RoleController');
            Route::resource('users', 'ManageUserController');
            Route::resource('group-taxes', 'GroupTaxController');
        // 3 ***
        
        // 2 ***
            Route::get('/barcodes/set_default/{id}', 'BarcodeController@setDefault');
            Route::resource('barcodes', 'BarcodeController');
        // 2 ***
        // 2 ***
            //Invoice schemes..
            Route::get('/invoice-schemes/set_default/{id}', 'InvoiceSchemeController@setDefault');
            Route::resource('invoice-schemes', 'InvoiceSchemeController');
        // 2 ***


        // 7 ***
            //.... pos create 
            Route::get('/pos-branch/go-to-pos', 'PosBranchController@Pos')->name("posBranch.all");
            Route::get('/pos-branch', 'PosBranchController@index')->name("posBranch.index");
            Route::get('/pos-branch/create', 'PosBranchController@create')->name("posBranch.create");
            Route::get('/pos-branch/edit/{id}', 'PosBranchController@edit')->name("posBranch.edit");
            Route::get('/pos-branch/show/{id}', 'PosBranchController@show')->name("posBranch.show");
            Route::post('/pos-branch/store', 'PosBranchController@store')->name("posBranch.store");
            Route::post('/pos-branch/update/{id}', 'PosBranchController@update')->name("posBranch.update");
        // 7 ***
        
        // 1 ***
            //......... mfg 
            Route::get('/check-store/{store}/{product}', 'WarehouseController@check')->name("warehouse.check");
        // 1 ***
        
        // 4 ***
            //..... symbol 
            Route::get('/symbol/{id}', 'BusinessController@symbol')->name("business.symbol");
            Route::get('/symbol/amount/{id}', 'BusinessController@symbol_amount')->name("business.symbol_amount");
            Route::get('/symbol-right-amount/{id}', 'BusinessController@symbolRightAmount')->name("business.symbol-right");
            Route::get('/symbol-left-amount/{id}', 'BusinessController@symbolLeftAmount')->name("business.symbol-left");
        // 4 ***
        


        //... delivery and recieved

        // 7 ***
            Route::get('/delivery/create', 'DeliveryPageController@create')->name("delivery.create");
            Route::get('/delivery/edit-delivery/{id}/{return}', 'DeliveryPageController@edit_delivery')->name("edit_delivery_return");
            Route::get('/delivery/edit-delivery/{id}', 'DeliveryPageController@edit_delivery')->name("edit_delivery");
            Route::get('/delivery/edite', 'DeliveryPageController@edit')->name("delivery.edite");
            Route::get('/delivery/index', 'DeliveryPageController@index')->name("delivery.index");
            Route::get('/delivery/show', 'DeliveryPageController@show')->name("delivery.show");
            Route::get('/delivery/allStores', 'DeliveryPageController@allStores')->name("allStores");
        // 7 ***
        // 5 ***
            Route::get('/recieved/create', 'RecievedPageController@create')->name("recieved.create");
            Route::get('/recieved/edite', 'RecievedPageController@edit')->name("recieved.edite");
            Route::get('/recieved/index', 'RecievedPageController@index')->name("recieved.index");
            Route::get('/recieved/show', 'RecievedPageController@show')->name("recieved.show");
            Route::get('/recieved/allStores', 'RecievedPageController@allStores')->name("allStores");
        // 5 ***
        // 2 ***
            /* os delivery  route */
            Route::post('delivery/add/{id}','Delivery\HomeController@index');
            Route::post('delivery/update/{id}','Delivery\HomeController@update');
            //end 
        // 2 ***
        
        // 6 ****
            /* Registers Section Route */
            // ... ** react frontend section ** ...  \\
            Route::get('/Rct/get-api', 'ReactFrontController@getApiList');
            Route::get('/Rct/get-api/create', 'ReactFrontController@createApi');
            Route::get('/Rct/get-api/edit/{id}', 'ReactFrontController@editApi');
            Route::post('/Rct/get-api/save', 'ReactFrontController@storeApi');
            Route::post('/Rct/get-api/update/{id}', 'ReactFrontController@updateApi');
            Route::delete('/Rct/get-api/delete/{id}', 'ReactFrontController@delete');
        // 6 ****

        // 6 ****
            // ... ** mobile section ** ...  \\
            Route::get('/get-api', 'ApimobileController@getApiList');
            Route::get('/get-api/create', 'ApimobileController@createApi');
            Route::get('/get-api/edit/{id}', 'ApimobileController@editApi');
            Route::post('/get-api/save', 'ApimobileController@storeApi');
            Route::post('/get-api/update/{id}', 'ApimobileController@updateApi');
            Route::delete('/get-api/delete/{id}', 'ApimobileController@delete');
            //end
        // 6 ****

        // 2 ***
            /*  return delivery  route */
            Route::post('delivery/add-return/{id}','Delivery\HomeReturnController@index');
            Route::post('delivery/update-return/{id}','Delivery\HomeReturnController@update');
            //end 
        // 2 ***

        // ** stripe
        // 2 **
            Route::get("checkout","ApiController\Ecommerce\CheckoutController@checkout");
            Route::post("checkout","ApiController\Ecommerce\CheckoutController@afterPayment")->name("checkout.credit-card");
        // 2 **

        // 4 ***
            //Print Labels
            Route::get('/labels/show', 'LabelsController@show');
            Route::get('/labels/add-product-row', 'LabelsController@addProductRow');
            Route::get('/labels/preview', 'LabelsController@preview');
            Route::get('/labels/preview_purchase', 'LabelsController@preview_purchase');
        // 4 ***
        
        // 10 ***
            //Reports...
            Route::get('/reports/purchase-report', 'ReportController@purchaseReport');
            Route::get('/reports/sale-report', 'ReportController@saleReport');
            Route::get('/reports/service-staff-report', 'ReportController@getServiceStaffReport');
            Route::get('/reports/service-staff-line-orders', 'ReportController@serviceStaffLineOrders');
            Route::get('/reports/table-report', 'ReportController@getTableReport');
            Route::get('/reports/profit-loss', 'ReportController@getProfitLoss');
            Route::get('/reports/get-opening-stock', 'ReportController@getOpeningStock');
            Route::get('/reports/purchase-sell', 'ReportController@getPurchaseSell');
            Route::get('/reports/customer-supplier', 'ReportController@getCustomerSuppliers');
            Route::get('/reports/stock-report', 'ReportController@getStockReport');
        // 10 ***
        
        // 17 ***
            Route::get('/reports/stock-details', 'ReportController@getStockDetails');
            Route::get('/reports/tax-report', 'ReportController@getTaxReport');
            Route::get('/reports/tax-details', 'ReportController@getTaxDetails');
            Route::get('/reports/trending-products', 'ReportController@getTrendingProducts');
            Route::get('/reports/expense-report', 'ReportController@getExpenseReport');
            Route::get('/reports/stock-adjustment-report', 'ReportController@getStockAdjustmentReport');
            Route::get('/reports/register-report', 'ReportController@getRegisterReport');
            Route::get('/reports/sales-representative-report', 'ReportController@getSalesRepresentativeReport');
            Route::get('/reports/sales-representative-total-expense', 'ReportController@getSalesRepresentativeTotalExpense');
            Route::get('/reports/sales-representative-total-sell', 'ReportController@getSalesRepresentativeTotalSell');
            Route::get('/reports/sales-representative-total-commission', 'ReportController@getSalesRepresentativeTotalCommission');
            Route::get('/reports/stock-expiry', 'ReportController@getStockExpiryReport');
            Route::get('/reports/stock-expiry-edit-modal/{purchase_line_id}', 'ReportController@getStockExpiryReportEditModal');
            Route::post('/reports/stock-expiry-update', 'ReportController@updateStockExpiryReport')->name('updateStockExpiryReport');
            Route::get('/reports/customer-group', 'ReportController@getCustomerGroup');
            Route::get('/reports/product-purchase-report', 'ReportController@getproductPurchaseReport');
            Route::get('/reports/product-sell-report', 'ReportController@getproductSellReport');
        // 17 ***
        
        // 13 ***
            Route::get('/reports/product-sell-return-report', 'ReportController@getproductSellReturnReport');
            Route::get('/reports/product-sell-report-with-purchase', 'ReportController@getproductSellReportWithPurchase');
            Route::get('/reports/product-sell-grouped-report', 'ReportController@getproductSellGroupedReport');
            Route::get('/reports/lot-report', 'ReportController@getLotReport');
            Route::get('/reports/purchase-payment-report', 'ReportController@purchasePaymentReport');
            Route::get('/reports/sell-payment-report', 'ReportController@sellPaymentReport');
            Route::get('/reports/product-stock-details', 'ReportController@productStockDetails');
            Route::get('/reports/adjust-product-stock', 'ReportController@adjustProductStock');
            Route::get('/reports/get-profit/{by?}', 'ReportController@getProfit');
            Route::get('/reports/items-report', 'ReportController@itemsReport');
            Route::get('/reports/get-stock-value', 'ReportController@getStockValue');
            Route::get('/reports/settings', 'ReportController@reportSetting');
            Route::post('/reports/settings/save', 'ReportController@reportSettingSave');
        // 13 ***
            Route::get('/printer/settings', 'Report\PrinterSettingController@generatePdf');
            Route::post('/printer/settings/store', 'Report\PrinterSettingController@store');
            Route::get('/printer/settings/create', 'Report\PrinterSettingController@index');
            Route::get('/printer/settings/list', 'Report\PrinterSettingController@index');
            Route::get('/printer/settings/edit/{id}', 'Report\PrinterSettingController@edit');
            Route::post('/printer/settings/update/{id}', 'Report\PrinterSettingController@update');
    
    
        // 1 ***
            /* Report for sell by eng mohamed ali*/
            Route::get('/reports/getsells', 'ReportController@getsells');
        // 1 ***
        
        // 1 ***
            Route::get('business-location/activate-deactivate/{location_id}', 'BusinessLocationController@activateDeactivateLocation');
        // 1 ***
        
        // 4 ***
            //Business Location Settings...
            Route::prefix('business-location/{location_id}')->name('location.')->group(function () {
                Route::get('settings', 'LocationSettingsController@index')->name('settings');
                Route::post('settings', 'LocationSettingsController@updateSettings')->name('settings_update');
            });
        // 4 ***
    
    
        // 2 ***
            //Business Locations...
            Route::post('business-location/check-location-id', 'BusinessLocationController@checkLocationId');
            Route::resource('business-location', 'BusinessLocationController');
        // 2 ***
        
        // 1 ***
            //Invoice layouts..
            Route::resource('invoice-layouts', 'InvoiceLayoutController');
        // 1 ***
        
        // 1 ***
            //Expense Categories...
            Route::resource('expense-categories', 'ExpenseCategoryController');
        // 1 ***
        
        // 1 ***
            //Expenses...
            Route::resource('expenses', 'ExpenseController');
        // 1 ***
        
        
        // 2 ***
            //... status live
            Route::get("/status-live","StatusLiveController@index");
            Route::get("/status-live/show/{id}","StatusLiveController@show");
        // 2 ***
        
        // 3 ***
            //... item move
            Route::get("/item-move/{id}","ItemMoveController@index");
            Route::get("/item-move/show/{id}","ItemMoveController@showMovement");
            Route::get("/get-move","ItemMoveController@getMove");
            Route::get("/get-previous-move","ItemMoveController@getPrevious");
        // 3 ***
        
        // 8 ***
            //.. patterns
            Route::get("/patterns-list","PatternController@index");
            Route::get("/patterns-create","PatternController@create");
            Route::get("/patterns-show/{id}","PatternController@show");
            Route::get("/patterns-edit/{id}","PatternController@edit");
            Route::post("/patterns-store","PatternController@store");
            Route::post("/patterns-update/{id}","PatternController@update");
            Route::get("/patterns-default/{id}","PatternController@DefaultPattern");
            Route::get("/patterns-delete/{id}","PatternController@destroy");
        // 8 ***
        // 16 ***
            //Transaction payments...
            // Route::get('/payments/opening-balance/{contact_id}', 'TransactionPaymentController@getOpeningBalancePayments');
            Route::get('/delivery/{id}', 'TransactionPaymentController@showww')->name("dele");
            Route::get('/deliver/update/{id}', 'TransactionPaymentController@update_recieve')->name("update_recieve");
            Route::get('/deliver/edit/{id}', 'TransactionPaymentController@edit_recieve')->name("edit_recieve");
            // Route::post('/deliver/delete/{id}', 'TransactionPaymentController@destroy_recieve')->name("destroy_recieve");
            // Route::post('/deliver/delete-d/{id}', 'TransactionPaymentController@destroy_delivery')->name("destroy_delivery");
            Route::get('/deliver/delete/{id}', 'TransactionPaymentController@destroy_recieve')->name("destroy_recieve");
            Route::get('/deliver/delete-d/{id}', 'TransactionPaymentController@destroy_delivery')->name("destroy_delivery");
            Route::get('/deliver/{id}', 'TransactionPaymentController@showw')->name("del");
            Route::get('/delived/{id}', 'TransactionPaymentController@addRecieve')->name("addRecieve");
            Route::get('/delived/view/{id}', 'TransactionPaymentController@viewRecieve')->name("viewRecieve");
            Route::post('/deliverd_item', 'TransactionPaymentController@make')->name("makeRecieved");
            Route::get('/payments/show-child-payments/{payment_id}', 'TransactionPaymentController@showChildPayments');
            Route::get('/payments/view-payment/{payment_id}', 'TransactionPaymentController@viewPayment');
            Route::get('/payments/add_payment/{transaction_id}', 'TransactionPaymentController@addPayment');
            Route::get('/payments/pay-contact-due/{contact_id}', 'TransactionPaymentController@getPayContactDue');
            Route::post('/payments/pay-contact-due', 'TransactionPaymentController@postPayContactDue');
            Route::resource('payments', 'TransactionPaymentController');
            Route::get('/delived/view/{ref_no}', 'TransactionPaymentController@viewRecieve_ref')->name("viewRecieve_ref");
            Route::get('/payments/return/{id}', 'TransactionPaymentController@return_payment');
        // 16 ***

        // 2 ***
            //Printers...
            Route::resource('printers', 'PrinterController');
           
            Route::get('/delived/view/reciept/{id}', 'TransactionPaymentController@viewDelivered')->name("viewDelivered");
        // 2 ***
        
        // 4 ***
            // stock adjustment
            Route::get('/stock-adjustments/remove-expired-stock/{purchase_line_id}', 'StockAdjustmentController@removeExpiredStock');
            Route::post('/stock-adjustments/get_product_row/{variation_id}/{location_id}/{store_id}/{status}', 'StockAdjustmentController@getProductRow');
            Route::post('/stock-adjustments/get_product_row', 'StockAdjustmentController@getProductRow');
            Route::resource('stock-adjustments', 'StockAdjustmentController');
        // 4 ***
        
        // 2 ***
            /*  osama stock transfer  */
            Route::post('stock-tranfer','Stock\HomeController@index');
            Route::get('pos-sell-stock/{id}/{status}','Stock\HomeController@check_stock');
        // 2 ***
        
        // 4 ***
            Route::get('/cash-register/register-details', 'CashRegisterController@getRegisterDetails');
            Route::get('/cash-register/close-register/{id?}', 'CashRegisterController@getCloseRegister');
            Route::post('/cash-register/close-register', 'CashRegisterController@postCloseRegister');
            Route::resource('cash-register', 'CashRegisterController');
        // 4 ***
        
        // 2 ***
            //Import products
            Route::get('/import-products', 'ImportProductsController@index');
            Route::post('/import-products/store', 'ImportProductsController@store');
        // 2 ***
        
        // 1 ***
            //Sales Commission Agent
            Route::resource('sales-commission-agents', 'SalesCommissionAgentController');
        // 1 ***
        
        // 4 ***
            //Stock Transfer
            Route::get('stock-transfers/print/{id}', 'StockTransferController@printInvoice');
            Route::get('stock-transfers/edit/{id}', 'StockTransferController@edit');
            Route::post('stock-transfers/update-status/{id}', 'StockTransferController@updateStatus');
            Route::resource('stock-transfers', 'StockTransferController');
        // 4 ***
        
        // 2 ***
            Route::get('/opening-stock/add/{product_id}', 'OpeningStockController@add');
            Route::post('/opening-stock/save', 'OpeningStockController@save');
        // 2 ***
        
        
        // 3 ***
            //===stocktaking
            Route::any('/stocktacking', 'StocktackingController@index')->name('home');
            Route::any('/stocktacking/create', 'StocktackingController@create')->name('stocktaking.create');
            Route::any('/stocktacking/store', 'StocktackingController@store')->name('stocktaking.store');
        // 3 ***
        
       

        // 1 ***
            Route::any('/stocktacking/transaction/{id}', 'StocktackingController@transaction')->name('stocktaking.transaction');
        // 1 ***
        
        // 10 ***
            Route::any('/stocktacking/report_plus/{id}', 'StocktackingController@report_plus')->name('report_plus');
            Route::any('/stocktacking/report_minus/{id}', 'StocktackingController@report_minus')->name('report_minus');
            Route::any('/stocktacking/report/{id}', 'StocktackingController@report')->name('stocktaking.report');
            Route::any('/stocktacking/not-tacking-report/{id}', 'StocktackingController@not_tacking_report')->name('stocktaking.not_tacking');
            Route::any('/stocktacking/changeStatus/{id}/{status}', 'StocktackingController@changeStatus')->name('stocktaking.changeStatus');
            Route::any('/stocktacking/transaction_ajax_get', 'StocktackingController@transaction_ajax_get')->name('stocktaking.transaction_ajax_get');
            Route::any('/stocktacking/transaction_ajax_post', 'StocktackingController@transaction_ajax_post')->name('stocktaking.transaction_ajax_post');
            Route::any('/stocktacking/Stock_liquidation', 'StocktackingController@Stock_liquidation')->name('stocktaking.Stock_liquidation');
            Route::any('/stocktacking/delete_from_stocktacking', 'StocktackingController@delete_from_stocktacking')->name('stocktaking.delete_from_stocktacking');
            Route::any('/stocktacking/get_last_product', 'StocktackingController@get_last_product')->name('stocktaking.get_last_product');
        // 10 ***
        
        
        // 14 ***
            //warehouse
            Route::any('/warehouse/create', 'WarehouseController@create')->name('warehouse.create');
            Route::get('/warehouse/edit/{id}', 'WarehouseController@edit')->name('warehouse.edit');
            Route::post('/warehouse/update/{id}', 'WarehouseController@update_id')->name('warehouse.update');
            // Route::post('/warehouse/update/{id}', 'WarehouseController@update')->name('warehouse.update');
            Route::get('/warehouse/delete/{id}', 'WarehouseController@delete')->name('warehouse.delete');
            Route::any('/warehouse/index', 'WarehouseController@index')->name('warehouse.index');
            Route::any('/warehouse/report', 'WarehouseController@report')->name('warehouse.report');
            Route::any('/warehouse/conveyor', 'WarehouseController@conveyor')->name('warehouse.conveyor');
            Route::any('/warehouse/store', 'WarehouseController@store')->name('warehouse.store');
            Route::any('/warehouse/allStores', 'WarehouseController@allStores')->name('allStores');
            Route::any('/warehouse/allMovement', 'WarehouseController@allMovement')->name('allMovement');
            Route::any('/warehouse/movement/{store_id}', 'WarehouseController@movement')->name('movement');
            Route::any('/warehouse/movement', 'WarehouseController@movement')->name('movement');
            Route::any('/warehouse-delete','WarehouseController@zero_qty');
            Route::resource("/warehouse","WarehouseController");
        // 14 ***
        
        // 3 ***
            //acccountTree
            Route::any('/accountsTree/create', 'AccountTreeController@create')->name('accountsTree.create');
            Route::any('/accountsTree/index', 'AccountTreeController@index')->name('accountsTree.index');
            Route::any('/accountsTree/edit', 'AccountTreeController@edit')->name('accountsTree.edit');
        // 3 ***
        
        
        // 14 ***
            //.............
            Route::any('/accountsTree/assets', 'AccountTreeController@assets')->name('assets');
            Route::any('/accountsTree/Fixed_origin', 'AccountTreeController@Fixed_origin')->name('Fixed_origin');
            Route::any('/accountsTree/Buildings_Real_Estate', 'AccountTreeController@Buildings_Real_Estate')->name('Buildings_Real_Estate');
            Route::any('/accountsTree/Traded_asset', 'AccountTreeController@Traded_asset')->name('Traded_asset');
            Route::any('/accountsTree/liability', 'AccountTreeController@Liability')->name('liability');
            Route::any('/accountsTree/Fixed_liabilities', 'AccountTreeController@Fixed_liabilities')->name('Fixed_liabilities');
            Route::any('/accountsTree/Current_Liabilities', 'AccountTreeController@Current_Liabilities')->name('Current_Liabilities');
            Route::any('/accountsTree/Capital', 'AccountTreeController@Capital')->name('Capital');
            Route::any('/accountsTree/purchases', 'AccountTreeController@purchases')->name('purchases');
            Route::any('/accountsTree/sells', 'AccountTreeController@sells')->name('sells');
            Route::any('/accountsTree/expenses', 'AccountTreeController@expenses')->name('expenses');
            Route::any('/accountsTree/Revenue', 'AccountTreeController@Revenue')->name('Revenue');
            Route::any('/accountsTree/budget', 'AccountTreeController@Budget')->name('budget');
            Route::any('/accountsTree/Profit_&_Loss', 'AccountTreeController@Profit_&_Loss')->name('ProfitandLoss');
        // 14 ***
        
        
        // 7 ***
            Route::any('/accountsTree/allpurchases', 'AccountTreeController@allPurcahses')->name('all_purchases');
            Route::any('/accountsTree/allsells', 'AccountTreeController@allsells')->name('all_sells');
            Route::any('/accountsTree/allassets', 'AccountTreeController@allassets')->name('all_assets');
            Route::any('/accountsTree/allliability', 'AccountTreeController@allliability')->name('all_liability');
            Route::any('/accountsTree/allexpense', 'AccountTreeController@allexpense')->name('all_expense');
            Route::any('/accountsTree/allBudget', 'AccountTreeController@allBudget')->name('all_Budget');
            Route::any('/accountsTree/allRevenue', 'AccountTreeController@allRevenue')->name('all_Revenue');
        // 7 ***
        
        
        // 1 ***
            //Customer Groups
            Route::resource('customer-group', 'CustomerGroupController');
        // 1 ***
        
        // 2 ***
            //Import opening stock
            Route::get('/import-opening-stock', 'ImportOpeningStockController@index');
            Route::post('/import-opening-stock/store', 'ImportOpeningStockController@store');
        // 2 ***
        
        // 6 ***
            //Sell return
            Route::get('sell-return/get-product-row', 'SellReturnController@getProductRow');
            Route::get('/sell-return/print/{id}', 'SellReturnController@printInvoice');
            Route::get('/sell-return/add/{id}', 'SellReturnController@add');
            Route::post('/sell-return/save', 'SellReturnController@save_return');
            Route::post('/sell-return/update', 'SellReturnController@update_return');
            Route::resource('sell-return', 'SellReturnController');
        // 6 ***
        
        // 5 ***
            //Backup
            Route::get('backup/download/{file_name}', 'BackUpController@download');
            Route::get('backup/delete/{file_name}', 'BackUpController@delete');
            Route::resource('backup', 'BackUpController', ['only' => [
                'index', 'create', 'store'
                ]]);
        // 5 ***
        
        
        // 3 ***
            Route::get('selling-price-group/activate-deactivate/{id}', 'SellingPriceGroupController@activateDeactivate');
            Route::get('export-selling-price-group', 'SellingPriceGroupController@export');
            Route::post('import-selling-price-group', 'SellingPriceGroupController@import');
        // 3 ***
        
        // 1 ***
            Route::resource('selling-price-group', 'SellingPriceGroupController');
        // 1 ***
        
        // 3 ***
            Route::resource('notification-templates', 'NotificationTemplateController')->only(['index', 'store']);
            Route::get('notification/get-template/{transaction_id}/{template_for}', 'NotificationController@getTemplate');
            Route::post('notification/send', 'NotificationController@send');
        // 3 ***
        
        
        // 8 ***
            Route::post('/purchase-return/update', 'CombinedPurchaseReturnController@update');
            Route::get('/purchase-return/edit/{id}', 'CombinedPurchaseReturnController@edit');
            Route::post('/purchase-return/save', 'CombinedPurchaseReturnController@save');
            // Route::post('/purchase-return/get_product_row', 'CombinedPurchaseReturnController@getProductRow');
            Route::get('/purchase-return/get_product_row', 'CombinedPurchaseReturnController@getProductRow');
            Route::get('/purchase-return/create', 'CombinedPurchaseReturnController@create');
            Route::get('/purchase-return/add/{id}', 'PurchaseReturnController@add');
            Route::post('/purchase-return/return', 'PurchaseReturnController@store_return');
            Route::resource('/purchase-return', 'PurchaseReturnController', ['except' => ['create']]);
        // 8 ***
        
        // 3 ***
            Route::get('/discount/activate/{id}', 'DiscountController@activate');
            Route::post('/discount/mass-deactivate', 'DiscountController@massDeactivate');
            Route::resource('discount', 'DiscountController');
        // 3 ***
        
        // 11 ***
            //..... delete actions
            Route::get('/delete-file', 'General\DeleteController@index');
            Route::get('/delete-all/admin-it', 'General\DeleteController@delete_all');
            Route::get('/delete-user/admin-it', 'General\DeleteController@delete_users');
            Route::get('/delete-customer/admin-it', 'General\DeleteController@delete_customers');
            Route::get('/delete-supplier/admin-it', 'General\DeleteController@delete_suppliers');
            Route::get('/delete-payments/admin-it', 'General\DeleteController@delete_payments');
            Route::get('/delete-accounts/admin-it', 'General\DeleteController@delete_accounts');
            Route::get('/delete-purchases/admin-it', 'General\DeleteController@delete_purchases');
            Route::get('/delete-sells/admin-it', 'General\DeleteController@delete_sells');
            Route::get('/delete-items/admin-it', 'General\DeleteController@delete_items');
            Route::get('/reset-numbers/admin-it', 'General\DeleteController@reset_numbers');
        // 11 ***
      
        Route::group(['prefix' => 'account'], function () {
            // 5 ***
                // system account 
                Route::get('system/{id}','General\SystemAccountController@edit');
                Route::get('system','General\SystemAccountController@create');
                Route::post('system/add','General\SystemAccountController@add');
                Route::post('system/update/{id}','General\SystemAccountController@update');
                Route::get("system-account-list",'General\SystemAccountController@index');
            // 5 ***
            // 5 ***
                //..... cost center
                Route::get('cost-center','General\CostCenterController@index');
                Route::get('cost-center/add','General\CostCenterController@add_cost_account');
                Route::post('cost-center/add','General\CostCenterController@post_add');
                Route::get('cost-center/edit/{id}','General\CostCenterController@edit');
                Route::post('cost-center/edit/{id}','General\CostCenterController@post_edit');
                //end 
            // 5 ***
            // 1 ***
                //...... entries
                Route::get('/entries/list','General\EntriesController@index');
                //... end
            // 1 ***
            // 21 ***
                Route::resource('/account', 'AccountController');
                Route::get('/account-ledger-show/{id}', 'AccountController@show_contact');
                Route::get('/account-show/{id}', 'AccountController@ledgerShow');
                Route::get('/fund-transfer/{id}', 'AccountController@getFundTransfer');
                Route::get('/cash', 'AccountController@showCash');
                Route::get('/getBalance/{id}', 'AccountController@getBalance');
                Route::get('/bank', 'AccountController@showBank');
                Route::get('/get-account', 'AccountController@getAccount');
                Route::get('/account-ref/{id}', 'AccountController@account_ref');
                Route::post('/fund-transfer', 'AccountController@postFundTransfer');
                Route::get('/deposit/{id}', 'AccountController@getDeposit');
                Route::post('/deposit', 'AccountController@postDeposit');
                Route::get('/close/{id}', 'AccountController@close');
                Route::get('/activate/{id}', 'AccountController@activate');
                Route::get('/delete-account-transaction/{id}', 'AccountController@destroyAccountTransaction');
                Route::get('/get-account-balance/{id}', 'AccountController@getAccountBalance');
                Route::get('/balance-sheet', 'AccountReportsController@balanceSheet');
                Route::get('/trial-balance', 'AccountReportsController@trialBalance');
                Route::get('/payment-account-report', 'AccountReportsController@paymentAccountReport');
                Route::get('/link-account/{id}', 'AccountReportsController@getLinkAccount');
                Route::post('/link-account', 'AccountReportsController@postLinkAccount');
                Route::get('/cash-flow', 'AccountController@cashFlow');
                Route::get('/get-balance/{id}', 'AccountController@getOneAccountBalance');
                Route::get('/repair-balances', 'AccountController@repairBalance');
            // 21 ***

            // 2  ***
                //........... filter account
                Route::get('/get-account-type/{id}', 'AccountController@filterAccountType');
                Route::get('/get-sub-account-type/{id}', 'AccountController@filterSubAccountType');
                //......
            // 2  ***

            // 2  ***
                Route::get('/check-name/{name}','AccountController@check');
                Route::get('/check-number/{number}','AccountController@check_number');
                //.........
            // 2  ***

            // 1  ***
                Route::get('/account-ty/{type}','AccountController@typeAccount');
            // 1  ***
            
            // 1  ***
                //... add account check number 
                Route::get('/get-num/{type}','AccountController@idTypeAccount');
            // 1  ***
            
            


        });

        // 5 ***
            Route::get('/check-activation', 'UserActivationController@checkActivation');
            Route::get('/user-activation/shows', 'UserActivationController@shows');
            Route::get('/user-activation/login-users', 'UserActivationController@login');
            Route::get('/user-activation/activate/{id}', 'UserActivationController@activate');
            Route::resource('user-activation', 'UserActivationController');
            Route::resource('account-types', 'AccountTypeController');
        // 5 ***
        
        //Restaurant module
        Route::group(['prefix' => 'modules'], function () {
            // 2 ***
                Route::resource('tables', 'Restaurant\TableController');
                Route::resource('modifiers', 'Restaurant\ModifierSetsController');
            // 2 ***
            
            // 4 ***
                //Map modifier to products
                Route::get('/product-modifiers/{id}/edit', 'Restaurant\ProductModifierSetController@edit');
                Route::post('/product-modifiers/{id}/update', 'Restaurant\ProductModifierSetController@update');
                Route::get('/product-modifiers/product-row/{product_id}', 'Restaurant\ProductModifierSetController@product_row');
                Route::get('/add-selected-modifiers', 'Restaurant\ProductModifierSetController@add_selected_modifiers');
            // 4 ***
            
            
            // 4 ***
                Route::get('/orders', 'Restaurant\OrderController@index');
                Route::get('/orders/mark-as-served/{id}', 'Restaurant\OrderController@markAsServed');
                Route::get('/data/get-pos-details', 'Restaurant\DataController@getPosDetails');
                Route::get('/orders/mark-line-order-as-served/{id}', 'Restaurant\OrderController@markLineOrderAsServed');
            // 4 ***
                
                
            // 4 ***
                Route::get('/kitchen', 'Restaurant\KitchenController@index');
                Route::get('/kitchen/mark-as-cooked/{id}', 'Restaurant\KitchenController@markAsCooked');
                Route::post('/refresh-orders-list', 'Restaurant\KitchenController@refreshOrdersList');
                Route::post('/refresh-line-orders-list', 'Restaurant\KitchenController@refreshLineOrdersList');
                // new form eng ali 20-4-2021
            // 4 ***
            
            // 11 ***
                /* Route::get('/orders', 'Restaurant\KitchenController@orders');*/
                Route::get('/kitchen_order', 'Restaurant\KitchenController@index_order');
                Route::get('/setorderstatus', 'Restaurant\KitchenController@setorderstatus');
                Route::get('/kitchen/create', 'Restaurant\KitchenController@create');
                Route::post('/kitchen/store', 'Restaurant\KitchenController@store');
                Route::get('/kitchen/edit/{id}', 'Restaurant\KitchenController@edit');
                Route::post('/kitchen/update', 'Restaurant\KitchenController@update');
                Route::post('/kitchen/delete/{id}', 'Restaurant\KitchenController@delete');
                Route::get('/kitchen_products', 'Restaurant\KitchenController@products');
                Route::get('/kitchen/product_add', 'Restaurant\KitchenController@product_add');
                Route::post('/kitchen/addtokitchen', 'Restaurant\KitchenController@addtokitchen');
                Route::post('/kitchen/removefromkitchen/{id}', 'Restaurant\KitchenController@removefromkitchen');
                // end 20-4-2021
            // 11 ***

        });

        // 2 *** 
            Route::get('bookings/get-todays-bookings', 'Restaurant\BookingController@getTodaysBookings');
            Route::resource('bookings', 'Restaurant\BookingController');
            // End of Restaurant
        // 2 *** 

        // 4 *** 
            Route::resource('types-of-service', 'TypesOfServiceController');
            Route::get('sells/edit-shipping/{id}', 'SellController@editShipping');
            Route::put('sells/update-shipping/{id}', 'SellController@updateShipping');
            Route::get('shipments', 'SellController@shipments');
        // 4 *** 
        
        // 1 *** 
            //... max
            Route::get('sells/max-qty', 'SellController@max_qty');
        // 1 *** 

        // 2 *** 
            Route::post('upload-module', 'Install\ModulesController@uploadModule');
            Route::resource('manage-modules', 'Install\ModulesController')->only(['index', 'destroy', 'update']);
        // 2 *** 


        // 7 *** 
            Route::resource('warranties', 'WarrantyController');
            Route::get('warranty-log-file','ArchiveTransactionController@warranties');
            Route::get('warranty-log-file/view/{id}','ArchiveTransactionController@warranties_view');
            Route::get('user-log-file/','ArchiveTransactionController@users_activations');
            Route::get('bill-log-file/','ArchiveTransactionController@transaction_activations');
            Route::get('bill-log-file/show-bill/{id}','ArchiveTransactionController@show_transaction');
            Route::get('bill-log-file/main-bill/{id}','ArchiveTransactionController@show_main');
        // 7 *** 



        // 1 *** 
            Route::resource('dashboard-configurator', 'DashboardConfiguratorController')
                ->only(['edit', 'update']);
        // 1 *** 

        // 1 *** 
            Route::get('view-media/{model_id}', 'SellController@viewMedia');
        // 1 *** 
        
        // 3 *** 
            //common controller for document & note 
            Route::get('get-document-note-page', 'DocumentAndNoteController@getDocAndNoteIndexPage');
            Route::post('post-document-upload', 'DocumentAndNoteController@postMedia');
            Route::resource('note-documents', 'DocumentAndNoteController');
        // 3 *** 
    });
// ***********************************************************************
   
    // 9 ***
        Route::middleware(['EcomApi'])->prefix('api/ecom')->group(function () {
            Route::get('products/{id?}', 'ProductController@getProductsApi');
            /* Route::get('categories', 'CategoryController@getCategoriesApi');*/
            Route::get('brands', 'BrandController@getBrandsApi');
            Route::post('customers', 'ContactController@postCustomersApi');
            Route::get('settings', 'BusinessController@getEcomSettings');
            Route::get('variations', 'ProductController@getVariationsApi');
            Route::post('orders', 'SellPosController@placeOrdersApi');
        });
    // 9 ***
    // 3 ***
        //common route
        Route::middleware(['authIzo'])->group(function () {
            Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
        });
    // 3 ***
    
    // 11 ***
        Route::middleware(['setData',   'authIzo', 'SetSessionData','SetDatabaseConnection', 'language', 'timezone'])->group(function () {
            Route::get('/load-more-notifications', 'HomeController@loadMoreNotifications');
            Route::get('/get-total-unread', 'HomeController@getTotalUnreadNotifications');
            Route::get('/purchases/print/{id}', 'PurchaseController@printInvoice');
            Route::get('/purchases/{id}', 'PurchaseController@show');
            Route::get('/entry/transaction/{id}', 'General\AccountController@transaction');
            Route::get('/sells/{id}', 'SellController@show');
           
            Route::get('/sells/{transaction_id}/print', 'SellPosController@printInvoice')->name('sell.printInvoice');
            Route::get('/sells/invoice-url/{id}', 'SellPosController@showInvoiceUrl');
            Route::get('/show-notification/{id}', 'HomeController@showNotification');
        });
    // 11 ***

    // 9 ***
        /* add by eng mohamed ali
        this route is opend without auth to view product  */
        Route::middleware(['setData',   'authIzo','SetDatabaseConnection', 'SetSessionData', 'AdminSidebarMenu', 'timezone'])->group(function () {
                Route::get('/gallery/gallery', 'ProductGallery@gallery');
                Route::get('/gallery/setting', 'ProductGallery@setting');
                Route::get('/gallery/stock_report', 'ProductGallery@stock_report');
                Route::get('/gallery/stock_report/table', 'ProductGallery@stock_report_table');
                Route::post('/gallery/store', 'ProductGallery@update');
                Route::get('/gallery/export', 'ProductGallery@export');
                Route::get('reports/activity-log', 'ReportController@activityLog');
        });
    // 9 ***

    //  1 ***
        Route::get("/close","CloseController@close");
    //  1 ***

    // 2 ***
        /* local inventory   */
        Route::get('/{slug}','ProductGallery@inventory');
        /* get inventory products with slug  used by ajax*/
        Route::get('/product/slug','ProductGallery@inventory');
    // 2 ***

    // 1 ***
        Route::get('/singlproduct/{id}/{name?}','ProductGallery@singlproduct');
    // 1 ***

    // 1 ***
        //... logout users
        Route::get('/business/settings/log','General\EntriesController@log_out')->name("log_out_user");
    // 1 ***
    // 5 ***
        /*For Qr Code */
        Route::get('reports/delivery/{id}','Report\DeliveryController@index');
        Route::get('reports/receive/{id}','Report\ReceiveController@index');
        Route::get('reports/sell/{id}','Report\SellController@index');
        Route::get('reports/purchase/{id}','Report\PurchaseController@index');
        Route::get('reports/sell-pdf/{id}','Report\SellController@generatePdf');
    // 5 ***
    
    // 4 ***
        //. * * voucher report * * . \\
        Route::get('reports/ex-vh/{id}','Report\ExVoucherController@index');
        Route::get('reports/jv-vh/{id}','Report\JuVoucherController@index');
        Route::get('reports/p-vh/{id}','Report\PayVoucherController@index');
        Route::get('reports/r-vh/{id}','Report\RecVoucherController@index');
    // 4 ***
    
    // 2 ***
        //... * * check report * * ...\\ 
        Route::get('reports/i-ch/{id}','Report\ICheckController@index');
        Route::get('reports/o-ch/{id}','Report\OCheckController@index');
    // 2 ***
    
    // 1 ***
        //... * * refresh all * * ...\\ 
        Route::get('product/item-refresh','ProductController@refreshAll');
    // 1 ***

    
    Route::get('/auth/google-page', 'ApiController\Ecommerce\GoogleController@googlePage');
    Route::get('/auth/google/callback', 'ApiController\Ecommerce\GoogleController@googleCallBack');
    

    
    Route::get('/create-account','IzoUserController@register');

// 2 ***
    

