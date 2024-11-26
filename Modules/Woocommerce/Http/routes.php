<?php

Route::post(
    '/webhook/order-created/{business_id}',
    'Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController@orderCreated'
);
Route::post(
    '/webhook/order-updated/{business_id}',
    'Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController@orderUpdated'
);
Route::post(
    '/webhook/order-deleted/{business_id}',
    'Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController@orderDeleted'
);
Route::post(
    '/webhook/order-restored/{business_id}',
    'Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController@orderRestored'
);

Route::group(['middleware' => ['web', 'SetSessionData', 'auth', 'language', 'timezone', 'AdminSidebarMenu'], 'prefix' => 'woocommerce', 'namespace' => 'Modules\Woocommerce\Http\Controllers'], function () {
    Route::get('/install', 'InstallController@index');
    Route::get('/install/update', 'InstallController@update');
    Route::get('/install/uninstall', 'InstallController@uninstall');
    
    Route::get('/', 'WoocommerceController@index');
    Route::get('/api-settings', 'WoocommerceController@apiSettings');
    Route::post('/update-api-settings', 'WoocommerceController@updateSettings');
    Route::get('/sync-categories', 'WoocommerceController@syncCategories');
    Route::get('/sync-products', 'WoocommerceController@syncProducts');
    Route::get('/sync-log', 'WoocommerceController@getSyncLog');
    Route::get('/sync-orders', 'WoocommerceController@syncOrders');
    Route::post('/map-taxrates', 'WoocommerceController@mapTaxRates');
    Route::get('/view-sync-log', 'WoocommerceController@viewSyncLog');
    Route::get('/get-log-details/{id}', 'WoocommerceController@getLogDetails');
    Route::get('/reset-categories', 'WoocommerceController@resetCategories');
    Route::get('/reset-products', 'WoocommerceController@resetProducts');
    Route::post('/update-products', 'WoocommerceController@updateProductSettings');
    Route::post('/update-sections', 'WoocommerceController@updateSectionSettings');
    Route::post('/contact-sections', 'WoocommerceController@updateContactUsSettings');
    Route::post('/accounts-settings', 'WoocommerceController@updateAccountsSettings');
    Route::get('/products', 'WoocommerceController@productSettings');
    Route::get('/software', 'WoocommerceController@softwarePage');
    Route::get('/software/create', 'WoocommerceController@softwareCreate');
    Route::get('/software/edit/{id}', 'WoocommerceController@softwareEdit');
    Route::post('/software/update/{id}', 'WoocommerceController@softwareUpdate');
    Route::get('/software/del/{id}', 'WoocommerceController@softwareDelete');
    Route::post('/software/save', 'WoocommerceController@softwareSave');
    Route::get('/software/del-image', 'WoocommerceController@deleteImage');
    Route::get('/auth-info', 'WoocommerceController@AuthInfo');
    Route::post('/auth-update-info', 'WoocommerceController@updateAuthImage');
    Route::post('/software-update-top/{id}', 'WoocommerceController@updateSoftwareTop')->name("woocommerce-software");
    Route::get('/sections', 'WoocommerceController@sectionsSettings');
    Route::get('/contacts', 'WoocommerceController@contactSettings');
    Route::get('/accounts', 'WoocommerceController@accountsSettings');
    Route::get('/products/get-pro', 'WoocommerceController@getEProduct');
    Route::get('/sections/all', 'WoocommerceController@getESection');
    Route::get('/modify/top/{id}', 'WoocommerceController@topSection');
    Route::get('/modify/add/{id}', 'WoocommerceController@addECommerce');
    Route::get('/modify/remove/{id}', 'WoocommerceController@removeECommerce');
    Route::get('/sections/contact-us/all', 'WoocommerceController@getEContact');
    Route::get('/sections/contact-us/create', 'WoocommerceController@createContact');
    Route::get('/sections/contact-us/edit/{id}', 'WoocommerceController@editContact');
    Route::post('/sections/contact-us/save', 'WoocommerceController@saveContact');
    Route::post('/sections/contact-us/update/{id}', 'WoocommerceController@updateContact');
    Route::get('/sections/create', 'WoocommerceController@createSection');
    Route::get('/sections/edit/{id}', 'WoocommerceController@editSection');
    Route::post('/sections/save', 'WoocommerceController@saveSections');
    Route::post('/sections/update/{id}', 'WoocommerceController@updateSections');
    Route::post('/sections/del/{id}', 'WoocommerceController@delSection');
    Route::get('/sections/view-in-commerce', 'WoocommerceController@viewInEcm');
    Route::get('/sections/dont-view-in-commerce', 'WoocommerceController@dontViewInEcm');
    Route::get('/website/list', 'WoocommerceController@connectWebsite');
    Route::get('/websites/list/all', 'WoocommerceController@Websites');
    // .1..SOCIAL MEDIA..................................................DONE...
    Route::get('/social/all', 'WoocommerceController@getSocial');
    Route::get('/social/create', 'WoocommerceController@createSocial');
    Route::get('/social/edit/{id}', 'WoocommerceController@editSocial');
    Route::get('/social/del/{id}', 'WoocommerceController@deleteSocial');
    Route::post('/social/save', 'WoocommerceController@saveSocial');
    Route::post('/social/update/{id}', 'WoocommerceController@updateSocial');
    // .....................................................................
    // ..2...BILLS............................................................
    Route::get('/bill/all', 'WoocommerceController@getBill');
    Route::get('/bill/view/{id}', 'WoocommerceController@viewBill');
    Route::get('/bill/edit/{id}', 'WoocommerceController@editBill');
    // .....................................................................
    // ......NAVIGATION BAR...........................................................
    Route::get('/nav/all', 'WoocommerceController@getNav');
    Route::get('/nav/create', 'WoocommerceController@createNav');
    Route::get('/nav/edit/{id}', 'WoocommerceController@editNav');
    Route::get('/nav/del/{id}', 'WoocommerceController@delNav');
    Route::post('/nav/save', 'WoocommerceController@saveNav');
    Route::post('/nav/update/{id}', 'WoocommerceController@updateNav');
    // .....................................................................
    // ..3...FLOATING BAR................................................DONE....
    Route::get('/float/all', 'WoocommerceController@getFloat');
    Route::get('/float/create', 'WoocommerceController@createFloat');
    Route::get('/float/edit/{id}', 'WoocommerceController@editFloat');
    Route::get('/float/del/{id}', 'WoocommerceController@delFloat');
    Route::post('/float/save', 'WoocommerceController@saveFloat');
    Route::post('/float/update/{id}', 'WoocommerceController@updateFloat');
    // .....................................................................
    // ..3...SHOP BY CATEGORY ................................................DONE....
    Route::get('/shop/all', 'WoocommerceController@getShop');
    Route::get('/shop/create', 'WoocommerceController@createShop');
    Route::get('/shop/edit/{id}', 'WoocommerceController@editShop');
    Route::get('/shop/del/{id}', 'WoocommerceController@delShop');
    Route::post('/shop/save', 'WoocommerceController@saveShop');
    Route::post('/shop/update/{id}', 'WoocommerceController@updateShop');
    // .....................................................................
    // ..4...STRIPE...........................................................
    Route::get('/stripe/all', 'WoocommerceController@getStripeApi');
    Route::get('/stripe/edit/{id}', 'WoocommerceController@editStripeApi');
    Route::post('/stripe/update/{id}', 'WoocommerceController@updateStripeApi');
    // .....................................................................
    // ..5...CARTS............................................................
    Route::get('/cart/all', 'WoocommerceController@getCart');
    Route::get('/cart/view/{id}', 'WoocommerceController@viewCart');
    Route::get('/cart/edit/{id}', 'WoocommerceController@editCart');
    // .....................................................................
    // .....................................................................
    // ..6...LOGO............................................................
    Route::get('/logo/page', 'WoocommerceController@getLogo');
    Route::get('/image-crop', "WoocommerceController@imageCrop");
    Route::post('/image-crops', "WoocommerceController@imageCropPost");
    Route::post('/image-crops/float', "WoocommerceController@imageFloatCropPost");
    // ..7...Color......................................................
    Route::post('/color-change', "WoocommerceController@changeColor");
    // // routes/web.php
    // Route::get('/image-crop', 'ImageCropController@index');
    // Route::post('/crop-image', 'ImageCropController@cropImage')->name('crop.image');

    // .....................................................................
    // .....................................................................
});
