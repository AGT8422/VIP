<?php

use Illuminate\Http\Request;
 
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// :::.................... Login Routes ...............................
// ... post
    Route::post("connection","ApiController\RegisterController@saveApi");
    Route::post("login","ApiController\ApiLoginController@login");
    Route::post("logout","ApiController\ApiLoginController@logout");
    Route::post("save-sale","ApiController\ApiLoginController@store");
    Route::post("app/login","ApiController\RegisterController@login");
    Route::post("app/front/login","ApiController\ApiLoginController@loginFront");
    Route::post("app/react/login","ApiController\RegisterController@loginFront");
    Route::post("app/react/register","ApiController\RegisterController@RegisterBusiness");
    // ... get link
    Route::get("home_login","ApiController\ApiLoginController@getBill");
    Route::get("app/react/currency","ApiController\RegisterController@currency");
    Route::get("app/react/language","ApiController\RegisterController@language");

// :::.................... End Login Routes ..


// :::.................... Sale Routes ................................
    // ... post
    Route::post("sale/add-customer","ApiController\SaleController@addCustomer");
    Route::post("sale/save","ApiController\SaleController@store");
    Route::post("sale/save-return","ApiController\SaleController@storeReturn");
    Route::post("sale/savePayment","ApiController\PaymentVoucherController@savePayment");
    Route::post("sale/saveCheck","ApiController\CheckController@saveCheck");
    Route::post("sale/update{id}","ApiController\SaleController@update");
    Route::post("sale/delete/{id}","ApiController\SaleController@delete");
    // ... get
    Route::get("sale/bill","ApiController\SaleController@index");
    Route::get("sale/customer-balance","ApiController\SaleController@showBalance");
// :::.................... End Sale Routes ..
 

// :::.................... items Routes ................................
    // ... post
    //.....................
    // ... get
    Route::get("sale/user","ApiController\SaleController@user");
    Route::get("sale/customer","ApiController\SaleController@customer");
    Route::get("sale/cost-center","ApiController\SaleController@cost_center");
    Route::get("sale/agent","ApiController\SaleController@agent");
    Route::get("sale/contact_bank","ApiController\SaleController@contact_bank");
    Route::get("sale/inventory","ApiController\SaleController@inventory");
    Route::get("sale/stores","ApiController\SaleController@stores");
    Route::get("sale/product","ApiController\SaleController@product");
    Route::get("sale/statement","ApiController\SaleController@statement");
    Route::get("sale/vat","ApiController\SaleController@vat");
    Route::get("sale/pattern","ApiController\SaleController@pattern");
// :::.................... End Sale Routes ..


// :::.................... Windows Application Routes ................................
    // ... post
    Route::post("user-activation/getcode","ApiController\UserActivateController@needCode");
    //.....................
    // ... get
    Route::get("user-activation/activate-check","ApiController\UserActivateController@codecheck");
// :::.................... End Sale Routes ..
 
 
 
