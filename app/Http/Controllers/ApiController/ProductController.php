<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Unit;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Transaction;
use App\TransactionPayment;
use App\TransactionSellLine;
use App\ReferenceCount;
use App\Models\Check;
use App\Models\PaymentVoucher;
use App\Models\TransactionDelivery;
use App\Models\DeliveredPrevious;
use App\Models\Entry;
use App\Contact;
use App\Models\User;
use App\Imports\ProductImage;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{

    protected $productUtil;
    protected $transactionUtil;
    protected $moduleUtil;
    protected $payment;

     /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil,PaymentVoucher $payment, BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil     = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil    = $businessUtil;
        $this->moduleUtil      = $moduleUtil;
        $this->payment         = $payment;
 
    }

    // *(1)* SECTION ONE ** 
    // **-**************-** 

        // *1* AUTHENTICATION
        // *** AGT8422
            //*----------------------------------------*\\
            //*------------ Get permission ------------*\\
            //******************************************\\
            public function getPermission(Request $request)
            {
                // $api_token  = request()->input("token");
                // $contact_id = request()->input("contact_id");
                // $api        = substr( $api_token,1);
                // $last_api   = substr( $api_token,1,strlen($api)-1);
                // $token      = $last_api;
                // $user       = User::where("api_token",$last_api)->first();
                $user = User::where("username",$request->username)->first();
                $credentials = $request->only('username', 'password');
                if(!$user || !Hash::check($request->password,$user->password)){
                    return response([
                        "status"  => "403",
                        "message" => "Invalid",
                        "result"  => 0
                    ],403);
                }
                $credentialsnt        =  $request->only(["username","password"]);
                $token                =  Auth::guard('api')->attempt($credentialsnt);
                // if(!$request->ip_device){
                //     abort(403, 'Unauthorized action.');
                // }
                return response()->json([
                    "status"    => 200,
                    // "product"   => $product_last ,
                    "message"   => " All Product Shown ",
                    "token"     => $token,
                    // "count_of_product"  => $count
                ]);
            }
        // **************

        // *2* PRODUCT
        // *** AGT8422
            //*----------------------------------------*\\
            //*------------ Show product  -------------*\\
            //******************************************\\
            public function getProduct(Request $request)
            {
                // $api_token  = request()->input("token");
                // $contact_id = request()->input("contact_id");
                // $api        = substr( $api_token,1);
                // $last_api   = substr( $api_token,1,strlen($api)-1);
                // $token      = $last_api;
                // $user       = User::where("api_token",$last_api)->first();
                // if(!$user){
                //     abort(403, 'Unauthorized action.');
                // }
                $business      = \App\Business::first();
                $page          = $request->query('page', 1);
                $skip          = $request->query('skip', 0);
                $limit         = $request->query('limit', 25);
                $skpp          = ($page-1)*$limit;
                $product       = \App\Product::join("variations as vr","products.id","vr.product_id")
                                                ->leftJoin("categories as ca","products.category_id","ca.id")
                                                ->leftJoin("categories as cas","products.sub_category_id","cas.id")
                                                ->leftJoin("units as unt","products.unit_id","unt.id")
                                                ->leftJoin("brands as br","products.brand_id","br.id")
                                                ->select("products.id"
                                                        ,"products.product_vedio as vedio_name"
                                                        ,"ca.name as main_category"
                                                        ,"cas.name as sub_category"
                                                        ,"br.name as brand"
                                                        ,"unt.actual_name as unit"
                                                        ,"products.sku as code"
                                                        ,"products.name"
                                                        ,"products.product_custom_field1 as height"
                                                        ,"products.weight as weight"
                                                        ,"products.product_custom_field2 as power"
                                                        ,"products.product_custom_field3 as current"
                                                        ,"products.product_custom_field4 as voltage"
                                                        ,"products.product_description as description"
                                                        ,"products.full_description as full_description"
                                                        ,"vr.sell_price_inc_tax as sale_price"
                                                        ,"products.image")
                                                ->skip($skpp)
                                                ->take($limit)
                                                ->where("products.ecommerce",1)
                                                ->get();
                $totalProduct = \App\Product::join("variations as vr","products.id","vr.product_id")->select("products.id","products.product_vedio","products.sku as code","products.name","products.product_description as description","vr.sell_price_inc_tax as sale_price","products.image")->where("ecommerce",1)->count();
                $totalPages   = ceil($totalProduct / $limit);
                // Create pagination URLs for next and previous pages
                $prevPage     = $page > 1 ? $page - 1 : null;
                $nextPage     = $page < $totalPages ? $page + 1 : null;
                $product_last = [];
                // "products.product_vedio as vedio_name",
                foreach($product as $it){
                    $listes      = []; 
                    $listes[]    = ["id"=>2,"title"=>"Weight",  "value" => $it->weight];
                    $listes[]    = ["id"=>1,"title"=>json_decode($business->custom_labels)->product->custom_field_1 , "value" => $it->height];
                    $listes[]    = ["id"=>3,"title"=>json_decode($business->custom_labels)->product->custom_field_2 ,  "value" => $it->power];
                    $listes[]    = ["id"=>4,"title"=>json_decode($business->custom_labels)->product->custom_field_3 ,"value" => $it->current];
                    $listes[]    = ["id"=>5,"title"=>json_decode($business->custom_labels)->product->custom_field_4 ,"value" => $it->voltage];
                    
                    unset($it->weight);
                    unset($it->power);
                    unset($it->current);
                    unset($it->voltage);
                    $prs                              =   json_decode($it);
                    $prs->sale_price                  =   round($prs->sale_price,2);
                    $prs->sepcifications              =   $listes;
                    
                    $contacts_price = TransactionSellLine::orderby("id","desc")->where("product_id",$it->id)->select()->first();
                    if($contacts_price != null){
                        if($it->tax!=null){$tax_rates = \App\TaxRate::where('id', $it->tax)->select(['amount'])->first();$tax_ = $tax_rates->amount ;}else{$tax_ = 0;}
                        $price = ($contacts_price->unit_price*$tax_/100)+($contacts_price->unit_price);                
                    }
                    $product_deatails = \App\ProductVariation::where('product_id', $it->id)
                                            ->with(['variations', 'variations.media'])
                                            ->first();
                    if($it->vedio_name != null || $it->vedio_name != ""){
                            $vedio="";
                            $name_vedio = json_decode($it->vedio_name);
                            foreach($name_vedio as $i){
                                $vedio = \URL::to("storage/app/public/".$i);
                            }
                            if($vedio != "" && $vedio != null){
                                $prs->vedio            =   $vedio;
                            }
                    }
                    $prs->image_url            =   ($prs->image_url != "")? $prs->image_url:"https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/1665px-No-Image-Placeholder.svg.png";

                    $more_image = [];
                    foreach($product_deatails->variations as $variation){
                            foreach($variation->media as $media){
                                $more_image[] = $media->getDisplayUrlAttribute();
                            }
                    }        
                    if(count($more_image)>0){
                        $prs->alter_images            =   $more_image;
                    }
                    $main_token    = $request->header("Authorization");
                    $token         = substr($main_token,7); 
                     
                    if($token != "" && $token != null){
                        $client        = \App\Models\e_commerceClient::where("api_token",$token)->first();
                        if($client){
                            $wishlist      = \App\Models\WishList::where("product_id",$prs->id)->where("client_id",$client->id)->first();
                            if(!empty($wishlist)){
                                $prs->wishlist = true;
                            }else{
                                $prs->wishlist = false;
                            }
                        }else{
                            $prs->wishlist = false;
                        }
                    }else{    
                        $prs->wishlist = false;
                    }
                    $productPrice                     = \App\Product::productPrice($prs);
                    // if($productPrice["before_price"] != 0){
                        $prs->price_before            = round($productPrice["before_price"],2);
                    // }
                    $enter_stock  =  \App\Models\WarehouseInfo::where("product_id",$it->id)->sum("product_qty");

                    $prs->price_after                 = ( $productPrice["after_price"] != 0)?round($productPrice["after_price"] ,2):$prs->sale_price;
                    $prs->stock_qty                   = $enter_stock;
                    $product_last[] = $prs;
                }
                $list                  = [];
                $type                  = \App\Models\Ecommerce::typeProduct();
                $brand                 = \App\Models\Ecommerce::brandProduct();
                $unit                  = \App\Models\Ecommerce::unitProduct();
                $category              = \App\Models\Ecommerce::categoryProduct();
                $subCategory           = \App\Models\Ecommerce::subCategoryProduct();
                $collection            = \App\Models\Ecommerce::collectionProduct();
            
                $ranges = [
                            1 => "( 100 - 500 )",
                            2 => "( 500 - 1000 )",
                            3 => "( 1000 - 5000 )",
                            4 => "( 5000 - 10000 )",
                            5 => "( 10000 - 50000 )",
                ];
                
                $list["filter"]   = [
                    "product_type"         => $type,
                    "product_unit"         => $unit,
                    "product_category"     => $category,
                    "product_sub_category" => $subCategory,
                    "product_brand"        => $brand,
                    "product_price_range"  => $ranges
                ];

                $count = count($product_last);
                return response()->json([
                    "status"        => 200,
                    "items"         => $product_last ,
                    "totalRows"     => $totalProduct,
                    'current_page'  => $page,
                    'last_page'     => $totalPages,
                    'limit'         => 25,
                    'prev_page_url' => $prevPage ? "/api/Ecom/products?page=$prevPage" : null,
                    'next_page_url' => $nextPage ? "/api/Ecom/products?page=$nextPage" : null,
                    "additional"    => $list ,
                    "message"       => " All Product Shown Successfully "

                ]);
            }
            //*----------------------------------------*\\
            //*------------ Show One product  ---------*\\
            //******************************************\\
            public function getOneProduct(Request $request)
            {
                // $api_token  = request()->input("token");
                // $contact_id = request()->input("contact_id");
                // $api        = substr( $api_token,1);
                // $last_api   = substr( $api_token,1,strlen($api)-1);
                // $token      = $last_api;
                // $user       = User::where("api_token",$last_api)->first();
                // if(!$user){
                //     abort(403, 'Unauthorized action.');
                // }
                $id               = $request->query('id');
                $business         = \App\Business::first();
                $product          = \App\Product::join("variations as vr","products.id","vr.product_id")
                                            ->leftJoin("categories as ca","products.category_id","ca.id")
                                            ->leftJoin("categories as cas","products.sub_category_id","cas.id")
                                            ->leftJoin("units as unt","products.unit_id","unt.id")
                                            ->leftJoin("brands as br","products.brand_id","br.id")
                                            ->select("products.id"
                                                    ,"products.product_vedio as vedio_name"
                                                    ,"ca.name as main_category"
                                                    ,"cas.name as sub_category"
                                                    ,"br.name as brand"
                                                    ,"unt.actual_name as unit"
                                                    ,"products.sku as code"
                                                    ,"products.name"
                                                    ,"products.product_custom_field1 as height"
                                                    ,"products.weight as weight"
                                                    ,"products.product_custom_field2 as power"
                                                    ,"products.product_custom_field3 as current"
                                                    ,"products.product_custom_field4 as voltage"
                                                    ,"products.product_description as description"
                                                    ,"products.full_description as full_description"
                                                    ,"vr.sell_price_inc_tax as sale_price"
                                                    ,"products.image")
                                            ->where("products.id",$id)
                                            ->where("products.ecommerce",1)
                                            ->get();
                $product_last     = [];
                $comments         = \App\Models\Ecommerce\WebComment::listGlobalComments($id); 
                $numbers          = \App\Models\Ecommerce\WebComment::listRateComments($id); 
                $related          = \App\Product::related($id); 
                $getStoreFeature  = \App\Models\Ecommerce\StoreFeature::getStoreFeature(); 
                 // "products.product_vedio as vedio_name",
                foreach($product as $it){
                    $listes      = []; 
                    $listes[]    = ["id"=>2,"title"=>"Weight",  "value" => $it->weight];
                    $listes[]    = ["id"=>1,"title"=>json_decode($business->custom_labels)->product->custom_field_1 , "value" => $it->height];
                    $listes[]    = ["id"=>3,"title"=>json_decode($business->custom_labels)->product->custom_field_2 ,  "value" => $it->power];
                    $listes[]    = ["id"=>4,"title"=>json_decode($business->custom_labels)->product->custom_field_3 ,"value" => $it->current];
                    $listes[]    = ["id"=>5,"title"=>json_decode($business->custom_labels)->product->custom_field_4 ,"value" => $it->voltage];
                    
                    
                    unset($it->height);
                    unset($it->weight);
                    unset($it->power);
                    unset($it->current);
                    unset($it->voltage);
                    $prs = json_decode($it);
                    $prs->sale_price                  =   round($prs->sale_price,2);
                    $prs->sepcifications              =   $listes;
                    $contacts_price = TransactionSellLine::orderby("id","desc")->where("product_id",$it->id)->select()->first();
                    if($contacts_price != null){
                        if($it->tax!=null){$tax_rates = \App\TaxRate::where('id', $it->tax)->select(['amount'])->first();$tax_ = $tax_rates->amount ;}else{$tax_ = 0;}
                        $price = ($contacts_price->unit_price*$tax_/100)+($contacts_price->unit_price);                
                    }
                    $product_deatails = \App\ProductVariation::where('product_id', $it->id)
                                            ->with(['variations', 'variations.media'])
                                            ->first();
                    if($it->vedio_name != null || $it->vedio_name != ""){
                            $vedio="";
                            $name_vedio = json_decode($it->vedio_name);
                            foreach($name_vedio as $i){
                                $vedio = \URL::to("storage/app/public/".$i);
                            }
                            if($vedio != "" && $vedio != null){
                                $prs->vedio            =   $vedio;
                            }
                    }
                    $prs->image_url            =   ($prs->image_url != "")? $prs->image_url:"https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/1665px-No-Image-Placeholder.svg.png";

                    $more_image = [];
                    foreach($product_deatails->variations as $variation){
                            foreach($variation->media as $media){
                                $more_image[] = $media->getDisplayUrlAttribute();
                            }
                    }        
                    if(count($more_image)>0){
                        $prs->alter_images            =   $more_image;
                    }
                    $main_token    = $request->header("Authorization");
                    $token         = substr($main_token,7); 
                     
                    if($token != "" && $token != null){
                        $client        = \App\Models\e_commerceClient::where("api_token",$token)->first();
                        if($client){
                            $wishlist      = \App\Models\WishList::where("product_id",$prs->id)->where("client_id",$client->id)->first();
                            if(!empty($wishlist)){
                                $prs->wishlist = true;
                            }else{
                                $prs->wishlist = false;
                            }
                        }else{
                            $prs->wishlist = false;
                        }
                    }else{    
                        $prs->wishlist = false;
                    }
                    $productPrice                     = \App\Product::productPrice($prs); 
                    // if($productPrice["before_price"] != 0){
                        $prs->price_before            = round($productPrice["before_price"],2);
                    // }
                    $prs->price_after                 = ( $productPrice["after_price"] != 0)?round($productPrice["after_price"] ,2):$prs->sale_price;
                    $prs->sale_price                  = ( $productPrice["after_price"] != 0)?round($productPrice["after_price"] ,2):$prs->sale_price;
                    $enter_stock  =  \App\Models\WarehouseInfo::where("product_id",$it->id)->sum("product_qty");
                    $prs->stock_qty                   = $enter_stock;
                    $product_last[] = $prs;
                }
                
                $count = count($product_last);
                return response()->json([
                    "status"           => 200,
                    "items"            => $product_last ,
                    "comments"         => $comments ,
                    "reviews"          => $numbers["numbers"] ,
                    "overall"          => $numbers["rate"] ,
                    "product_related"  => $related ,
                    "store_feature"    => $getStoreFeature ,
                    "message"          => " Product Shown Successfully "

                ]);
            }
            //*----------------------------------------*\\
            //*------- Show product alaa test  --------*\\
            //******************************************\\
            public function getProducts(Request $request)
            {
                // $api_token  = request()->input("token");
                // $contact_id = request()->input("contact_id");
                // $api        = substr( $api_token,1);
                // $last_api   = substr( $api_token,1,strlen($api)-1);
                // $token      = $last_api;
                // $user       = User::where("api_token",$last_api)->first();
                // if(!$user){
                //     abort(403, 'Unauthorized action.');
                // }
                $product = \App\Product::join("variations as vr","products.id","vr.product_id")->select("products.id","products.sku as code","products.name","products.product_description as description","vr.sell_price_inc_tax as sale_price","products.image")->where("ecommerce",1)->get();
                $product_last = [];
                foreach($product as $it){
                    $prs = json_decode($it);
                    
                    $contacts_price = TransactionSellLine::orderby("id","desc")->where("product_id",$it->id)->select()->first();
                    
                    if($contacts_price != null){
                        if($it->tax!=null){$tax_rates = \App\TaxRate::where('id', $it->tax)->select(['amount'])->first();$tax_ = $tax_rates->amount ;}else{$tax_ = 0;}
                        $price = ($contacts_price->unit_price*$tax_/100)+($contacts_price->unit_price);                
                    }
                    // $prs->last_price            =   (isset($price)?doubleval($price):doubleval($it->sale_price));
                    $product_last[] = $prs;
                }
                $count = count($product_last);
                return response()->json([
                    "status"    => 200,
                    "product"   => $product_last ,
                    "message"   => " All Product Shown ",
                    "count_of_product"  => $count

                ]);
            }
            //*----------------------------------------*\\
            //* ------- Save Product From Api -------- *\\
            //******************************************\\
            public function saveProduct(Request $request) {
                    
                    try{
                        DB::beginTransaction();
                        $products_previous = \App\Product::where("name",trim($request->product_name))->first();
                        if(!empty($products_previous)){
                            $output = ['success'   => 0,
                                    'msg'       => " This  Product Name Is Already Exist " ,
                            ]; 
                            return response()->json([
                                "status"   => 405,
                                "message"  => " Not Allowed  ",
                                "output"   => $output

                            ]);
                        }
                        $sku_previous = \App\Product::where("sku",trim($request->product_sku))->first();
                        if(!empty($sku_previous)){
                            $output = ['success'   => 0,
                                    'msg'       => " This  Product Sku Is Already Exist " ,
                            ]; 
                            return response()->json([
                                "status"   => 405,
                                "message"  => " Not Allowed  ",
                                "output"   => $output

                            ]);
                        }
                        $product                            = new \App\Product();
                        $product->business_id               = 1;
                        $product->name                      = $request->product_name;
                        $product->sku                       = $request->product_sku;
                        $product->product_description       = $request->product_description;
                        $product->type                      = "single";
                        $product->unit_id                   = 1;
                        $product->enable_stock              = 1;
                        $product->sub_category_id           = 1;
                        $product->category_id               = 1;
                        $product->image                     = ($this->productUtil->uploadFile($request, 'image', config('constants.product_img_path'), 'image'))?$this->productUtil->uploadFile($request, 'image', config('constants.product_img_path'), 'image'):"";
                        $product->save(); 
                        $product_variation                  = new \App\ProductVariation();
                        $product_variation->product_id      = $product->id;
                        $product_variation->is_dummy        = 1;
                        $product_variation->name            = "DUMMY";
                        $product_variation->save();
                        $variation                          = new \App\Variation();
                        $variation->product_id              = $product->id;
                        $variation->sub_sku                 = $product->sku;
                        $variation->product_variation_id    = $product_variation->id;
                        $variation->default_purchase_price  = 0;
                        $variation->dpp_inc_tax             = 0;
                        $variation->profit_percent          = 25;
                        $variation->default_sell_price      = 0;
                        $variation->sell_price_inc_tax      = $request->product_price;
                        $variation->save();
                        DB::commit();
                        $output = ['success'   => 1,
                                'msg'       => " Added Successfully " ,
                        ];
                        return response()->json([
                                "status"       => 200,
                                "message"      => " Added Successfully ",
                                "output"       => $output,
                                "product_id"   => $product->id
                            ]);
                        
                    }catch(Exception $e){
                        DB::rollBack();
                            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                            \Log::alert($e);
                        $output = ['success'       => 0,
                                        'msg'      => $e
                                    ];
                        return response()->json([
                                        "status"   => 403,
                                        "message"  => " Failed ",
                                        "output"   => $output

                                    ]);
                    }
            }
            //*----------------------------------------*\\
            //* ------- Edit Product From Api -------- *\\
            //******************************************\\
            public function updateProduct(Request $request,$id) {
                try{
                        DB::beginTransaction();
                        $products_previous = \App\Product::where("name",trim($request->product_name))->where("id","!=",$id)->first();
                        if(!empty($products_previous)){
                            $output = ['success'   => 0,
                                    'msg'       => " This  Product Name Is Already Exist " ,
                            ]; 
                            return response()->json([
                                "status"   => 405,
                                "message"  => " Not Allowed  ",
                                "output"   => $output

                            ]);
                        }
                        $sku_previous = \App\Product::where("sku",trim($request->product_sku))->where("id","!=",$id)->first();
                        if(!empty($sku_previous)){
                            $output = ['success'   => 0,
                                    'msg'       => " This Product Sku Is Already Exist " ,
                            ]; 
                            return response()->json([
                                "status"   => 405,
                                "message"  => " Not Allowed  ",
                                "output"   => $output

                            ]);
                        }
                        $product                            = \App\Product::find($id);
                        $product->name                      = $request->product_name;
                        $product->sku                       = $request->product_sku;
                        $product->product_description       = $request->product_description;
                        if($request->image){
                            $product->image                     = ($this->productUtil->uploadFile($request, 'image', config('constants.product_img_path'), 'image'))?$this->productUtil->uploadFile($request, 'image', config('constants.product_img_path'), 'image'):"";
                        }
                        $product->update(); 
                        
                        $variation                          = \App\Variation::where("product_id",$product->id)->first();
                        $variation->sub_sku                 = $product->sku;
                        $variation->sell_price_inc_tax      = $request->product_price;
                        $variation->update();
                        
                        DB::commit();
                        $output = ['success'   => 1,
                                'msg'       => " Updated Successfully " ,
                        ];
                        return response()->json([
                                "status"       => 200,
                                "message"      => " Updated Successfully ",
                                "output"       => $output,
                                "product_id"   => $product->id
                            ]);
                        
                    }catch(Exception $e){
                        DB::rollBack();
                            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                            \Log::alert($e);
                        $output = ['success'       => 0,
                                        'msg'      => $e
                                    ];
                        return response()->json([
                                        "status"   => 403,
                                        "message"  => " Failed ",
                                        "output"   => $output

                                    ]);
                    }

            }
            //*----------------------------------------*\\
            //* ------ delete Product From Api ------- *\\
            //******************************************\\
            public function deleteProduct($id) {
                if($id > 191 ){
                    try{
                        DB::beginTransaction();
                        $product                            = \App\Product::find($id);
                        if(!empty($product)){
                            $product->delete();
                        }
                        
                        $variation                          = \App\Variation::where("product_id",$product->id)->first();
                        if(!empty($variation)){
                            $variation->delete();
                        }
                            
                        
                        $pro_variation                          = \App\ProductVariation::where("product_id",$product->id)->first();
                        if(!empty($pro_variation)){
                            $pro_variation->delete();
                        }
                        DB::commit();
                        $output = ['success'   => 1,
                        'msg'       => " Deleted Successfully " ,
                    ];
                    return response()->json([
                                "status"       => 200,
                                "message"      => " Deleted Successfully ",
                                "output"       => $output,
                            ]);
                            
                        }catch(Exception $e){
                            DB::rollBack();
                            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                            \Log::alert($e);
                            $output = ['success'       => 0,
                            'msg'      => $e
                        ];
                        return response()->json([
                            "status"   => 403,
                            "message"  => " Failed ",
                            "output"   => $output
                            
                        ]);
                    }
                }
                
            }
        // **************

        // *3* PRODUCT RELATIONS
        // *** AGT8422
            //*----------------------------------------*\\
            //*------- Show category    -----  --------*\\
            //******************************************\\
            public function getCategory(Request $request)
            {
                // $api_token  = request()->input("token");
                // $contact_id = request()->input("contact_id");
                // $api        = substr( $api_token,1);
                // $last_api   = substr( $api_token,1,strlen($api)-1);
                // $token      = $last_api;
                // $user       = User::where("api_token",$last_api)->first();
                // if(!$user){
                //     abort(403, 'Unauthorized action.');
                // }
                $page               = $request->query('page', 1);
                $skip               = $request->query('skip', 0);
                $limit              = $request->query('limit', 25);
                $skpp               = ($page-1)*$limit;
                $category           = \App\Category::where('category_type',"product")->select("id","name","parent_id")->skip($skpp)->take($limit)->get();
                $categorytotal      = \App\Category::where('category_type',"product")->count();
                $totalPages         = ceil($categorytotal / $limit);
                // Create pagination URLs for next and previous pages
                $prevPage           = $page > 1 ? $page - 1 : null;
                $nextPage           = $page < $totalPages ? $page + 1 : null;
                $main_categoey_last = [];
                $categoey_last      = [];
                foreach($category as $it){
                    if($it->parent_id == 0){
                        $main_categoey_last[] = ["id"=>$it->id,"name"=>$it->name] ;
                    }else{
                        $categoey_last[] = $it;
                    }
                }
                $count_main = count($main_categoey_last);
                $count_sub  = count($categoey_last);
                return response()->json([
                    "items"      => [ "main" => $main_categoey_last , "sub" => $categoey_last ],
                    "totalRows"  => ["count_main" => $count_main , "count_sub" => $count_sub],
                    'current_page'  => $page,
                    'last_page'     => $totalPages,
                    'limit'         => 25,
                    'prev_page_url' => $prevPage ? "/api/Ecom/category?page=$prevPage" : null,
                    'next_page_url' => $nextPage ? "/api/Ecom/category?page=$nextPage" : null,
                    "message"    => " All Categories Shown Successfully "
                ]);
            }
            //*----------------------------------------*\\
            //*------- Show brand       -----  --------*\\
            //******************************************\\
            public function getBrand(Request $request)
            {
                // $api_token  = request()->input("token");
                // $contact_id = request()->input("contact_id");
                // $api        = substr( $api_token,1);
                // $last_api   = substr( $api_token,1,strlen($api)-1);
                // $token      = $last_api;
                // $user       = User::where("api_token",$last_api)->first();
                // if(!$user){
                //     abort(403, 'Unauthorized action.');
                // }
                $page         = $request->query('page', 1);
                $skip         = $request->query('skip', 0);
                $limit        = $request->query('limit', 25);
                $skpp         = ($page-1)*$limit;
                $brand        = \App\Brands::select("id","name")->skip($skpp)->take($limit)->get();
                $brandtotal   = \App\Brands::count();
                $totalPages   = ceil($brandtotal / $limit);
                // Create pagination URLs for next and previous pages
                $prevPage     = $page > 1 ? $page - 1 : null;
                $nextPage     = $page < $totalPages ? $page + 1 : null;
                $brand_last   = [];
                foreach($brand as $it){
                        $brand_last[] = $it;
                }
                return response()->json([
                    "items"      =>  $brand_last  ,
                    "totalRows"  =>  $brandtotal ,
                    'current_page'  => $page,
                    'last_page'     => $totalPages,
                    'limit'         => 25,
                    'prev_page_url' => $prevPage ? "/api/Ecom/brand?page=$prevPage" : null,
                    'next_page_url' => $nextPage ? "/api/Ecom/brand?page=$nextPage" : null,
                    "message"    => " All Brands Shown Successfully "

                ]);
            }
        // **************
   
    // *(2)* SECTION TWO ** 
    // **-**************-** 

        // *4* DASHBOARD
        // *** AGT8422
            //*----------------------------------------*\\
            //* ------ Get Required From Api   ------- *\\
            //******************************************\\
            public function getRequired(Request $request)  {
                try{
                    DB::beginTransaction();
                    // $feature         = \App\Product::Feature();
                    // $variable        = \App\Product::Variable();
                    $array           = \App\Product::getData();
                    $currency        = "AED";
                    $list            = \App\Models\Ecommerce\StoreFeature::getStoreFeature();
                    $business        = \App\Business::select()->first(); 
                    $metaData        = [
                            "icon"=>$business->ico_url,    
                            "share"=>$business->share_url,    
                            "data"=>"Izo E-commerce It is an online store specialized in selling accounting software, restaurant management software, supermarket software, hair salon software, and point-of-sale (POS) devices and cash registers, along with all related accessories such as printers, barcode scanners, cash drawers, and cameras. It integrates with the Stripe electronic payment gateway.",    
                    ];
                    DB::commit();
                    return response()->json([
                        "status"             => 200,
                        "message"            => "Main Page Access Successfully",
                        // "item"               => $array["item"],
                        "Category"           => $array["Category"], 
                        "Feature"            => $array["Feature"], 
                        "Discount"           => $array["Discount"],
                        "Collection"         => $array["Collection"],
                        "Title"              => $array["Title"],
                        "store_feature"      => $list,
                        "Meta"               => $metaData,
                        "currency"           => $currency,
                    ]); 
                }catch(Exception $e){
                    DB::rollBack();
                    \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                    \Log::alert($e);
                    return response()->json([
                                    "status"   => 403,
                                    "message"  => "Failed To Access",
                                ]);
                }  
            }
       
            //*--------------------------------------*\\
            //* ------------ Footer page   --------- *\\
            // ************************************** \\
            public function getFooterPage(Request $request){
                    try{

                        return response()->json([
                            "status"             => 200,
                            "pages"              => $array["Category"], 
                            "links"              => $array["Feature"], 
                            "Discount"           => $array["Discount"],
                            "Collection"         => $array["Collection"],
                            "Title"              => $array["Title"],
                            "currency"           => $currency,
                            "message"            => "Main Page Access Successfully",
                        ]); 
                    }catch(Exception $e){
                        return response([
                            "status"   => 403,
                            "message"  => __("Failed To Access"),
                        ],403);
                    }
            }
        // ************* 
        
        // *5* CONTACT US SEC
        // *** AGT8422
            //*----------------------------------------*\\
            //* ------ Get Contact us From Api ------- *\\
            //******************************************\\
            public function getContact(Request $request)  {
                try{
                    
                    DB::beginTransaction();
                    // $feature         = \App\Product::Feature();
                    // $variable        = \App\Product::Variable();
                    $array              = \App\Product::getContact();
                    $business_location  = \App\BusinessLocation::first();
                    $social_media       = \App\Models\Ecommerce\SocialMedia::where("business_id",$business_location->business_id)->select(["id","title","link","icon","index_id"])->orderBy("index_id","asc")->get();
                    $list               =  [];
                    $list_              =  [];
                    foreach($social_media as $i){
                        $list_ [] = [
                                "id"    => $i->id,
                                "title" => $i->title,
                                "link"  => $i->link,
                                "icon"  => $i->icon_url,
                        ];
                    }
                    $location           = json_decode($business_location->location_map);
                    foreach($location as $key => $i){
                        if($key == 0){
                            $list["lat"]           = $i;
                        }else{
                            $list["lng"]           = $i;
                        }
                    }
                    $client          = null;
                    $condition       = \App\Models\Ecommerce\Condition::indexCondition($client);
                    $installment     = \App\Models\Ecommerce\Installment::indexInstallment($client);
                    DB::commit();
                    return response()->json([
                        "status"             => 200,
                        "message"            => "Main Page Access Successfully",
                        "Contact-us"         => $array["Contact-us"],
                        "location"           => $list,
                        "social-media"       => $list_,
                        "card"               => $condition,    
                        "installment"        => $installment, 
                    ]); 
                }catch(Exception $e){
                    DB::rollBack();
                    \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                    \Log::alert($e);
                    return response()->json([
                                    "status"   => 403,
                                    "message"  => "Failed To Access",
                                ]);
                }  
            }
            //*----------------------------------------*\\
            //* ------ Get one Contact us From Api --- *\\
            //******************************************\\
            public function getContactOne(Request $request)  {
                try{
                    
                    DB::beginTransaction();
                    // $feature         = \App\Product::Feature();
                    // $variable        = \App\Product::Variable();
                    $id                 = $request->input("id");
                    $array              = \App\Product::getContact($id);
                    DB::commit();
                    return response()->json([
                        "status"             => 200,
                        "message"            => "Contact us Access Successfully",
                        "Contact-us"         => $array["Contact-us"],
                    ]); 
                }catch(Exception $e){
                    DB::rollBack();
                    \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                    \Log::alert($e);
                    return response()->json([
                                    "status"   => 403,
                                    "message"  => "Failed To Access",
                                ]);
                }  
            }
            //*----------------------------------------*\\
            //* ------ Save   Contact   Us     ------- *\\
            //******************************************\\
            public function saveContact(Request $request)  {
                try{
                    // $api_token  = request()->input("token");
                    // $api        = substr( $api_token,1);
                    // $last_api   = substr( $api_token,1,strlen($api)-1);
                    // $token      = $last_api;
                    // $user       = User::where("api_token",$last_api)->first();
                    // if(!$user){
                    //     abort(403, 'Unauthorized action.');
                    // } 
                    $contact_us              = new \App\Models\ContactUs();
                    $contact_us->title       = $request->title;
                    $contact_us->mobile      = $request->contact;
                    $contact_us->view        = 1;
                    $contact_us->index_item  = $request->index;  
                    if($request->hasFile("icon") != null || $request->hasFile("icon") != false){
                        $dir_name =  config('constants.product_img_path');
                        if ($request->file("icon")->getSize() <= config('constants.document_size_limit')) {
                            $new_file_name = time() . '_' . $request->file("icon")->getClientOriginalName();
                            if ($request->file("icon")->storeAs($dir_name, $new_file_name)) {
                                $uploaded_file_name      = $new_file_name;
                                $contact_us->icon              = $uploaded_file_name;
                            }
                        }
                    }
                    $contact_us->save();            

                    $output = ["status"=>200,"msg"=>__('added Successfully')];
                }catch(Exception $e){
                    $output = ["status"=>403,"msg"=>__('Failed')];
                }
                return response([
                    "status"=>$output["status"], 
                    "message"=>$output["msg"]
                ],$output["status"]);
            }
            //*----------------------------------------*\\
            //* ------ Update   Contact Us     ------- *\\
            //******************************************\\
            public function updateContact(Request $request,$id)  {
                try{
                    // $api_token  = request()->input("token");
                    // $api        = substr( $api_token,1);
                    // $last_api   = substr( $api_token,1,strlen($api)-1);
                    // $token      = $last_api;
                    // $user       = User::where("api_token",$last_api)->first();
                    // if(!$user){
                    //     abort(403, 'Unauthorized action.');
                    // } 
                    $contact_us              = \App\Models\ContactUs::find($id);
                    if(!empty($contact_us)){
                            $contact_us->title      = $request->title;            
                            $contact_us->mobile     = $request->contact;            
                            $contact_us->view       = $request->view;  
                            $contact_us->index_item = $request->index;  
                            if($request->hasFile("icon") != null || $request->hasFile("icon") != false){
                                $dir_name =  config('constants.product_img_path');
                                if ($request->file("icon")->getSize() <= config('constants.document_size_limit')) {
                                    $new_file_name = time() . '_' . $request->file("icon")->getClientOriginalName();
                                    if ($request->file("icon")->storeAs($dir_name, $new_file_name)) {
                                        $uploaded_file_name      = $new_file_name;
                                        $contact_us->icon              = $uploaded_file_name;
                                    }
                                }
                            }          
                            $contact_us->update();            
                            $output = ["status"=>200,"msg"=>__('Updated Successfully')];
                    }else{
                            return response([
                                "status"  => 403,
                                "message" => "Invalid Data",
                            ],403);
                    }

                }catch(Exception $e){
                    $output = ["status"=>403,"msg"=>__('Failed')];
                }
                return response([
                    "status"=>$output["status"], 
                    "message"=>$output["msg"]
                ]);
            }
            //*----------------------------------------*\\
            //* ------ Delete Contact   Us     ------- *\\
            //******************************************\\
            public function deleteContact(Request $request,$id)  {
                try{
                    // $api_token  = request()->input("token");
                    // $api        = substr( $api_token,1);
                    // $last_api   = substr( $api_token,1,strlen($api)-1);
                    // $token      = $last_api;
                    // $user       = User::where("api_token",$last_api)->first();
                    // if(!$user){
                    //     abort(403, 'Unauthorized action.');
                    // } 
                    $contact_us              = \App\Models\ContactUs::find($id);
                    if(!empty($contact_us)){
                            $contact_us->delete();            
                            $output = ["status"=>200,"msg"=>__('Deleted Successfully')];
                    }else{
                        $output                  = ["status"=>403,"msg"=>__('Failed Action')];

                        return response([
                            "status" =>$output["status"], 
                            "message"=>$output["msg"]
                        ],$output["status"]);;
                    }

                }catch(Exception $e){
                    $output = ["status"=>403,"msg"=>__('Failed')];
                }
                return response([
                    "status" =>$output["status"], 
                    "message"=>$output["msg"]
                ],$output["status"]);
            }
        // *************

        // *6* ABOUT US SEC
        // *** AG8422
            //*----------------------------------------*\\
            //* ------ Get About us From Api   ------- *\\
            //******************************************\\
            public function getAbout(Request $request)  {
                try{
                    
                    DB::beginTransaction();
                    // $feature         = \App\Product::Feature();
                    // $variable        = \App\Product::Variable();
                    $array              = \App\Product::getAbout();
                    DB::commit();
                    return response()->json([
                        "status"             => 200,
                        "message"            => "Main Page Access Successfully",
                        "About-us"           => $array["About-us"],
                    ]); 
                }catch(Exception $e){
                    DB::rollBack();
                    \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                    \Log::alert($e);
                    return response()->json([
                                    "status"   => 403,
                                    "message"  => "Failed To Access",
                                ],403);
                }  
            }
            //*----------------------------------------*\\
            //* ------ Get one About us From Api ----- *\\
            //******************************************\\
            public function getAboutOne(Request $request)  {
                try{
                    
                    DB::beginTransaction();
                    // $feature         = \App\Product::Feature();
                    // $variable        = \App\Product::Variable();
                    $id                 = $request->input("id");
                    $array              = \App\Product::getAbout($id);
                    DB::commit();
                    return response()->json([
                        "status"             => 200,
                        "message"            => "Main Page Access Successfully",
                        "About-us"           => $array["About-us"],
                    ]); 
                }catch(Exception $e){
                    DB::rollBack();
                    \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                    \Log::alert($e);
                    return response()->json([
                                    "status"   => 403,
                                    "message"  => "Failed To Access",
                                ],403);
                }  
            }
            //*----------------------------------------*\\
            //* ------ Save   About   Us       ------- *\\
            //******************************************\\
            public function saveAbout(Request $request)  {
                try{
                    // $api_token  = request()->input("token");
                    // $api        = substr( $api_token,1);
                    // $last_api   = substr( $api_token,1,strlen($api)-1);
                    // $token      = $last_api;
                    // $user       = User::where("api_token",$last_api)->first();
                    // if(!$user){
                    //     abort(403, 'Unauthorized action.');
                    // } 
                    $E_commerce              = new \App\Models\Ecommerce();
                    $E_commerce->name        = $request->name;
                    if($request->hasFile("image") != null || $request->hasFile("image") != false){
                        $dir_name =  config('constants.product_img_path');
                        if ($request->file("image")->getSize() <= config('constants.document_size_limit')) {
                            $new_file_name = time() . '_' . $request->file("image")->getClientOriginalName();
                            if ($request->file("image")->storeAs($dir_name, $new_file_name)) {
                                $uploaded_file_name      = $new_file_name;
                                $E_commerce->image       = $uploaded_file_name;
                            }
                        }
                    }
                    $E_commerce->title       = $request->title;
                    $E_commerce->desc        = $request->description;
                    $E_commerce->about_us    = 1;
                    $E_commerce->index_item  = $request->index;
                    $E_commerce->view        = 1;
                    $E_commerce->save();            
                    $output                  = ["status"=>200,"msg"=>__('added Successfully')];
                }catch(Exception $e){
                    $output                  = ["status"=>403,"msg"=>__('Failed')];
                }
                return response([
                    "status"  => $output["status"], 
                    "message" => $output["msg"]
                ],$output["status"]);
            }
            //*----------------------------------------*\\
            //* ------ Update   About Us       ------- *\\
            //******************************************\\
            public function updateAbout(Request $request,$id)  {
                try{
                    // $api_token  = request()->input("token");
                    // $api        = substr( $api_token,1);
                    // $last_api   = substr( $api_token,1,strlen($api)-1);
                    // $token      = $last_api;
                    // $user       = User::where("api_token",$last_api)->first();
                    // if(!$user){
                    //     abort(403, 'Unauthorized action.');
                    // } 
                    $E_commerce              = \App\Models\Ecommerce::where("id",$id)->where("about_us",1)->first();
                    if(!empty($E_commerce)){
                            $E_commerce->name        = $request->name;
                            if($request->hasFile("image") != null || $request->hasFile("image") != false){
                                $dir_name =  config('constants.product_img_path');
                                if ($request->file("image")->getSize() <= config('constants.document_size_limit')) {
                                    $new_file_name = time() . '_' . $request->file("image")->getClientOriginalName();
                                    if ($request->file("image")->storeAs($dir_name, $new_file_name)) {
                                        $uploaded_file_name      = $new_file_name;
                                        $E_commerce->image       = $uploaded_file_name;
                                    }
                                }
                            }
                            $E_commerce->title       = $request->title;
                            $E_commerce->desc        = $request->description;
                            $E_commerce->view        = $request->view;
                            $E_commerce->index_item  = $request->index;
                            $E_commerce->update();                    
                            $output                  = ["status"=>200,"msg"=>__('Updated Successfully')];
                    }else{
                        $output                  = ["status"=>403,"msg"=>__('Failed Action')];
                        return response([
                            "status" =>$output["status"], 
                            "message"=>$output["msg"]
                        ],$output["status"]);;
                    }

                }catch(Exception $e){
                    $output = ["status"=>403,"msg"=>__('Failed')];
                }
                return response([
                    "status"=>$output["status"], 
                    "message"=>$output["msg"]
                ],$output["status"]);
            }
            //*----------------------------------------*\\
            //* ------ Update TopSection       ------- *\\
            //******************************************\\
            public function updateTopSection(Request $request)  {
                try{
                    // $api_token  = request()->input("token");
                    // $api        = substr( $api_token,1);
                    // $last_api   = substr( $api_token,1,strlen($api)-1);
                    // $token      = $last_api;
                    // $user       = User::where("api_token",$last_api)->first();
                    // if(!$user){
                    //     abort(403, 'Unauthorized action.');
                    // } 
                    $E_commerce              = \App\Models\Ecommerce::where("about_us",0)->where("store_page",0)->where("subscribe",0)->where("topSection",1)->first();
                    if(!empty($E_commerce)){
                            $E_commerce->name        = $request->name;
                            if($request->hasFile("image") != null || $request->hasFile("image") != false){
                                $dir_name =  config('constants.product_img_path');
                                if ($request->file("image")->getSize() <= config('constants.document_size_limit')) {
                                    $new_file_name = time() . '_' . $request->file("image")->getClientOriginalName();
                                    if ($request->file("image")->storeAs($dir_name, $new_file_name)) {
                                        $uploaded_file_name      = $new_file_name;
                                        $E_commerce->image       = $uploaded_file_name;
                                    }
                                }
                            }
                            $E_commerce->title       = $request->title;
                            $E_commerce->desc        = $request->description;
                            $E_commerce->about_us    = $request->view;
                            $E_commerce->update();                    
                            $output                  = ["status"=>200,"msg"=>__('Updated Successfully')];
                    }else{
                            $output = ["status"=>403,"msg"=>__('Failed UnInvalid Data.')];
                    }

                }catch(Exception $e){
                    $output = ["status"=>403,"msg"=>__('Failed')];
                }
                return response([
                    "status"  => $output["status"], 
                    "message" => $output["msg"]
                ],$output["status"]);
            }
            //*----------------------------------------*\\
            //* ------ Delete About   Us       ------- *\\
            //******************************************\\
            public function deleteAbout(Request $request,$id)  {
                try{
                    // $api_token  = request()->input("token");
                    // $api        = substr( $api_token,1);
                    // $last_api   = substr( $api_token,1,strlen($api)-1);
                    // $token      = $last_api;
                    // $user       = User::where("api_token",$last_api)->first();
                    // if(!$user){
                    //     abort(403, 'Unauthorized action.');
                    // } 
                    $E_commerce              = \App\Models\Ecommerce::where("id",$id)->where("about_us",1)->first();
                    if(!empty($E_commerce)){
                            $E_commerce->delete();            
                            $output = ["status" => 200,"msg"=>__('Deleted Successfully')];
                    }else{
                            $output = ["status" => 403,"msg"=>__('UnInvalid Data')];
                    }
                }catch(Exception $e){
                    $output = ["status"=>403,"msg"=>__('Failed')];
                }
                return response([
                    "status"=>$output["status"], 
                    "message"=>$output["msg"]
                ],$output["status"]);
            }
        // *************
        
        // *7* SUBSCRIBE SEC
        // *** AG8422
            //*----------------------------------------*\\
            //* ------ Get Subscribe           ------- *\\
            //******************************************\\
            public function getSubscribe(Request $request)  {
                try{
                    
                    DB::beginTransaction();
                    // $feature         = \App\Product::Feature();
                    // $variable        = \App\Product::Variable();
                    $array              = \App\Product::getSubscribe();
                    DB::commit();
                    return response()->json([
                        "status"             => 200,
                        "message"            => "Main Page Access Successfully",
                        "Subscribe"          => $array["Subscribe"],
                    ]); 
                }catch(Exception $e){
                    DB::rollBack();
                    \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                    \Log::alert($e);
                    return response()->json([
                                    "status"   => 403,
                                    "message"  => "Failed To Access",
                                ],403);
                }  
            }
            //*----------------------------------------*\\
            //* ------ Save   Subscribe        ------- *\\
            //******************************************\\
            public function saveSubscribe(Request $request)  {
                try{
                    $email                      = trim($request->email);
                    $Check                      = filter_var($email,FILTER_VALIDATE_EMAIL);
                    if(!$Check){
                        $output = ["status"=>403,"msg"=>__('Invalid Email ')];
                         return response([
                            "status"  => $output["status"], 
                            "message" => $output["msg"]
                        ],$output["status"]);
                    }
                    $save                    = \App\Models\e_commerceClient::subscribe($email);
                    if($save == "false"){
                        $output = ["status"=>403,"msg"=>__('Failed Subscribe')];
                    }elseif($save == "exist"){
                        $output = ["status"=>403,"msg"=>__('Faild This Email is already exist')];
                    }else{
                        $output = ["status"=>200,"msg"=>__('Subscribe Successfully')];
                    }
                }catch(Exception $e){
                    $output = ["status"=>403,"msg"=>__('Failed')];
                }
                return response([
                    "status"  => $output["status"], 
                    "message" => $output["msg"]
                ],$output["status"]);
            }
        // *************
        
        // *8* SOCIAL-MEDIA SEC
        // *** AG8422
            //*----------------------------------------*\\
            //* ------ Get Social media        ------- *\\
            //******************************************\\
            public function getSocial(Request $request)  {
                try{
                    
                    DB::beginTransaction();
                    // $feature         = \App\Product::Feature();
                    // $variable        = \App\Product::Variable();
                    $array              = \App\Product::getSocial();
                    DB::commit();
                    return response()->json([
                        "status"             => 200,
                        "message"            => "Social media Access Successfully",
                        "Social_media"       => $array,
                    ]); 
                }catch(Exception $e){
                    DB::rollBack();
                    \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                    \Log::alert($e);
                    return response()->json([
                                    "status"   => 403,
                                    "message"  => "Failed To Access",
                    ],403);
                }  
            }
            //*----------------------------------------*\\
            //* ------ Save   Social media one ------- *\\
            //******************************************\\
            public function getSocialOne(Request $request)  {
                try{
                    
                    DB::beginTransaction();
                    // $feature         = \App\Product::Feature();
                    // $variable        = \App\Product::Variable();
                    $id                 = $request->input("id");
                    $array              = \App\Product::getSocial($id);
                    DB::commit();
                    return response()->json([
                        "status"             => 200,
                        "message"            => "Social media Access Successfully",
                        "Social_media"       => $array,
                    ]); 
                }catch(Exception $e){
                    DB::rollBack();
                    \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                    \Log::alert($e);
                    return response()->json([
                                    "status"   => 403,
                                    "message"  => "Failed To Access",
                    ],403);
                }
            }
        // *************
    
    // *(3)* SECTION THREE ** 
    // **-****************-**
    
        // *7* lOCATION SEC
        // *** AGT8422
            //* ---------------------------------------- *\\
            //* ------------   Location ---------------- *\\
            //********************************************\\
            public function  Location(Request $request)  {
                    try{
                        // $main_token    = $request->header("Authorization");
                        // $token         = substr($main_token,7);
                        // $data["token"] = $token;
                        $check         = \App\Models\e_commerceClient::Location();
                        return  $check;
                    }catch(Exception $e){
                        return response([
                            "status"    => 403,
                            "message"  => __("Invalid data"),
                        ],403);
                    }
                    
                    
                
            }
            //* ---------------------------------------- *\\
            //* ------------ E/Location ---------------- *\\
            //********************************************\\
            public function editLocation(Request $request)  {
                    try{
                        $main_token    = $request->header("Authorization");
                        $token         = substr($main_token,7);
                        $data          = $request->only(["lat","lng","business_id"]);
                        $data["token"] = $token;
                        $check =  \App\Models\e_commerceClient::EditLocation($data);
                        return  $check;
                    }catch(Exception $e){
                        return response([
                            "status"    => 403,
                            "message"  => __("Invalid data"),
                        ],403);
                    }
                    
                    
                
            }
        // *************
        
        // *8* MESSAGE SEC
        // *** AGT8422
            //* ---------------------------------------- *\\
            //* ------------ Send Messages ------------- *\\
            //********************************************\\
            public function sendMessage(Request $request) {
                try{
                    $main_token    = $request->header("Authorization");
                    $token         = substr($main_token,7);
                    $data          = $request->only(["name","phone","email","message"]);
                    $data["token"] = $token;
                    $check         = \App\Models\e_commerceClient::sendMessage($data);
                    return  $check;
                }catch(Exception $e){
                    return response([
                        "status"    => 403,
                        "message"  => __("Invalid data"),
                    ],403);
                }
            }
        // *************
        
        // *9* RATE SEC
        // *** AGT8422    
            //* ---------------------------------------- *\\
            //* ------------ Add  rate     ------------- *\\
            //********************************************\\
            public function addRate(Request $request) {
                try{
                    $main_token    = $request->header("Authorization");
                    $token         = substr($main_token,7);
                    $data          = $request->only(["number_of_stars","comment"]);
                    $data["token"] = $token;
                    $check =  \App\Models\e_commerceClient::addRate($data);
                    return  $check;
                }catch(Exception $e){
                    return response([
                        "status"    => 403,
                        "message"  => __("Invalid data"),
                    ],403);
                }
            }
            //* ---------------------------------------- *\\
            //* ------------ List  rate    ------------- *\\
            //********************************************\\
            public function listRate(Request $request) {
                try{
                    $main_token    = $request->header("Authorization");
                    $token         = substr($main_token,7);
                    $data["token"] = $token;
                    $check =  \App\Models\e_commerceClient::listRate($data);
                    return  $check;
                }catch(Exception $e){
                    return response([
                        "status"    => 403,
                        "message"  => __("Invalid data"),
                    ],403);
                }
            }
        // *************
    
    
    // *(4)* SECTION FOUR ** 
    // **-***************-**
        // *** DATA SECTION  
        // *10* ADDITIONAL INFO
            // 1............ Business    ............ \\
            // ************************************** \\
            public function getBusinessType(Request $request) {
                try{
                    $list   = [];
                    $list[] = [
                         "id"    =>0,    
                         "value" =>"Restaurant"
                    ];    
                    $list[] = [
                         "id"    =>1,    
                         "value" =>"Salon"
                    ];    
                    $list[] = [
                         "id"    =>2,    
                         "value" =>"Commercial"
                    ];    
                    $list[] = [
                         "id"    =>3,    
                         "value" =>"Industrial"
                    ];    
                    $list[] = [
                         "id"    =>4,    
                         "value" =>"Retail"
                    ];    
                    $list[] = [
                         "id"    =>5,    
                         "value" =>"Supermarket"
                    ];    
                          
                     
                    return response([
                        "status"  => 200,
                        "items"   => $list,
                        "message" => __("Access Successfully")
                    ]);
                }catch(Exception $e){
                    return response([
                        "status"   => 403,
                        "message"  => " Failed ", 
                    ],403);
                }
            }
            // 2............ Address    ............ \\
            // ************************************** \\
            public function  getAddressType(Request $request) {
                try{
                    $list   = [];
                    $list[] = [
                         "id"    => 0,    
                         "value" => "Shipping Address"
                    ];    
                    $list[] = [
                         "id"    => 1,    
                         "value" => "Billing Address"
                    ];    
                   
                    return response([
                        "status"  => 200,
                        "items"   => $list,
                        "message" => __("Access Successfully")
                    ]);
                }catch(Exception $e){
                    return response([
                        "status"   => 403,
                        "message"  => " Failed ", 
                    ],403);
                }
            }
            // 3............ Card    ............ \\
            // ************************************** \\
            public function getCardType(Request $request)  {
                try{
                    $list   = [];
                    $list[] = [
                         "id"    => 0,    
                         "value" => "Visa Card"
                    ];    
                    $list[] = [
                         "id"    => 1,    
                         "value" => "Master Card"
                    ];    
                    
                    return response([
                        "status"  => 200,
                        "items"   => $list,
                        "message" => __("Access Successfully")
                    ]);
                }catch(Exception $e){
                    return response([
                        "status"   => 403,
                        "message"  => " Failed ", 
                    ],403);
                }
            }
        // *****
    
        
    // *(5)* SECTION FIVE ** 
    // **-***************-**
    // *11* EXPORT SEC
    // **** AGT8422
        //* ---------------------------------------- *\\
        //* ------------ EXPORT TABLE  ------------- *\\
        //********************************************\\
        public function export(){
            return Excel::download(new UsersExport, 'users.xlsx');
        }
    // **************

    // *12* STORE PAGE
    // **** AGT8422
        //* ---------------------------------------- *\\
        //* ------------ STORE PAGE    ------------- *\\
        //********************************************\\
        public function getStorePage(Request $request) {
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data          = $request->only(["sort",
                                                    "product_type",
                                                    "product_unit",
                                                    "product_category",
                                                    "product_sub_category",
                                                    "product_brand",
                                                    "product_price_range_min",
                                                    "product_price_range_max",
                                                    "Search"
                                                ]);
                $data["token"] = $token;
                $check         = \App\Models\e_commerceClient::getStorePage($data,$request);
                return $check;
            }catch(Exception $e){
                return response([
                    "status"  => 403,
                    "message" => __("Failed Actions"),
                ],403);
            }
        }
    // **************
        



}
