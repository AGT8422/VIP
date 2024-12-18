<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrivacyCondition extends Model
{
    use HasFactory,SoftDeletes;

    protected $append = ["image_url","icon_url","img_url"];

    public function getImgUrlAttribute() {
        $img_url ='';
        if (!empty($this->img)) {
            $img_url = asset('public/uploads/img/' . rawurlencode($this->img));
        } 
        return $img_url;
    }
    public function getIconUrlAttribute() {
        $icon_url ='';
        if (!empty($this->icon)) {
            $icon_url = asset('public/uploads/img/' . rawurlencode($this->icon));
        } 
        return $icon_url;
    }
    public function getImageUrlAttribute() {
        $image_url ='';
        if (!empty($this->image)) {
            $image_url = asset('public/uploads/img/' . rawurlencode($this->image));
        } 
        return $image_url;
    }

    // **** E_COMMERCE PRIVACY
    // *1*INDEX 
    public static function PrivacyCondition($user,$data) {
        
    }
    // *2*CREATE 
    public static function CreatePrivacyCondition($user,$data) {
        
    }
    // *3*EDIT 
    public static function EditPrivacyCondition($user,$data) {
        
    }
    // *4*STORE 
    public static function StorePrivacyCondition($user,$data) {
        
    }
    // *5*UPDATE 
    public static function UpdatePrivacyCondition($user,$data) {
        
    }
    // *6*DELETE 
    public static function DeletePrivacyCondition($user,$data) {
        
    }
    // *6* SHEET 
    public static function SEOSheet($data) {
        switch($data["type"]){
            case "Home":                    
                $title       = "IZO ERP & Accounting Solutions - Software & Hardware For POS Systems .";
                $description = "IZO It is an online store specialized in selling accounting software,restaurant management software, supermarket software, hair salon software,and point-of-sale (POS) devices and cash registers,along with all related accessories such as printers,barcode scanners, cash drawers, and cameras.";
                break; 
            case "About":                   
                $title       = "IZO ERP & Accounting Solutions - Software & Hardware For POS Systems .";
                $description = "IZO It is an online store specialized in selling accounting software,restaurant management software, supermarket software, hair salon software,and point-of-sale (POS) devices and cash registers,along with all related accessories such as printers,barcode scanners, cash drawers, and cameras.";
                break; 
            case "Contact_Us":              
                $title       = "IZO ERP & Accounting Solutions - Software & Hardware For POS Systems .";
                $description = "IZO It is an online store specialized in selling accounting software,restaurant management software, supermarket software, hair salon software,and point-of-sale (POS) devices and cash registers,along with all related accessories such as printers,barcode scanners, cash drawers, and cameras.";
                break; 
            case "All_Products":            
                $title       = "IZO ERP & Accounting Solutions - Software & Hardware For POS Systems .";
                $description = "IZO It is an online store specialized in selling accounting software,restaurant management software, supermarket software, hair salon software,and point-of-sale (POS) devices and cash registers,along with all related accessories such as printers,barcode scanners, cash drawers, and cameras.";
                break; 
            case "Profile":                 
                $title       = "IZO ERP & Accounting Solutions - Software & Hardware For POS Systems .";
                $description = "IZO It is an online store specialized in selling accounting software,restaurant management software, supermarket software, hair salon software,and point-of-sale (POS) devices and cash registers,along with all related accessories such as printers,barcode scanners, cash drawers, and cameras.";
                break; 
            case "Wishlist":                
                $title       = "IZO ERP & Accounting Solutions - Software & Hardware For POS Systems .";
                $description = "IZO It is an online store specialized in selling accounting software,restaurant management software, supermarket software, hair salon software,and point-of-sale (POS) devices and cash registers,along with all related accessories such as printers,barcode scanners, cash drawers, and cameras.";
                break; 
            case "Payment_Info":            
                $title       = "IZO ERP & Accounting Solutions - Software & Hardware For POS Systems .";
                $description = "IZO It is an online store specialized in selling accounting software,restaurant management software, supermarket software, hair salon software,and point-of-sale (POS) devices and cash registers,along with all related accessories such as printers,barcode scanners, cash drawers, and cameras.";
                break; 
            case "Addresses":               
                $title       = "IZO ERP & Accounting Solutions - Software & Hardware For POS Systems .";
                $description = "IZO It is an online store specialized in selling accounting software,restaurant management software, supermarket software, hair salon software,and point-of-sale (POS) devices and cash registers,along with all related accessories such as printers,barcode scanners, cash drawers, and cameras.";
                break; 
            case "Returns":                 
                $title       = "IZO ERP & Accounting Solutions - Software & Hardware For POS Systems .";
                $description = "IZO It is an online store specialized in selling accounting software,restaurant management software, supermarket software, hair salon software,and point-of-sale (POS) devices and cash registers,along with all related accessories such as printers,barcode scanners, cash drawers, and cameras.";
                break; 
            case "My_Orders":               
                $title       = "IZO ERP & Accounting Solutions - Software & Hardware For POS Systems .";
                $description = "IZO It is an online store specialized in selling accounting software,restaurant management software, supermarket software, hair salon software,and point-of-sale (POS) devices and cash registers,along with all related accessories such as printers,barcode scanners, cash drawers, and cameras.";
                break; 
            case "My_Order_Details":        
                $title       = "IZO ERP & Accounting Solutions - Software & Hardware For POS Systems .";
                $description = "IZO It is an online store specialized in selling accounting software,restaurant management software, supermarket software, hair salon software,and point-of-sale (POS) devices and cash registers,along with all related accessories such as printers,barcode scanners, cash drawers, and cameras.";
                break; 
            case "Cart":                    
                $title       = "IZO ERP & Accounting Solutions - Software & Hardware For POS Systems .";
                $description = "IZO It is an online store specialized in selling accounting software,restaurant management software, supermarket software, hair salon software,and point-of-sale (POS) devices and cash registers,along with all related accessories such as printers,barcode scanners, cash drawers, and cameras.";
                break; 
            case "Checkout":                
                $title       = "IZO ERP & Accounting Solutions - Software & Hardware For POS Systems .";
                $description = "IZO It is an online store specialized in selling accounting software,restaurant management software, supermarket software, hair salon software,and point-of-sale (POS) devices and cash registers,along with all related accessories such as printers,barcode scanners, cash drawers, and cameras.";
                break; 
            case "Login":                   
                $title       = "IZO ERP & Accounting Solutions - Software & Hardware For POS Systems .";
                $description = "IZO It is an online store specialized in selling accounting software,restaurant management software, supermarket software, hair salon software,and point-of-sale (POS) devices and cash registers,along with all related accessories such as printers,barcode scanners, cash drawers, and cameras.";
                break; 
            case "SignUp":                  
                $title       = "IZO ERP & Accounting Solutions - Software & Hardware For POS Systems .";
                $description = "IZO It is an online store specialized in selling accounting software,restaurant management software, supermarket software, hair salon software,and point-of-sale (POS) devices and cash registers,along with all related accessories such as printers,barcode scanners, cash drawers, and cameras.";
                break; 
            case "Software_Deals":          
                $title       = "IZO ERP & Accounting Solutions - Software & Hardware For POS Systems .";
                $description = "IZO It is an online store specialized in selling accounting software,restaurant management software, supermarket software, hair salon software,and point-of-sale (POS) devices and cash registers,along with all related accessories such as printers,barcode scanners, cash drawers, and cameras.";
                break; 
            case "Return_Policy":           
                $title       = "IZO ERP & Accounting Solutions - Software & Hardware For POS Systems .";
                $description = "IZO It is an online store specialized in selling accounting software,restaurant management software, supermarket software, hair salon software,and point-of-sale (POS) devices and cash registers,along with all related accessories such as printers,barcode scanners, cash drawers, and cameras.";
                break; 
            case "Privacy_Policy":          
                $title       = "IZO ERP & Accounting Solutions - Software & Hardware For POS Systems .";
                $description = "IZO It is an online store specialized in selling accounting software,restaurant management software, supermarket software, hair salon software,and point-of-sale (POS) devices and cash registers,along with all related accessories such as printers,barcode scanners, cash drawers, and cameras.";
                break; 
            case "Terms_and_Conditions":    
                $title       = "IZO ERP & Accounting Solutions - Software & Hardware For POS Systems .";
                $description = "IZO It is an online store specialized in selling accounting software,restaurant management software, supermarket software, hair salon software,and point-of-sale (POS) devices and cash registers,along with all related accessories such as printers,barcode scanners, cash drawers, and cameras.";
                break; 
            default:                        
                $title       = "IZO ERP & Accounting Solutions - Software & Hardware For POS Systems .";
                $description = "IZO It is an online store specialized in selling accounting software,restaurant management software, supermarket software, hair salon software,and point-of-sale (POS) devices and cash registers,along with all related accessories such as printers,barcode scanners, cash drawers, and cameras.";
                break; 
        }
        return response([
            "title"       => "IZO ERP & Accounting Solutions - Software & Hardware For POS Systems .", 
            "description" => "IZO It is an online store specialized in selling accounting software,restaurant management software, supermarket software, hair salon software,and point-of-sale (POS) devices and cash registers,along with all related accessories such as printers,barcode scanners, cash drawers, and cameras.", 
        ]);
    }

}