// :::.................... E-commerce Routes ................................
    // ... post
        // ********************* 1 auth
            Route::post("Ecom/admin-login","ApiController\LogInController@ALogIn");
            Route::post("Ecom/admin-logout","ApiController\LogInController@ALogOut");
            Route::post("Ecom/basic/signup","ApiController\SignUpController@SignUp");
            Route::post("Ecom/basic/login","ApiController\LogInController@Login");
            Route::post("Ecom/basic/logout","ApiController\LogInController@logout");
            Route::post("Ecom/refresh-token","ApiController\LogInController@Refresh");
            Route::post("Ecom/forget-password","ApiController\LogInController@forgetPassword");
            Route::post("Ecom/forget-save","ApiController\LogInController@forgetSave");
            Route::post("Ecom/google/login","ApiController\LogInController@loginGoogle");
        // *********************
        // ********************* 2 profile
            Route::post("Ecom/Profile/edit","ApiController\ProfileController@UpdateProfile");
            Route::post("Ecom/Profile/change-password","ApiController\ProfileController@ChangePassword");
            Route::post("Ecom/Profile/delete-my-account","ApiController\ProfileController@DeleteAccount");
                // ********************* 2/1 Card 
                Route::post("Ecom/Profile/payments/create","ApiController\ProfileController@CreatePaymentCard");
                Route::post("Ecom/Profile/payments/edit/{id}","ApiController\ProfileController@UpdatePaymentCard");
                Route::post("Ecom/Profile/payments/del/{id}","ApiController\ProfileController@DeletePaymentCard");
                // ********************* 
                // ********************* 2/2 Addresses
                Route::post("Ecom/Profile/address/create","ApiController\ProfileController@CreateAddress");
                Route::post("Ecom/Profile/address/edit/{id}","ApiController\ProfileController@UpdateAddress");
                Route::post("Ecom/Profile/address/del/{id}","ApiController\ProfileController@DeleteAddress");
                // ********************* 
                // ********************* 2/3 Wishlist
                    Route::post("Ecom/Profile/wishlist/add/{id}","ApiController\ProfileController@AddWishlist");
                    Route::post("Ecom/Profile/wishlist/remove/{id}","ApiController\ProfileController@RemoveWishlist");
                // *********************
                // ********************* 2/4 Orders
                    Route::post("Ecom/Profile/orders/stripe","ApiController\ProfileController@stripe");
                    Route::post("Ecom/Profile/orders/create","ApiController\ProfileController@saveOrder");
                    Route::post("Ecom/Profile/orders/returns/create","ApiController\ProfileController@OrderReturn");
                // *********************
                // ********************* 2/5 Cart
                    Route::post("Ecom/Profile/Cart/create/qty/{id}","ApiController\ProfileController@SaveCartQty");
                    Route::post("Ecom/Profile/Cart/edit/qty/{id}","ApiController\ProfileController@UpdateCartQty");
                    Route::post("Ecom/Profile/Cart/create/{id}","ApiController\ProfileController@SaveCart");
                    Route::post("Ecom/Profile/Cart/edit/{id}","ApiController\ProfileController@UpdateCart");
                    Route::post("Ecom/Profile/Cart/del/{id}","ApiController\ProfileController@DeleteCart");
                    Route::post("Ecom/Profile/condition/create","ApiController\ProfileController@StoreCondition");
                    Route::post("Ecom/Profile/condition/update/{id}","ApiController\ProfileController@UpdateCondition");
                    Route::post("Ecom/Profile/condition/del/{id}","ApiController\ProfileController@DeleteCondition");
                    Route::post("Ecom/Profile/installments/create","ApiController\ProfileController@StoreInstallment");
                    Route::post("Ecom/Profile/installments/update/{id}","ApiController\ProfileController@UpdateInstallment");
                    Route::post("Ecom/Profile/installments/del/{id}","ApiController\ProfileController@DeleteInstallment");
                // *********************
        // *********************
        Route::post("Ecom/getPermission","ApiController\ProductController@getPermission");
        Route::post("Ecom/save-Product","ApiController\ProductController@saveProduct");
        Route::post("Ecom/update-Product/{id}","ApiController\ProductController@updateProduct");
        Route::post("Ecom/delete-Product/{id}","ApiController\ProductController@deleteProduct");
        Route::post("Ecom/save-Contact","ApiController\ProductController@saveContact");
        Route::post("Ecom/update-Contact/{id}","ApiController\ProductController@updateContact");
        Route::post("Ecom/delete-Contact/{id}","ApiController\ProductController@deleteContact");
        Route::post("Ecom/save-About","ApiController\ProductController@saveAbout");
        Route::post("Ecom/update-About/{id}","ApiController\ProductController@updateAbout");
        Route::post("Ecom/delete-About/{id}","ApiController\ProductController@deleteAbout");
        Route::post("Ecom/Last-products","ApiController\ProfileController@LastProduct");
        Route::post("Ecom/Contact-us/location/edit","ApiController\ProductController@editLocation");
        Route::get("Ecom/Contact-us/location","ApiController\ProductController@Location");
        Route::post("Ecom/Contact-us/social-media/create","ApiController\Ecommerce\SocialMediaController@saveLink");
        Route::post("Ecom/Contact-us/social-media/edit/{id}","ApiController\Ecommerce\SocialMediaController@updateLink");
        Route::post("Ecom/Contact-us/social-media/del/{id}","ApiController\Ecommerce\SocialMediaController@deleteLink");
        Route::post("Ecom/Contact-us/message/support","ApiController\ProductController@sendMessage");
        Route::post("Ecom/Subscribe/save","ApiController\ProductController@saveSubscribe");
        Route::post("Ecom/Floating-bar-save","ApiController\Ecommerce\WebBarController@saveFloatingBar");
        Route::post("Ecom/Nav-bar-save","ApiController\Ecommerce\WebBarController@saveNavigationBar");
        Route::post("Ecom/Store-Feature/save","ApiController\Ecommerce\StoreFeatureController@addStoreFeature");
        Route::post("Ecom/Store-Feature/edit/{id}","ApiController\Ecommerce\StoreFeatureController@updateStoreFeature");
        Route::post("Ecom/Store-Feature/del/{id}","ApiController\Ecommerce\StoreFeatureController@deleteStoreFeature");
        Route::post("Ecom/Social-media/save","ApiController\Ecommerce\SocialMediaController@saveLink");
        Route::post("Ecom/Color","ApiController\ProfileController@ChangeColor");
        Route::post("Ecom/Nav-bar/change-align","ApiController\ProfileController@ChangeNavAlign");
        Route::post("Ecom/Floating-bar/change-align","ApiController\ProfileController@ChangeFloatAlign");
        Route::post("Ecom/change-logo","ApiController\ProfileController@ChangeLogo");
        Route::post("Ecom/add-rate","ApiController\ProductController@addRate");
        Route::post("Ecom/Comment-create","ApiController\ProfileController@addComments");
        Route::post("Ecom/Comment-update/{id}","ApiController\ProfileController@updateComments");
        Route::post("Ecom/Comment-delete/{id}","ApiController\ProfileController@deleteComments");
        Route::post("Ecom/Comment-replay/{id}","ApiController\ProfileController@replayComments");
        Route::post("Ecom/Comment/save-emoji/{id}","ApiController\ProfileController@saveEmojiComments");
        Route::post("Ecom/Nav-bar/social-media/create","ApiController\Ecommerce\SocialMediaController@saveLink");
        Route::post("Ecom/Nav-bar/social-media/edit/{id}","ApiController\Ecommerce\SocialMediaController@updateLink");
        Route::post("Ecom/Nav-bar/social-media/del/{id}","ApiController\Ecommerce\SocialMediaController@deleteLink");
        Route::post("Ecom/send-email","ApiController\Ecommerce\SendEmailController@saveEmail");
        Route::post("Ecom/required/title-top","ApiController\ProductController@updateTopSection");
        Route::post("Ecom/all-sections-home/edit","ApiController\Ecommerce\SectionController@editAllSection");
        Route::post("Ecom/all-sections-store/edit","ApiController\Ecommerce\SectionController@editAllSection");
        Route::post("Ecom/all-sections-about/edit","ApiController\Ecommerce\SectionController@editAllSection");
        // ** stripe
        Route::post("Ecom/checkout","ApiController\Ecommerce\CheckoutController@afterPayment")->name("checkout.credit-card");
       
        // *** PRIVACY & TERMS & CONDITIONS
        Route::post("Ecom/terms-condition/save","ApiController\Ecommerce\PrivacyConditionController@StorePrivacyCondition");
        Route::post("Ecom/terms-condition/update/{id}","ApiController\Ecommerce\PrivacyConditionController@UpdatePrivacyCondition");
        Route::post("Ecom/terms-condition/del/{id}","ApiController\Ecommerce\PrivacyConditionController@DeletePrivacyCondition");
        //.....................
    // ... get
        // ********************* 0 Auth
            Route::get("Ecom/basic/signup-image","ApiController\LogInController@AuthImage");
            Route::get("Ecom/basic/login-image","ApiController\LogInController@AuthImage");
        // ********************* 2 profile
            Route::get("Ecom/Profile","ApiController\ProfileController@Profile");
            Route::get("Ecom/Profile/orders/returns","ApiController\ProfileController@GetLastReturn");
            Route::get("Ecom/Profile/orders/returns-all","ApiController\ProfileController@GetLastOrderReturn");
            Route::get("Ecom/Profile/orders/checkout","ApiController\ProfileController@checkout");
            Route::get("Ecom/Profile/address","ApiController\ProfileController@getAddresses");
            Route::get("Ecom/Profile/wishlist","ApiController\ProfileController@getWishlists");
            Route::get("Ecom/Profile/payments","ApiController\ProfileController@getCards");
            Route::get("Ecom/Profile/print","ApiController\ProfileController@OrderPrint");
            Route::get("Ecom/Profile/orders","ApiController\ProfileController@Orders");
            Route::get("Ecom/Profile/orders/movement","ApiController\ProfileController@GetListOrderMovement");
        // *********************
        // ********************* 3 Wishlist
            // Route::get("Ecom/Profile/wishlist","ApiController\ProfileController@getWishlist");
        // *********************
        Route::get("Ecom/products","ApiController\ProductController@getProduct");
        Route::get("Ecom/one-products","ApiController\ProductController@getOneProduct");
        Route::get("Ecom/pro","ApiController\ProductController@getProducts");
        Route::get("Ecom/category","ApiController\ProductController@getCategory");
        Route::get("Ecom/brand","ApiController\ProductController@getBrand");
        Route::get("Ecom/required","ApiController\ProductController@getRequired");
        Route::get("Ecom/Contact_us","ApiController\ProductController@getContact");
        Route::get("Ecom/Contact_us/one","ApiController\ProductController@getContactOne");
        Route::get("Ecom/Social-media","ApiController\ProductController@getSocial");
        Route::get("Ecom/Social-media/one","ApiController\ProductController@getSocialOne");
        Route::get("Ecom/About_us","ApiController\ProductController@getAbout");
        Route::get("Ecom/About_us/one","ApiController\ProductController@getAboutOne");
        Route::get("Ecom/Subscribe","ApiController\ProductController@getSubscribe");
        Route::get("Ecom/Contact-us/social-media","ApiController\Ecommerce\SocialMediaController@getLinks");
        Route::get("Ecom/Floating-bar-all","ApiController\Ecommerce\WebBarController@getListFloatingBar");
        Route::get("Ecom/Nav-bar/all","ApiController\Ecommerce\WebBarController@getListNavigationBar");
        Route::get("Ecom/Store-Feature/list","ApiController\Ecommerce\StoreFeatureController@getStoreFeature");
        Route::get("Ecom/Store-Feature/{id}","ApiController\Ecommerce\StoreFeatureController@getStoreFeatureOne");
        Route::get("Ecom/getColor","ApiController\ProfileController@getColor");
        Route::get("Ecom/Nav-bar/align","ApiController\ProfileController@getNavAlign");
        Route::get("Ecom/Floating-bar/align","ApiController\ProfileController@getFloatAlign");
        Route::get("Ecom/logo","ApiController\ProfileController@getLogo");
        Route::get("Ecom/list-rate","ApiController\ProductController@listRate");
        Route::get("Ecom/Last-products-show","ApiController\ProfileController@GetLastProduct");
        Route::get("Ecom/Comment","ApiController\ProfileController@Comments");
        Route::get("Ecom/Store-page","ApiController\ProductController@getStorePage");
        Route::get("Ecom/Footer-page","ApiController\DashboardController@getFooterPage");
        Route::get("Ecom/business-type","ApiController\ProductController@getBusinessType");
        Route::get("Ecom/address-type","ApiController\ProductController@getAddressType");
        Route::get("Ecom/card-type","ApiController\ProductController@getCardType");
        Route::get("Ecom/business-list","ApiController\Ecommerce\StoreFeatureController@getBusiness");
        Route::get("Ecom/all-sections","ApiController\Ecommerce\SectionController@getAllSection");
        Route::get("Ecom/forget-save/changed","ApiController\LogInController@getChanged");
        Route::get("Ecom/Software-page","ApiController\ProfileController@software");
        Route::get("Ecom/Profile/Cart/Item","ApiController\ProfileController@GetCart");

        // ** stripe
        Route::get("Ecom/checkout","ApiController\Ecommerce\CheckoutController@checkout");
        
        // *** PRIVACY & TERMS & CONDITIONS
        Route::get("Ecom/terms-condition","ApiController\Ecommerce\PrivacyConditionController@PrivacyCondition");
        Route::get("Ecom/terms-condition/create","ApiController\Ecommerce\PrivacyConditionController@CreatePrivacyCondition");
        Route::get("Ecom/terms-condition/edit/{id}","ApiController\Ecommerce\PrivacyConditionController@EditPrivacyCondition");
        Route::get("Ecom/seo/sheet","ApiController\Ecommerce\PrivacyConditionController@SEOSheet");


// :::.................... End E-commerce Routes ..

// :::.................... Export Routes ..
        Route::get("Ecom/Export","ApiController\ProductController@export");
// :::.................... End Export Routes ..


// :::.................... React Routes ................................
    // ... post

        //  *!* Dashboard
            Route::post("app/react/layouts/dashboard/style/save","ApiController\DashboardController@saveStyle");
        //  *1*
        //  *4* Auth
            Route::post("rct/login","ApiController\ApiLoginController@login");
            Route::post("app/front/login","ApiController\RegisterController@loginFront");
            Route::post("app/react/login","ApiController\ApiLoginController@loginFront");
            Route::post("app/react/register","ApiController\RegisterController@RegisterBusiness");
        //  *4*
            
        //  *3* Users
            Route::post("app/react/users/store","ApiController\DashboardController@UsersStore");
            Route::post("app/react/users/update/{id}","ApiController\DashboardController@UsersUpdate");
            Route::post("app/react/users/del/{id}","ApiController\DashboardController@UsersDelete");
        //  *3*
            
        //  *3* Contacts
            Route::post("app/react/contact/save","ApiController\FrontEnd\ContactController@StoreContact");
            Route::post("app/react/contact/update/{id}","ApiController\FrontEnd\ContactController@UpdateContact");
            Route::post("app/react/contact/del/{id}","ApiController\FrontEnd\ContactController@DeleteContact");
            Route::post("app/react/contact/import-file","ApiController\FrontEnd\ContactController@ImportContact");
        //  *3* 
        
        //  *3* Roles
            Route::post("app/react/role/store","ApiController\DashboardController@RolesStore");
            Route::post("app/react/role/update/{id}","ApiController\DashboardController@RolesUpdate");
            Route::post("app/react/role/del/{id}","ApiController\DashboardController@RolesDelete");
        //  *3*
        // ***************************************************************************************************************
        // *3* CustomerGroup
            Route::post("app/react/customer-group/save","ApiController\FrontEnd\CustomerGroupController@CustomerGroupStore");
            Route::post("app/react/customer-group/update/{id}","ApiController\FrontEnd\CustomerGroupController@CustomerGroupUpdate");
            Route::post("app/react/customer-group/del/{id}","ApiController\FrontEnd\CustomerGroupController@CustomerGroupDelete");
        // *3*
        // *3* Brand
            Route::post("app/react/brands/save","ApiController\FrontEnd\BrandController@BrandStore");
            Route::post("app/react/brands/update/{id}","ApiController\FrontEnd\BrandController@BrandUpdate");
            Route::post("app/react/brands/del/{id}","ApiController\FrontEnd\BrandController@BrandDelete");
        // *3*

        // *3* Contact Bank
            Route::post("app/react/contact-bank/save","ApiController\FrontEnd\ContactBankController@ContactBankStore");
            Route::post("app/react/contact-bank/update/{id}","ApiController\FrontEnd\ContactBankController@ContactBankUpdate");
            Route::post("app/react/contact-bank/del/{id}","ApiController\FrontEnd\ContactBankController@ContactBankDelete");
        // *3*

        // *3* Category
            Route::post("app/react/category/save","ApiController\FrontEnd\CategoryController@CategoryStore");
            Route::post("app/react/category/update/{id}","ApiController\FrontEnd\CategoryController@CategoryUpdate");
            Route::post("app/react/category/del/{id}","ApiController\FrontEnd\CategoryController@CategoryDelete");
        // *3*

        // *4* Opening Quantity
            Route::post("app/react/opening-quantity/save","ApiController\FrontEnd\OpeningQuantityController@OpeningQuantityStore");
            Route::post("app/react/opening-quantity/update/{id}","ApiController\FrontEnd\OpeningQuantityController@OpeningQuantityUpdate");
            Route::post("app/react/opening-quantity/del/{id}","ApiController\FrontEnd\OpeningQuantityController@OpeningQuantityDelete");
            Route::post("app/react/opening-quantity/import-file","ApiController\FrontEnd\OpeningQuantityController@importFile");
        // *4*

        // *5* Products
            Route::post("app/react/products/save","ApiController\FrontEnd\ProductController@ProductStore");
            Route::post("app/react/products/unit/save","ApiController\FrontEnd\ProductController@UnitStore");
            Route::post("app/react/products/update/{id}","ApiController\FrontEnd\ProductController@ProductUpdate");
            Route::post("app/react/products/del/{id}","ApiController\FrontEnd\ProductController@ProductDelete");
            Route::post("app/react/products/import-file","ApiController\FrontEnd\ProductController@importFile");
            // Route::get("app/react/products/delete-media/{id}", "ApiController\FrontEnd\ProductController@ProductMediaDelete");
            Route::post("app/react/products/delete-media/{id}", "ApiController\FrontEnd\ProductController@ProductMediaDelete");

        // *5*

        // *3* Patterns
            Route::post("app/react/patterns/save","ApiController\FrontEnd\PatternController@PatternStore");
            Route::post("app/react/patterns/update/{id}","ApiController\FrontEnd\PatternController@PatternUpdate");
            Route::post("app/react/patterns/del/{id}","ApiController\FrontEnd\PatternController@PatternDelete");
        // *3*

        // *5* Sales Price Group
            Route::post("app/react/sales-price-group/save","ApiController\FrontEnd\SalesPriceGroupController@SalesPriceGroupStore");
            Route::post("app/react/sales-price-group/update/{id}","ApiController\FrontEnd\SalesPriceGroupController@SalesPriceGroupUpdate");
            Route::post("app/react/sales-price-group/del/{id}","ApiController\FrontEnd\SalesPriceGroupController@SalesPriceGroupDelete");
            Route::post("app/react/sales-price-group/active/{id}","ApiController\FrontEnd\SalesPriceGroupController@SalesPriceGroupActive");
            Route::post("app/react/sales-price-group/import","ApiController\FrontEnd\SalesPriceGroupController@import");
        // *5*

        // *3* Units
            Route::post("app/react/units/save","ApiController\FrontEnd\UnitController@UnitStore");
            Route::post("app/react/units/update/{id}","ApiController\FrontEnd\UnitController@UnitUpdate");
            Route::post("app/react/units/del/{id}","ApiController\FrontEnd\UnitController@UnitDelete");
            Route::post("app/react/units/default/{id}","ApiController\FrontEnd\UnitController@DefaultUnit");
        // *3*

        // *3* Variation
            Route::post("app/react/variations/save","ApiController\FrontEnd\VariationController@VariationStore");
            Route::post("app/react/variations/update/{id}","ApiController\FrontEnd\VariationController@VariationUpdate");
            Route::post("app/react/variations/del/{id}","ApiController\FrontEnd\VariationController@VariationDelete");
            Route::post("app/react/variations/del/row/{id}","ApiController\FrontEnd\VariationController@VariationRowDelete");
        // *3*
        
        // *3* Warranty
            Route::post("app/react/warranties/save","ApiController\FrontEnd\WarrantyController@WarrantyStore");
            Route::post("app/react/warranties/update/{id}","ApiController\FrontEnd\WarrantyController@WarrantyUpdate");
            Route::post("app/react/warranties/del/{id}","ApiController\FrontEnd\WarrantyController@WarrantyDelete");
        // *3*
    
        
        // *3* Manufacturing recipe
            Route::post("app/react/recipe/save","ApiController\FrontEnd\RecipeController@RecipeStore");
            Route::post("app/react/recipe/update/{id}","ApiController\FrontEnd\RecipeController@RecipeUpdate");
            Route::post("app/react/recipe/del/{id}","ApiController\FrontEnd\RecipeController@RecipeDelete");
        // *3*
            
        // *3* Manufacturing Production
            Route::post("app/react/production/save","ApiController\FrontEnd\ProductionController@ProductionStore");
            Route::post("app/react/production/update/{id}","ApiController\FrontEnd\ProductionController@ProductionUpdate");
            Route::post("app/react/production/del/{id}","ApiController\FrontEnd\ProductionController@ProductionDelete");
        // *3*
        
        // *6* Purchase
            Route::post("app/react/purchase/save","ApiController\FrontEnd\PurchaseController@PurchaseStore");
            Route::post("app/react/purchase/update/{id}","ApiController\FrontEnd\PurchaseController@PurchaseUpdate");
            Route::post("app/react/purchase/del/{id}","ApiController\FrontEnd\PurchaseController@PurchaseDelete");
            Route::post("app/react/purchase/purchase-received/save","ApiController\FrontEnd\PurchaseController@PurchaseReceivedStore");
            Route::post("app/react/purchase/purchase-received/update/{id}","ApiController\FrontEnd\PurchaseController@PurchaseReceivedUpdate");
            Route::post("app/react/purchase/purchase-received/del/{id}","ApiController\FrontEnd\PurchaseController@PurchaseReceivedDelete");
            
        // *6*
        
        // *2* Return Purchase From Old
            Route::post("app/react/return-purchase-old/save/{id}","ApiController\FrontEnd\ReturnPurchaseController@ReturnOldPurchaseStore");
            Route::post("app/react/return-purchase-old/update/{id}","ApiController\FrontEnd\ReturnPurchaseController@ReturnOldPurchaseUpdate");
        // *2* 

        // *6* Return Purchase
            Route::post("app/react/return-purchase/save","ApiController\FrontEnd\ReturnPurchaseController@ReturnPurchaseStore");
            Route::post("app/react/return-purchase/update/{id}","ApiController\FrontEnd\ReturnPurchaseController@ReturnPurchaseUpdate");
            Route::post("app/react/return-purchase/del/{id}","ApiController\FrontEnd\ReturnPurchaseController@ReturnPurchaseDelete");
            Route::post("app/react/return-purchase/purchase-received/save","ApiController\FrontEnd\ReturnPurchaseController@ReturnPurchaseReceivedStore");
            Route::post("app/react/return-purchase/purchase-received/update/{id}","ApiController\FrontEnd\ReturnPurchaseController@ReturnPurchaseReceivedUpdate");
            Route::post("app/react/return-purchase/purchase-received/del/{id}","ApiController\FrontEnd\ReturnPurchaseController@ReturnPurchaseReceivedDelete");
        // *6*

        // *3* Sales
            Route::post("app/react/sales/save","ApiController\FrontEnd\SalesController@SalesStore");
            Route::post("app/react/sales/update/{id}","ApiController\FrontEnd\SalesController@SalesUpdate");
            Route::post("app/react/sales/del/{id}","ApiController\FrontEnd\SalesController@SalesDelete");
        // *3*
        
        // *3* Return Sales
            Route::post("app/react/return-sales/save","ApiController\FrontEnd\ReturnSalesController@ReturnSalesStore");
            Route::post("app/react/return-sales/update/{id}","ApiController\FrontEnd\ReturnSalesController@ReturnSalesUpdate");
            Route::post("app/react/return-sales/del/{id}","ApiController\FrontEnd\ReturnSalesController@ReturnSalesDelete");
        // *3*

        // *3* Voucher
            Route::post("app/react/voucher/save","ApiController\FrontEnd\VoucherController@VoucherStore");
            Route::post("app/react/voucher/update/{id}","ApiController\FrontEnd\VoucherController@VoucherUpdate");
            Route::post("app/react/voucher/del/{id}","ApiController\FrontEnd\VoucherController@VoucherDelete");
        // *3*

        // *3* Journal Voucher
        Route::post("app/react/journal-voucher/save","ApiController\FrontEnd\JournalVoucherController@JournalVoucherStore");
        Route::post("app/react/journal-voucher/update/{id}","ApiController\FrontEnd\JournalVoucherController@JournalVoucherUpdate");
        Route::post("app/react/journal-voucher/del/{id}","ApiController\FrontEnd\JournalVoucherController@JournalVoucherDelete");
        // *3*
        
        // *3* Expense Voucher
        Route::post("app/react/expense-voucher/save","ApiController\FrontEnd\ExpenseVoucherController@ExpenseVoucherStore");
        Route::post("app/react/expense-voucher/update/{id}","ApiController\FrontEnd\ExpenseVoucherController@ExpenseVoucherUpdate");
        Route::post("app/react/expense-voucher/del/{id}","ApiController\FrontEnd\ExpenseVoucherController@ExpenseVoucherDelete");
        // *3*
        
        // *3* Cheque
        Route::post("app/react/cheque/save","ApiController\FrontEnd\ChequeController@ChequeStore");
        Route::post("app/react/cheque/update/{id}","ApiController\FrontEnd\ChequeController@ChequeUpdate");
        Route::post("app/react/cheque/del/{id}","ApiController\FrontEnd\ChequeController@ChequeDelete");
        // *3*

        // *3* Pattern 
        Route::post("app/react/pattern/save","ApiController\FrontEnd\PatternController@PatternStore");
        Route::post("app/react/pattern/update/{id}","ApiController\FrontEnd\PatternController@PatternUpdate");
        Route::post("app/react/pattern/del/{id}","ApiController\FrontEnd\PatternController@PatternDelete");
        // *3*
        
        // *3* System Account 
        Route::post("app/react/system-account/save","ApiController\FrontEnd\SystemAccountController@SystemAccountStore");
        Route::post("app/react/system-account/update/{id}","ApiController\FrontEnd\SystemAccountController@SystemAccountUpdate");
        Route::post("app/react/system-account/del/{id}","ApiController\FrontEnd\SystemAccountController@SystemAccountDelete");
        // *3*
        // *3* Account 
        Route::post("app/react/accounts/save","ApiController\FrontEnd\AccountController@AccountStore");
        Route::post("app/react/accounts/update/{id}","ApiController\FrontEnd\AccountController@AccountUpdate");
        Route::post("app/react/accounts/del/{id}","ApiController\FrontEnd\AccountController@AccountDelete");
        // *3*
    
        // *4* bank and cash Account 
        Route::post("app/react/account-cash/save","ApiController\FrontEnd\AccountController@cashAccountStore");
        Route::post("app/react/account-bank/save","ApiController\FrontEnd\AccountController@bankAccountStore");
        Route::post("app/react/account-cash/update/{id}","ApiController\FrontEnd\AccountController@cashAccountUpdate");
        Route::post("app/react/account-bank/update/{id}","ApiController\FrontEnd\AccountController@bankAccountUpdate");
        Route::post("app/react/account/del/{id}","ApiController\FrontEnd\AccountController@AccountDelete");
        // *4*
        
        // *3*  Account Type 
        Route::post("app/react/account-type/save","ApiController\FrontEnd\AccountTypeController@AccountTypeStore");
        Route::post("app/react/account-type/update/{id}","ApiController\FrontEnd\AccountTypeController@AccountTypeUpdate");
        Route::post("app/react/account-type/del/{id}","ApiController\FrontEnd\AccountTypeController@AccountTypeDelete");
        // *3*

        // *3* Tax rate 
        Route::post("app/react/tax/save","ApiController\FrontEnd\TaxRateController@TaxRateStore");
        Route::post("app/react/tax/update/{id}","ApiController\FrontEnd\TaxRateController@TaxRateUpdate");
        Route::post("app/react/tax/del/{id}","ApiController\FrontEnd\TaxRateController@TaxRateDelete");
        // *3*
        
        // *3* Tax rate group
        Route::post("app/react/tax-group/save","ApiController\FrontEnd\GroupTaxController@GroupTaxStore");
        Route::post("app/react/tax-group/update/{id}","ApiController\FrontEnd\GroupTaxController@GroupTaxUpdate");
        Route::post("app/react/tax-group/del/{id}","ApiController\FrontEnd\GroupTaxController@GroupTaxDelete");
        // *3*
        
        // *6* Warehouse 
        Route::post("app/react/warehouse/save","ApiController\FrontEnd\WarehouseController@WarehouseStore");
        Route::post("app/react/warehouse/update/{id}","ApiController\FrontEnd\WarehouseController@WarehouseUpdate");
        Route::post("app/react/warehouse/del/{id}","ApiController\FrontEnd\WarehouseController@WarehouseDelete");
        
        Route::post("app/react/warehouse/transfer/save","ApiController\FrontEnd\WarehouseTransferController@WarehouseTransferStore");
        Route::post("app/react/warehouse/transfer/update/{id}","ApiController\FrontEnd\WarehouseTransferController@WarehouseTransferUpdate");
        Route::post("app/react/warehouse/transfer/del/{id}","ApiController\FrontEnd\WarehouseTransferController@WarehouseTransferDelete");
        // *6*

        // ***************************************************************************************************************

        //.....................
    
        // ... get
        
        // *3* Dashboard
        Route::get("app/react/dashboard","ApiController\DashboardController@Dashboard");
        Route::get("app/react/layouts/dashboard/style","ApiController\DashboardController@getStyle");
        Route::get("rct/dashboard","ApiController\DashboardController@Dashboard");
        // *3*
        
        // *4* Requirements
            Route::get("app/react/get-user","ApiController\RegisterController@getUser");
            Route::get("app/react/language","ApiController\RegisterController@language");
            Route::get("app/react/currency/all","ApiController\RegisterController@currency");
            Route::get("app/react/currency","ApiController\DashboardController@Currency");
        // *4*
        
        // *4*  Users
            Route::get("app/react/users","ApiController\DashboardController@Users");
            Route::get("app/react/users/create","ApiController\DashboardController@UsersCreate");
            Route::get("app/react/users/edit/{id}","ApiController\DashboardController@UsersEdit");
            Route::get("app/react/users/view/{id}","ApiController\DashboardController@UsersView");
        // *4*

        // *5*  Contacts
            Route::get("app/react/contact/all","ApiController\FrontEnd\ContactController@GetContact");
            Route::get("app/react/contact/supplier","ApiController\FrontEnd\ContactController@GetSupplier");
            Route::get("app/react/contact/customer","ApiController\FrontEnd\ContactController@GetCustomer");
            Route::get("app/react/contact/create","ApiController\FrontEnd\ContactController@CreateContact");
            Route::get("app/react/contact/edit/{id}","ApiController\FrontEnd\ContactController@EditContact");
            Route::get("app/react/contact/view/{id}","ApiController\FrontEnd\ContactController@ViewContact");
            Route::get("app/react/contact/export-file","ApiController\FrontEnd\ContactController@ExportContact");
        // *5*
        


        // *3* Roles
            Route::get("app/react/role","ApiController\DashboardController@Roles");
            Route::get("app/react/role/create","ApiController\DashboardController@RolesCreate");
            Route::get("app/react/role/edit/{id}","ApiController\DashboardController@RolesEdit");
        // *3*
        
        // ***************************************************************************************************************
        // *3* CustomerGroup
            Route::get("app/react/customer-group/all","ApiController\FrontEnd\CustomerGroupController@CustomerGroup");
            Route::get("app/react/customer-group/create","ApiController\FrontEnd\CustomerGroupController@CustomerGroupCreate");
            Route::get("app/react/customer-group/edit/{id}","ApiController\FrontEnd\CustomerGroupController@CustomerGroupEdit");
        // *3*

        // *3* Brand
            Route::get("app/react/brands/all","ApiController\FrontEnd\BrandController@Brand");
            Route::get("app/react/brands/create","ApiController\FrontEnd\BrandController@BrandCreate");
            Route::get("app/react/brands/edit/{id}","ApiController\FrontEnd\BrandController@BrandEdit");
            Route::get("app/react/brands/role/all","ApiController\DashboardController@RolesBy");

        // *3*

        // *3* Brand
            Route::get("app/react/contact-bank/all","ApiController\FrontEnd\ContactBankController@ContactBank");
            Route::get("app/react/contact-bank/create","ApiController\FrontEnd\ContactBankController@ContactBankCreate");
            Route::get("app/react/contact-bank/edit/{id}","ApiController\FrontEnd\ContactBankController@ContactBankEdit");
        // *3*

        // *4* Category
            Route::get("app/react/category/all","ApiController\FrontEnd\CategoryController@Category");
            Route::get("app/react/category/all/tree","ApiController\FrontEnd\CategoryController@CategoryTree");
            Route::get("app/react/category/create","ApiController\FrontEnd\CategoryController@CategoryCreate");
            Route::get("app/react/category/edit/{id}","ApiController\FrontEnd\CategoryController@CategoryEdit");
            Route::get("app/react/category/role/all","ApiController\DashboardController@RolesBy");
        // *4*

        // *5* Opening Quantity
            Route::get("app/react/opening-quantity/all","ApiController\FrontEnd\OpeningQuantityController@OpeningQuantity");
            Route::get("app/react/opening-quantity/create","ApiController\FrontEnd\OpeningQuantityController@OpeningQuantityCreate");
            Route::get("app/react/opening-quantity/export-file","ApiController\FrontEnd\OpeningQuantityController@exportFile");
            Route::get("app/react/opening-quantity/view/{id}","ApiController\FrontEnd\OpeningQuantityController@OpeningQuantityView");
            Route::get("app/react/opening-quantity/edit/{id}","ApiController\FrontEnd\OpeningQuantityController@OpeningQuantityEdit");
            Route::get("app/react/opening-quantity/search-product","ApiController\FrontEnd\OpeningQuantityController@OpeningQuantityProduct");
            Route::get("app/react/opening-quantity/select-product","ApiController\FrontEnd\OpeningQuantityController@OpeningQuantitySelectProduct");
            Route::get("app/react/opening-quantity/last-product","ApiController\FrontEnd\OpeningQuantityController@OpeningQuantityLastProduct");
        // *5*

        // *6* Products
            Route::get("app/react/products/all","ApiController\FrontEnd\ProductController@Product");
            Route::get("app/react/products/view-stock/{id}","ApiController\FrontEnd\ProductController@ProductView");
            Route::get("app/react/products/item-move/{id}","ApiController\FrontEnd\ProductController@itemMove");
            Route::get("app/react/products/create","ApiController\FrontEnd\ProductController@ProductCreate");
            Route::get("app/react/products/unit/create","ApiController\FrontEnd\ProductController@UnitCreate");
            Route::get("app/react/products/edit/{id}","ApiController\FrontEnd\ProductController@ProductEdit");
            Route::get("app/react/products/export-file","ApiController\FrontEnd\ProductController@exportFile");
            Route::get("app/react/products/search-product","ApiController\FrontEnd\ProductController@SearchProduct");
            Route::get("app/react/products/select-product","ApiController\FrontEnd\ProductController@SelectProduct");
            Route::get("app/react/products/role/all","ApiController\DashboardController@RolesBy");

        // *6*

        // *3* Patterns
            Route::post("app/react/patterns/all","ApiController\FrontEnd\PatternController@Pattern");
            Route::post("app/react/patterns/create","ApiController\FrontEnd\PatternController@PatternCreate");
            Route::post("app/react/patterns/edit/{id}","ApiController\FrontEnd\PatternController@PatternEdit");
        // *3*

        // *3* Sales Price Group
            Route::get("app/react/sales-price-group/all","ApiController\FrontEnd\SalesPriceGroupController@SalesPriceGroup");
            Route::get("app/react/sales-price-group/create","ApiController\FrontEnd\SalesPriceGroupController@SalesPriceGroupCreate");
            Route::get("app/react/sales-price-group/edit/{id}","ApiController\FrontEnd\SalesPriceGroupController@SalesPriceGroupEdit");
            Route::get("app/react/sales-price-group/export","ApiController\FrontEnd\SalesPriceGroupController@export");            
            Route::get("app/react/sales-price-group/role/all","ApiController\DashboardController@RolesBy");
        // *3*

        // *3* Units
            Route::get("app/react/units/all","ApiController\FrontEnd\UnitController@Unit");
            Route::get("app/react/units/create","ApiController\FrontEnd\UnitController@UnitCreate");
            Route::get("app/react/units/edit/{id}","ApiController\FrontEnd\UnitController@UnitEdit");
            Route::get("app/react/units/role/all","ApiController\DashboardController@RolesBy");

        // *3*

        // *3* Variation
            Route::get("app/react/variations/all","ApiController\FrontEnd\VariationController@Variation");
            Route::get("app/react/variations/create","ApiController\FrontEnd\VariationController@VariationCreate");
            Route::get("app/react/variations/edit/{id}","ApiController\FrontEnd\VariationController@VariationEdit");
            Route::get("app/react/variations/role/all","ApiController\DashboardController@RolesBy");

        // *3*
        
        // *3* Warranty
            Route::get("app/react/warranties/all","ApiController\FrontEnd\WarrantyController@Warranty");
            Route::get("app/react/warranties/create","ApiController\FrontEnd\WarrantyController@WarrantyCreate");
            Route::get("app/react/warranties/edit/{id}","ApiController\FrontEnd\WarrantyController@WarrantyEdit");
            Route::get("app/react/warranties/role/all","ApiController\DashboardController@RolesBy");
        // *3*
        
        // *3* Inventory
            Route::get("app/react/product-gallery/all","ApiController\FrontEnd\ProductController@ProductGallery");
            Route::get("app/react/inventory-report/all","ApiController\FrontEnd\ProductController@InventoryReport");
        // *3*
        
        // *3* Manufacturing Recipe
            Route::get("app/react/recipe/all","ApiController\FrontEnd\RecipeController@Recipe");
            Route::get("app/react/recipe/create","ApiController\FrontEnd\RecipeController@RecipeCreate");
            Route::get("app/react/recipe/edit/{id}","ApiController\FrontEnd\RecipeController@RecipeEdit");
        // *3*
       
        // *3* Manufacturing Production
            Route::get("app/react/production/all","ApiController\FrontEnd\ProductionController@Production");
            Route::get("app/react/production/create","ApiController\FrontEnd\ProductionController@ProductionCreate");
            Route::get("app/react/production/edit/{id}","ApiController\FrontEnd\ProductionController@ProductionEdit");
        // *3*
        
        // *23* Purchase
            Route::get("app/react/purchase/all","ApiController\FrontEnd\PurchaseController@Purchase");
            Route::get("app/react/purchase/create","ApiController\FrontEnd\PurchaseController@PurchaseCreate");
            Route::get("app/react/purchase/edit/{id}","ApiController\FrontEnd\PurchaseController@PurchaseEdit");
            Route::get("app/react/purchase/supplier","ApiController\FrontEnd\PurchaseController@getSupplier");
            Route::get("app/react/purchase/supplier-select/{id}","ApiController\FrontEnd\PurchaseController@selectSupplier");
            Route::get("app/react/purchase/view/{id}","ApiController\FrontEnd\PurchaseController@PurchaseView");
            Route::get("app/react/purchase/print/{id}","ApiController\FrontEnd\PurchaseController@PurchasePrint");
            Route::get("app/react/purchase/entry/{id}","ApiController\FrontEnd\PurchaseController@PurchaseEntry");
            Route::get("app/react/purchase/map/all","ApiController\FrontEnd\PurchaseController@PurchaseAllMap");
            Route::get("app/react/purchase/map/{id}","ApiController\FrontEnd\PurchaseController@PurchaseMap");
            Route::get("app/react/purchase/add-payment/{id}","ApiController\FrontEnd\PurchaseController@PurchaseAddPayment");
            Route::get("app/react/purchase/update-status/{id}","ApiController\FrontEnd\PurchaseController@PurchaseGetUpdateStatus");
            Route::get("app/react/purchase/update-status-change/{id}","ApiController\FrontEnd\PurchaseController@PurchaseUpdateStatus");
            Route::get("app/react/purchase/view-payment/{id}","ApiController\FrontEnd\PurchaseController@PurchaseViewPayment");
            Route::get("app/react/purchase/search-product","ApiController\FrontEnd\PurchaseController@PurchaseSearchProduct");
            Route::get("app/react/purchase/select-product","ApiController\FrontEnd\PurchaseController@PurchaseSelectProduct");
            Route::get("app/react/purchase/attach/{id}","ApiController\FrontEnd\PurchaseController@PurchaseAttachment");
            Route::get("app/react/purchase/log-file/all","ApiController\FrontEnd\PurchaseController@PurchaseAllLogFile");
            Route::get("app/react/purchase/log-file/{id}","ApiController\FrontEnd\PurchaseController@PurchaseLogFile");
            
            Route::get("app/react/purchase/received/all","ApiController\FrontEnd\PurchaseController@PurchaseAllReceived");
            Route::get("app/react/purchase/purchase-received/create","ApiController\FrontEnd\PurchaseController@PurchaseCreateReceived");
            Route::get("app/react/purchase/purchase-received/edit/{id}","ApiController\FrontEnd\PurchaseController@PurchaseEditReceived");
            Route::get("app/react/purchase/purchase-received/attach/{id}","ApiController\FrontEnd\PurchaseController@PurchaseAttachmentReceived");
            Route::get("app/react/purchase/purchase-received/view/{id}","ApiController\FrontEnd\PurchaseController@PurchaseViewReceived");
            Route::get("app/react/purchase/purchase-received/print/{id}","ApiController\FrontEnd\PurchaseController@PurchasePrintReceived");
            Route::get("app/react/purchase/purchase-received/{id}","ApiController\FrontEnd\PurchaseController@PurchaseReceived");
            
            Route::get("app/react/purchase/supplier-name","ApiController\FrontEnd\PurchaseController@PurchaseCheckContact");
            Route::get("app/react/purchase/supplier-last","ApiController\FrontEnd\PurchaseController@PurchaseLastContact");

        // *23*

        // *2* Return Purchase From Old
            Route::get("app/react/return-purchase-old/create/{id}","ApiController\FrontEnd\ReturnPurchaseController@ReturnOldPurchaseCreate");
            Route::get("app/react/return-purchase-old/edit/{id}","ApiController\FrontEnd\ReturnPurchaseController@ReturnOldPurchaseEdit");
        // *2* 

        // *3* Return Purchase
            Route::get("app/react/return-purchase/all","ApiController\FrontEnd\ReturnPurchaseController@ReturnPurchase");
            Route::get("app/react/return-purchase/create","ApiController\FrontEnd\ReturnPurchaseController@ReturnPurchaseCreate");
            Route::get("app/react/return-purchase/edit/{id}","ApiController\FrontEnd\ReturnPurchaseController@ReturnPurchaseEdit");
            Route::get("app/react/return-purchase/view/{id}","ApiController\FrontEnd\ReturnPurchaseController@ReturnPurchaseView");
            Route::get("app/react/return-purchase/print/{id}","ApiController\FrontEnd\ReturnPurchaseController@ReturnPurchasePrint");
            Route::get("app/react/return-purchase/entry/{id}","ApiController\FrontEnd\ReturnPurchaseController@ReturnPurchaseEntry");
            Route::get("app/react/return-purchase/map/{id}","ApiController\FrontEnd\ReturnPurchaseController@ReturnPurchaseMap");
            Route::get("app/react/return-purchase/add-payment/{id}","ApiController\FrontEnd\ReturnPurchaseController@ReturnPurchaseAddPayment");
            Route::get("app/react/return-purchase/update-status/{id}","ApiController\FrontEnd\ReturnPurchaseController@ReturnPurchaseGetUpdateStatus");
            Route::get("app/react/return-purchase/update-status-change/{id}","ApiController\FrontEnd\ReturnPurchaseController@ReturnPurchaseUpdateStatus");
            Route::get("app/react/return-purchase/view-payment/{id}","ApiController\FrontEnd\ReturnPurchaseController@ReturnPurchaseViewPayment");
            Route::get("app/react/return-purchase/attach/{id}","ApiController\FrontEnd\ReturnPurchaseController@ReturnPurchaseAttachment");

            Route::get("app/react/return-purchase/received/all","ApiController\FrontEnd\ReturnPurchaseController@ReturnPurchaseAllReceived");
            Route::get("app/react/return-purchase/purchase-received/create","ApiController\FrontEnd\ReturnPurchaseController@ReturnPurchaseCreateReceived");
            Route::get("app/react/return-purchase/purchase-received/edit/{id}","ApiController\FrontEnd\ReturnPurchaseController@ReturnPurchaseEditReceived");
            Route::get("app/react/return-purchase/purchase-received/attach/{id}","ApiController\FrontEnd\ReturnPurchaseController@ReturnPurchaseAttachmentReceived");
            Route::get("app/react/return-purchase/purchase-received/view/{id}","ApiController\FrontEnd\ReturnPurchaseController@ReturnPurchaseViewReceived");
            Route::get("app/react/return-purchase/purchase-received/print/{id}","ApiController\FrontEnd\ReturnPurchaseController@ReturnPurchasePrintReceived");
            Route::get("app/react/return-purchase/purchase-received/{id}","ApiController\FrontEnd\ReturnPurchaseController@ReturnPurchaseReceived");

        // *3*

        // *3* Sales
            Route::get("app/react/sales/all","ApiController\FrontEnd\SalesController@Sales");
            Route::get("app/react/sales/create","ApiController\FrontEnd\SalesController@SalesCreate");
            Route::get("app/react/sales/edit/{id}","ApiController\FrontEnd\SalesController@SalesEdit");
        // *3*

        // *3* Return Sales
            Route::get("app/react/return-sales/all","ApiController\FrontEnd\ReturnSalesController@ReturnSales");
            Route::get("app/react/return-sales/create","ApiController\FrontEnd\ReturnSalesController@ReturnSalesCreate");
            Route::get("app/react/return-sales/edit/{id}","ApiController\FrontEnd\ReturnSalesController@ReturnSalesEdit");
        // *3*
        
        // *2*
            Route::get("app/react/approve-quotation/all","ApiController\FrontEnd\SalesController@ApproveQuotation");
            Route::get("app/react/quotation/all","ApiController\FrontEnd\SalesController@Quotation");
            Route::get("app/react/draft/all","ApiController\FrontEnd\SalesController@Draft");
        // *2*

        // *8* Voucher
            Route::get("app/react/voucher/all","ApiController\FrontEnd\VoucherController@Voucher");
            Route::get("app/react/voucher/create","ApiController\FrontEnd\VoucherController@VoucherCreate");
            Route::get("app/react/voucher/edit/{id}","ApiController\FrontEnd\VoucherController@VoucherEdit");
            Route::get("app/react/voucher/bills/{id}","ApiController\FrontEnd\VoucherController@VoucherBills");
            Route::get("app/react/voucher/view/{id}","ApiController\FrontEnd\VoucherController@VoucherView");
            Route::get("app/react/voucher/print/{id}","ApiController\FrontEnd\VoucherController@VoucherPrint");
            Route::get("app/react/voucher/currency/{id}","ApiController\FrontEnd\VoucherController@VoucherCurrency");
            Route::get("app/react/voucher/entry/{id}","ApiController\FrontEnd\VoucherController@VoucherEntry");
            Route::get("app/react/voucher/attach/{id}","ApiController\FrontEnd\VoucherController@VoucherAttachment");
            Route::get("app/react/voucher/bill/view/{id}","ApiController\FrontEnd\VoucherController@VoucherBillView");
        // *8*
            
        // *7* Journal Voucher
            Route::get("app/react/journal-voucher/all","ApiController\FrontEnd\JournalVoucherController@JournalVoucher");
            Route::get("app/react/journal-voucher/create","ApiController\FrontEnd\JournalVoucherController@JournalVoucherCreate");
            Route::get("app/react/journal-voucher/edit/{id}","ApiController\FrontEnd\JournalVoucherController@JournalVoucherEdit");
            Route::get("app/react/journal-voucher/currency/{id}","ApiController\FrontEnd\JournalVoucherController@JournalVoucherCurrency");
            Route::get("app/react/journal-voucher/view/{id}","ApiController\FrontEnd\JournalVoucherController@JournalVoucherView");
            Route::get("app/react/journal-voucher/print/{id}","ApiController\FrontEnd\JournalVoucherController@JournalVoucherPrint");
            Route::get("app/react/journal-voucher/attach/{id}","ApiController\FrontEnd\JournalVoucherController@JournalVoucherAttachment");
            Route::get("app/react/journal-voucher/entry/{id}","ApiController\FrontEnd\JournalVoucherController@JournalVoucherEntry");
            // *7*
            
            // *7* Expense Voucher
            Route::get("app/react/expense-voucher/all","ApiController\FrontEnd\ExpenseVoucherController@ExpenseVoucher");
            Route::get("app/react/expense-voucher/create","ApiController\FrontEnd\ExpenseVoucherController@ExpenseVoucherCreate");
            Route::get("app/react/expense-voucher/edit/{id}","ApiController\FrontEnd\ExpenseVoucherController@ExpenseVoucherEdit");
            Route::get("app/react/expense-voucher/view/{id}","ApiController\FrontEnd\ExpenseVoucherController@ExpenseVoucherView");
            Route::get("app/react/expense-voucher/print/{id}","ApiController\FrontEnd\ExpenseVoucherController@ExpenseVoucherPrint");
            Route::get("app/react/expense-voucher/entry/{id}","ApiController\FrontEnd\ExpenseVoucherController@ExpenseVoucherEntry");
            Route::get("app/react/expense-voucher/attach/{id}","ApiController\FrontEnd\ExpenseVoucherController@ExpenseVoucherAttachment");
            Route::get("app/react/expense-voucher/currency/{id}","ApiController\FrontEnd\ExpenseVoucherController@ExpenseVoucherCurrency");
        // *7*
        
        // *3* Cheque
            Route::get("app/react/cheque/all","ApiController\FrontEnd\ChequeController@Cheque");
            Route::get("app/react/cheque/create","ApiController\FrontEnd\ChequeController@ChequeCreate");
            Route::get("app/react/cheque/edit/{id}","ApiController\FrontEnd\ChequeController@ChequeEdit");
            Route::get("app/react/cheque/bills/{id}","ApiController\FrontEnd\ChequeController@ChequeBills");
            Route::get("app/react/cheque/view/{id}","ApiController\FrontEnd\ChequeController@ChequeView");
            Route::get("app/react/cheque/print/{id}","ApiController\FrontEnd\ChequeController@ChequePrint");
            Route::get("app/react/cheque/currency/{id}","ApiController\FrontEnd\ChequeController@ChequeCurrency");
            Route::get("app/react/cheque/entry/{id}","ApiController\FrontEnd\ChequeController@ChequeEntry");
            Route::get("app/react/cheque/collect/{id}","ApiController\FrontEnd\ChequeController@ChequeCollect");
            Route::get("app/react/cheque/delete-collect/{id}","ApiController\FrontEnd\ChequeController@ChequeDeleteCollect");
            Route::get("app/react/cheque/un-collect/{id}","ApiController\FrontEnd\ChequeController@ChequeUnCollect");
            Route::get("app/react/cheque/refund/{id}","ApiController\FrontEnd\ChequeController@ChequeRefund");
            Route::get("app/react/cheque/attach/{id}","ApiController\FrontEnd\ChequeController@ChequeAttachment");
        // *3*

        // *3* Pattern 
            Route::get("app/react/pattern/all","ApiController\FrontEnd\PatternController@Pattern");
            Route::get("app/react/pattern/create","ApiController\FrontEnd\PatternController@PatternCreate");
            Route::get("app/react/pattern/edit/{id}","ApiController\FrontEnd\PatternController@PatternEdit");
        // *3*

        // *3* System Account 
            Route::get("app/react/system-account/all","ApiController\FrontEnd\SystemAccountController@SystemAccount");
            Route::get("app/react/system-account/create","ApiController\FrontEnd\SystemAccountController@SystemAccountCreate");
            Route::get("app/react/system-account/edit/{id}","ApiController\FrontEnd\SystemAccountController@SystemAccountEdit");
        // *3*


        // *3*  Account 
            Route::get("app/react/accounts/all","ApiController\FrontEnd\AccountController@Account");
            Route::get("app/react/accounts/all/tree","ApiController\FrontEnd\AccountController@AccountTree");
            Route::get("app/react/accounts/create","ApiController\FrontEnd\AccountController@AccountCreate");
            Route::get("app/react/accounts/edit/{id}","ApiController\FrontEnd\AccountController@AccountEdit");
            Route::get("app/react/accounts/ledger/{id}","ApiController\FrontEnd\AccountController@AccountLedger");
        // *3*

        // *3*  Account Reports
            Route::get("app/react/account-report/trialBalance","ApiController\FrontEnd\AccountController@AccountTrialBalance");
            Route::get("app/react/account-report/balanceSheet","ApiController\FrontEnd\AccountController@AccountBalanceSheet");
            Route::get("app/react/account-report/cashFlow","ApiController\FrontEnd\AccountController@AccountCashFlow");
        // *3*

        // *3*  Account Type 
            Route::get("app/react/account-type/all","ApiController\FrontEnd\AccountTypeController@AccountType");
            Route::get("app/react/account-type/create","ApiController\FrontEnd\AccountTypeController@AccountTypeCreate");
            Route::get("app/react/account-type/edit/{id}","ApiController\FrontEnd\AccountTypeController@AccountTypeEdit");
        // *3*

        // *4* bank and cash Account 
            Route::get("app/react/account-cash/all","ApiController\FrontEnd\AccountController@cashAccount");
            Route::get("app/react/account-bank/all","ApiController\FrontEnd\AccountController@bankAccount");
            Route::get("app/react/account-cash/create","ApiController\FrontEnd\AccountController@cashAccountCreate");
            Route::get("app/react/account-bank/create","ApiController\FrontEnd\AccountController@bankAccountCreate");
        // *4*

        // *3* Tax rate 
            Route::get("app/react/tax/all","ApiController\FrontEnd\TaxRateController@TaxRate");
            Route::get("app/react/tax/create","ApiController\FrontEnd\TaxRateController@TaxRateCreate");
            Route::get("app/react/tax/edit/{id}","ApiController\FrontEnd\TaxRateController@TaxRateEdit");
        // *3*

        // *3* Tax rate group
            Route::get("app/react/tax-group/all","ApiController\FrontEnd\GroupTaxController@GroupTax");
            Route::get("app/react/tax-group/create","ApiController\FrontEnd\GroupTaxController@GroupTaxCreate");
            Route::get("app/react/tax-group/edit/{id}","ApiController\FrontEnd\GroupTaxController@GroupTaxEdit");
        // *3*

        // *6* Warehouse 
            Route::get("app/react/warehouse/all","ApiController\FrontEnd\WarehouseController@Warehouse");
            Route::get("app/react/warehouse/create","ApiController\FrontEnd\WarehouseController@WarehouseCreate");
            Route::get("app/react/warehouse/edit/{id}","ApiController\FrontEnd\WarehouseController@WarehouseEdit");
            
            Route::get("app/react/warehouse-movement/{id}","ApiController\FrontEnd\WarehouseController@WarehouseMovement");
            
            Route::get("app/react/warehouse/transfer/all","ApiController\FrontEnd\WarehouseTransferController@WarehouseTransfer");
            Route::get("app/react/warehouse/transfer/create","ApiController\FrontEnd\WarehouseTransferController@WarehouseTransferCreate");
            Route::get("app/react/warehouse/transfer/edit/{id}","ApiController\FrontEnd\WarehouseTransferController@WarehouseTransferEdit");
        // *6*
       
        // *2* Entry
            Route::get("app/react/entry/all","ApiController\FrontEnd\AccountController@Entries");
            Route::get("app/react/entry/view/{id}","ApiController\FrontEnd\AccountController@viewEntry");
        // *2*
        // ***************************************************************************************************************


        // *2* Customer Supplier
            Route::get("app/react/dashboard/customers","ApiController\DashboardController@Customers");
            Route::get("app/react/dashboard/suppliers","ApiController\DashboardController@Suppliers");
        // *2*
        
 
// :::.................... End React Routes ..
 

// :::......................... pos izo
        Route::post("pos/database","ApiController\POS\UploadController@database");
        Route::get("pos/get-database","ApiController\POS\UploadController@getDatabase");
// :::.................................


// ::::......................... menu izo pos
Route::get('izo-pos-menu/product/list','ApiController\IzoMenu\izoMenuController@index');
Route::post('izo-pos-menu/product/save','ApiController\IzoMenu\izoMenuController@store');
Route::put('izo-pos-menu/product/update/{id}','ApiController\IzoMenu\izoMenuController@update');
Route::delete('izo-pos-menu/product/del/{id}','ApiController\IzoMenu\izoMenuController@destroy');
// ::::......................... end menu

