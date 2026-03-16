<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

header('Access-Control-Allow-Origin: *');
header('Content-type: application/json; charset=utf-8');
//include Rest Controller library
require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;
class Vendor_api extends REST_Controller {

    public function __construct() { 
      header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

        parent::__construct();
        
        //load user model
        $this->load->model('vendor');
        //$this->load->library('email'); 
    }

    public function user_post() {
       $userData = array();
       if($this->post('action')=='validate_pincodes')
       {
               $pincode = $this->post('pincode');
               $chk = $this->vendor->validatePincodes($pincode);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       }
       else if($this->post('action')=='login')
       {
              $pincode = $this->post('pincode');
              $username = $this->post('mobile');
              $password = md5($this->post('password'));
              $token = $this->post('token');
               $chk = $this->vendor->checkLogin($pincode,$username,$password,$token);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       }
       else if($this->post('action')=='resend_otp')
       {
               $user_id = $this->post('user_id');
               $chk = $this->vendor->resendOTP($user_id);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       }
       else if($this->post('action')=='forgotpassword')
       {
              $phone = $this->post('phone');
              $type = $this->post('type');
               $chk = $this->vendor->checkForgot($phone,$type);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       }
       else if($this->post('action')=='otp_verification')
       {
              $user_id = $this->post('user_id');
              $otp = $this->post('otp');
              $type = $this->post('type');
               $chk = $this->vendor->verify_OTP($user_id,$otp,$type);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       }

        else if($this->post('action')=='resetPassword')
       {
              $otp = $this->post('otp');
              $password = $this->post('password');
              $phone = $this->post('phone');
              $type = $this->post('type');
               $chk = $this->vendor->resetPassword($phone,$otp,$password,$type);
               if($chk=='error')
               {
                  $this->response($chk, REST_Controller::HTTP_OK);  
               }
               else
               {
                  $this->response($chk, REST_Controller::HTTP_OK);
               }
       }
       else if($this->post('action')=='getProfileDetails')
       {
              $user_id = $this->post('user_id');
              $chk = $this->vendor->getProfile($user_id);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       
       else if($this->post('action')=='getcategories')
       {
             $user_id = $this->post('user_id');
              $chk = $this->vendor->getCategories($user_id);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getsubcategories')
       {
             $cat_id = $this->post('cat_id');
              $chk = $this->vendor->getSubCategories($cat_id);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getbanner')
       {
              $chk = $this->vendor->getBanners();
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getProducts')
       {
              $cat_id = $this->post('cat_id'); 
              $subcat_id = $this->post('subcat_id');
              $shop_id = $this->post('shop_id');
              $chk = $this->vendor->get_Products($cat_id,$subcat_id,$shop_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='addProduct_bk')
       {
              
              
              $variant_product = $this->post('variant_product');
              
              if($this->post('brand')!=''){ $brand = $this->post('brand'); }else{ $brand =0;  }     

              if($this->post('product_tags')!='')
              {
                $producttags=$this->post('product_tags');
              }
              else
              {
                $producttags='';
              }
              
  
              $novar = array('price' =>$this->post('price'),'saleprice' =>$this->post('saleprice'));
                  $name = $this->post('name');

               $seo_url = preg_replace('/[^a-z0-9_-]/i', '', strtolower(str_replace(' ', '-', trim($name))));

       $ar = array(
            'shop_id' =>$this->post('shop_id'),
            'name'=>$this->post('name'),
            'product_title_telugu'=>$this->post('product_title_telugu'),
            'cat_id' => $this->post('cat_id'),
            'sub_cat_id' => $this->post('sub_cat_id'),
            'key_features' =>$this->post('key_features'),
            'descp' =>$this->post('description'),
            //'selling_date' =>$this->post('selling_date'),
            'product_tags' =>$producttags,
            'product_type'=>$this->post('product_type'),
           /* 'meta_tag_title'=>$this->post('meta_tag_title'),
            'meta_tag_description'=>$this->post('meta_tag_description'),
            'meta_tag_keywords'=>$this->post('meta_tag_keywords'),*/
            //'tax_class' =>$this->post('tax_class'),
            'brand'=>$brand,
            //'taxname' =>$taxname,
                'cancel_status' =>'no',
            'return_status' =>'no',
            'variant_product'=>$variant_product,
            'manage_stock'=>'yes',
            'availabile_stock_status'=>$this->post('availabile_stock_status')
            );
              $imagepath=$this->post('imagepath');
       
            
            if($variant_product=='no')
            {
                $chk = $this->vendor->insertProduct($ar,$novar,$imagepath);
                $this->response($chk, REST_Controller::HTTP_OK); 
            }
            else if($variant_product=='yes')
            {
                $chk = $this->vendor->novariant_insertProduct($ar);
                $this->response($chk, REST_Controller::HTTP_OK); 
            }
            
        
       }
       else if($this->post('action')=='get_attributes')
       {
            $shop_id = $this->post('shop_id');
            $chk = $this->vendor->getAttributes($shop_id);
             $this->response($chk, REST_Controller::HTTP_OK); 
       }
       else if($this->post('action')=='get_attributes_wise_values')
       {
            $shop_id = $this->post('shop_id');
            $attribute_id = $this->post('attribute_id');
            $chk = $this->vendor->getShopAttributeValues($shop_id,$attribute_id);
             $this->response($chk, REST_Controller::HTTP_OK); 
       }
       else if($this->post('action')=='getShopWiseSubCategories')
       {
              $shop_id = $this->post('shop_id');
              $chk = $this->vendor->getShopWiseSubCategories($shop_id);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }

       else if($this->post('action')=='upload_image')
       {
           $shop_id = $this->post('shop_id');
            $chk = $this->vendor->uploadImage($shop_id);
            if($chk=='false')
            {
               $ss = array('status'=>false,'message'=>"please upload below 5mb");
            }
            else
            {
              $img = base_url()."uploads/products/".$chk;
              $ss = array('status'=>true,'file'=>$chk,'fullpath'=>$img);
            }
            $this->response($ss, REST_Controller::HTTP_OK);  
       }

       else if($this->post('action')=='addProduct')
       {
              $shop_id = $this->post('shop_id');
              $name = $this->post('product_name');
              $product_title_telugu = $this->post('product_name_telugu');
              $sub_cat_id = $this->post('sub_cat_id');

              $description = $this->post('description');
              $product_tags = $this->post('product_tags');

              $product_type = $this->post('product_type');
              $product_available_in = $this->post('product_available_in');
              $stock_status = $this->post('availabile_stock_status');
              $imagepath = $this->post('imagepath');
  
              if($product_available_in==12)
              {
                $variant = 'no';
              }
              else
              {
                $variant = 'yes';
              }
                 
               $data = array(
                  'shop_id' =>$shop_id,
                  'name'=>$name,
                  'product_title_telugu'=>$product_title_telugu,
                  //'cat_id' => $this->post('cat_id'),
                  'sub_cat_id' => $sub_cat_id,
                  'descp' =>$description,
                  'product_tags' =>$product_tags,
                  'product_type'=>$product_type,
                      'cancel_status' =>'no',
                  'return_status' =>'no',
                  'variant_product'=>$variant,
                  'manage_stock'=>'yes',
                  'availabile_stock_status'=>$stock_status
                  ); 

                      $variant_json_data = $this->post('variant_json_data');       

               $chk = $this->vendor->insertProduct($data,$imagepath,$variant_json_data);

                $this->response($chk, REST_Controller::HTTP_OK); 
        
       }
       else if($this->post('action')=='updateProduct')
       {
              $product_id = $this->post('product_id');
              $shop_id = $this->post('shop_id');
              $name = $this->post('product_name');
              $product_title_telugu = $this->post('product_name_telugu');
              $sub_cat_id = $this->post('sub_cat_id');

              $description = $this->post('description');
              $product_tags = $this->post('product_tags');

              $product_type = $this->post('product_type');
              $product_available_in = $this->post('product_available_in');
              $stock_status = $this->post('availabile_stock_status');
             // $imagepath = $this->post('imagepath');
  
              if($product_available_in==12)
              {
                $variant = 'no';
              }
              else
              {
                $variant = 'yes';
              }
                 
               $data = array(
                  'shop_id' =>$shop_id,
                  'name'=>$name,
                  'product_title_telugu'=>$product_title_telugu,
                  //'cat_id' => $this->post('cat_id'),
                  'sub_cat_id' => $sub_cat_id,
                  'descp' =>$description,
                  'product_tags' =>$product_tags,
                  'product_type'=>$product_type,
                      'cancel_status' =>'no',
                  'return_status' =>'no',
                  'variant_product'=>$variant,
                  'manage_stock'=>'yes',
                  'availabile_stock_status'=>$stock_status
                  ); 

                      $variant_json_data = $this->post('variant_json_data');       

               $chk = $this->vendor->updateProduct($product_id,$data,$variant_json_data);

                $this->response($chk, REST_Controller::HTTP_OK); 
       }
       else if($this->post('action')=='getProductDetails')
       {
              $product_id = $this->post('product_id');
              $chk = $this->vendor->getProductDetails($product_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getAttributeTypes')
       {
              $product_id = $this->post('product_id');
              $chk = $this->vendor->attributeTypes($product_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getAttributeValues')
       {
              $product_id = $this->post('product_id');
              $attribute_type_id = $this->post('attribute_type_id');
              $chk = $this->vendor->AttributeValues($product_id,$attribute_type_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='addvariant')
       {
              $product_id = $this->post('product_id');
              $attribute_type_id = $this->post('attribute_type_id');
              $attribute_value_ids = $this->post('attribute_value_ids');
              $chk = $this->vendor->addVariant($product_id,$attribute_type_id,$attribute_value_ids);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='upload_productImage')
       {
           $product_id = $this->post('product_id');
            $chk = $this->vendor->browse_file($product_id);
            if($chk=='false')
            {
               $ss = array('status'=>false,'message'=>"please upload below 5mb");
            }
            else
            {
              $chk1=$chk['image'];
              $image_id=$chk['image_id'];
              $img = base_url()."uploads/products/".$chk1;
              $ss = array('status'=>true,'file'=>$chk1,'fullpath'=>$img,'image_id'=>$image_id);
            }
            $this->response($ss, REST_Controller::HTTP_OK);  
       }
       
       else if($this->post('action')=='getvariantList')
       {
              $product_id = $this->post('product_id');
              
              $chk = $this->vendor->getVariantsList($product_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
        else if($this->post('action')=='getProductImages')
       {
              $product_id = $this->post('product_id');
              $variant_id = $this->post('variant_id');
              $chk = $this->vendor->getProductImages($product_id,$variant_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='deleteProductImages')
       {
              $variant_id = $this->post('image_id');
              $chk = $this->vendor->deleteProductImages($variant_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
        else if($this->post('action')=='updatePrice')
       {
              $product_id = $this->post('product_id');
              $variant_id = $this->post('variant_id');
              $price = $this->post('price');
              $saleprice = $this->post('saleprice');
              $stock = $this->post('stock');
              $wr = array('id'=>$variant_id);
              $ar = array('product_id'=>$product_id,'price'=>$price,'saleprice'=>$saleprice,'stock'=>$stock);
              $chk = $this->vendor->updatePrice($ar,$wr);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='addStock')
       {
              $product_id = $this->post('product_id');
              $variant_id = $this->post('variant_id');
              $quantity = $this->post('quantity');
              $stockstatus = $this->post('stockstatus');
              $chk = $this->vendor->addStock($product_id,$variant_id,$quantity,$stockstatus);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='stockManagement')
       {
              $product_id = $this->post('pid');
              $variant_id = $this->post('variant_id');
              
              $chk = $this->vendor->getstockManagement($product_id,$variant_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }

       else if($this->post('action')=='shops_list')
       {
              $user_id = $this->post('user_id');
              
              $chk = $this->vendor->shopsList($user_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getproductTags')
       {
              $chk = $this->vendor->getProductTags();
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getBrands')
       {
              $chk = $this->vendor->getBrandslist();
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='gettaxList')
       {
              $chk = $this->vendor->getTaxList();
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getSingleProduct')
       {
              $pid = $this->post('pid');
              $chk = $this->vendor->getSingleProduct($pid);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getaddvariantList')
       {
              $product_id =  $this->post('product_id');
              $chk = $this->vendor->getAddVariantList($product_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='updatevariant')
       {
              $product_id = $this->post('product_id');
               $vid = $this->post('vid');
              $attribute_type_id = $this->post('attribute_type_id');
              $attribute_value_ids = $this->post('attribute_value_ids');
              $chk = $this->vendor->updateVariant($product_id,$attribute_type_id,$attribute_value_ids,$vid);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='delete_variant')
       {
              $product_id =  $this->post('product_id');
              $vid =  $this->post('vid');
              $chk = $this->vendor->deleteVariant($product_id,$vid);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getlink_variants')
       {
              $product_id =  $this->post('product_id');
              $chk = $this->vendor->getLinkVariants($product_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='changeStatus')
       {
              $status =  $this->post('status');
              $vid =  $this->post('vid');
              $chk = $this->vendor->changevariantStatus($vid,$status);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='changePassword')
       {
           $login_type =  $this->post('login_type');
           $user_id =  $this->post('user_id');
           $current_password =  $this->post('current_password');
           $new_password =  $this->post('new_password');

           $chk = $this->vendor->updatePassword($user_id,$current_password,$new_password);
           $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='termsandconditions')
       {
             $status =  $this->post('status');
             $chk = $this->vendor->getTerms($status);
             $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getPendingOrdersList')
       {
             $vendor_id =  $this->post('vendor_id');
             $chk = $this->vendor->fetchOrdersList($vendor_id);
             $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getOngoingOrdersList')
       {
             $vendor_id =  $this->post('vendor_id');
             $chk = $this->vendor->fetchProcessingOrdersList($vendor_id);
             $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getTransmitOrdersList')
       {
             $vendor_id =  $this->post('vendor_id');
             $chk = $this->vendor->fetchTransmitOrdersList($vendor_id);
             $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getCompletedOrdersList')
       {
             $vendor_id =  $this->post('vendor_id');
             $chk = $this->vendor->fetchCompletedOrdersList($vendor_id);
             $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getCancelledOrdersList')
       {
             $vendor_id =  $this->post('vendor_id');
             $chk = $this->vendor->getCancelledOrdersList($vendor_id);
             $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='pending_settlements')
       {
             $vendor_id =  $this->post('vendor_id');
             $chk = $this->vendor->pendingSettlements($vendor_id);
             $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='completed_Settlements')
       {
             $vendor_id =  $this->post('vendor_id');
             $chk = $this->vendor->completedSettlements($vendor_id);
             $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getOrdersdetails')
       {
             $vendor_id =  $this->post('vendor_id');
             $oid =  $this->post('oid');
             $chk = $this->vendor->getOrdersDetails($oid,$vendor_id);
             $this->response($chk, REST_Controller::HTTP_OK);  
       }

       else if($this->post('action')=='dashboardDetails')
       {
              $vendor_id =  $this->post('vendor_id');
             $chk = $this->vendor->fetchdashboardDetails($vendor_id);
             $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getVendorStatus')
       {  
              $vendor_id =  $this->post('vendor_id');
             $tokenId =  $this->post('tokenId');
             $chk = $this->vendor->fetchVendorStatus($vendor_id,$tokenId);
             $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='ChangeVendorStatus')
       {
             $status =  $this->post('status');
             $vendor_id =  $this->post('vendor_id');
             $chk = $this->vendor->changeVendorStatus($vendor_id,$status);
             $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getshopworkinghours')
       {
             $vendor_id =  $this->post('vendor_id'); 
             $chk = $this->vendor->getShopWorkingHours($vendor_id);
             $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='addBusinessHours')
       {
             $vendor_id =  $this->post('vendor_id'); 
             $open_time =  $this->post('open_time'); 
             $closed_time =  $this->post('closed_time'); 
             $weekname =  $this->post('weekname'); 
             $working =  $this->post('working'); 

             $chk = $this->vendor->createBusinessHours($vendor_id,$open_time,$closed_time,$weekname,$working);
             $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='updateBusinessHours')
       {
             $vendor_id =  $this->post('vendor_id'); 
             $open_time =  $this->post('open_time'); 
             $closed_time =  $this->post('closed_time'); 
             $weekname =  $this->post('weekname'); 
             $working =  $this->post('working'); 
             $wid =  $this->post('wid'); 

             $chk = $this->vendor->updateBusinessHours($vendor_id,$open_time,$closed_time,$weekname,$working,$wid);
             $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='acceptOrder')
       {
             $vendor_id =  $this->post('vendor_id'); 
             $orderid =  $this->post('order_id');  

             $chk = $this->vendor->doacceptOrder($vendor_id,$orderid);
             $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='cancelOrder')
       {
             $vendor_id =  $this->post('vendor_id'); 
             $orderid =  $this->post('order_id'); 
             $reason =  $this->post('reason'); 
             //$comments =  $this->post('comments'); 
             $chk = $this->vendor->docancelOrder($vendor_id,$orderid,$reason);
             $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='complete_order')
       {
             $vendor_id =  $this->post('vendor_id'); 
             $orderid =  $this->post('orderid'); 
             $chk = $this->vendor->completeOrder($vendor_id,$orderid);
             $this->response($chk, REST_Controller::HTTP_OK);  
       }
       
       else if($this->post('action')=='getvendorProfile')
       {
             $vendor_id =  $this->post('vendor_id'); 
             $chk = $this->vendor->getVendorProfile($vendor_id);
             $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getmarchantProfile')
       {
             $vm_id =  $this->post('vm_id'); 
             $chk = $this->vendor->getMarchantProfile($vm_id);
             $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='updatevmProfile')
       {
             $vm_id =  $this->post('vm_id'); 
             $name =  $this->post('name'); 
             $address =  $this->post('address');
             $chk = $this->vendor->updatevmProfile($vm_id,$name,$address);
             $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='upload_shopimage')
       {
           $vendor_id = $this->post('vendor_id');
            $chk = $this->vendor->updateShopImage($vendor_id);
            
              $img = base_url()."uploads/shops/".$chk;
              $ss = array('status'=>true,'file'=>$chk,'fullpath'=>$img);
            
            $this->response($ss, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='upload_logoimage')
       {
            $vendor_id = $this->post('vendor_id');
            $chk = $this->vendor->updateLogoImage($vendor_id);
            
              $img = base_url()."uploads/shops/".$chk;
              $ss = array('status'=>true,'file'=>$chk,'fullpath'=>$img);
            
            $this->response($ss, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='updateProfiledetails')
       {
            $vendor_id = $this->post('vendor_id');
            $shop_name = $this->post('shop_name');
            $owner_name = $this->post('owner_name');
            $gender = $this->post('gender');

            $mobile = $this->post('mobile');
            $description = $this->post('bio');

            $address = $this->post('address');
            
            $delivery_charges= $this->post('delivery_charges');

            $youtube_url = $this->post('youtube_url');

            $chk = $this->vendor->updateProfileDetails($vendor_id,$shop_name,$owner_name,$gender,$mobile,$description,$address,$delivery_charges,$youtube_url);
            $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='vendorReview')
       {
          $vendor_id = $this->post('vendor_id'); 
          $chk = $this->vendor->getvendorReview($vendor_id);
           $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='deleteProduct')
       {
          $pid = $this->post('pid'); 
          $chk = $this->vendor->deleteProduct($pid);
           $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getsalesReport')
       {
          $vendor_id = $this->post('vendor_id'); 
          $chk = $this->vendor->getSalesReport($vendor_id);
           $this->response($chk, REST_Controller::HTTP_OK);  
       }
        else if($this->post('action')=='getDatewisesalesReport')
       {
          $vendor_id = $this->post('vendor_id'); 
          $sdate = $this->post('sdate'); 
          $chk = $this->vendor->getDatewisesalesReport($vendor_id,$sdate);
           $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='socialshare')
       {
          $chk = $this->vendor->socialShare();
           $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='deleteBuss')
       {
        $vendor_id = $this->post('vendor_id');
        $bid = $this->post('bid');
          $chk = $this->vendor->deleteBussness($vendor_id,$bid);
           $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getexchange_orders')
       {
          $vendor_id = $this->post('vendor_id');
          $chk = $this->vendor->getexchangeOrders($vendor_id);
           $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='acceptExchange_orders')
       {
             $oid = $this->post('oid');
             $sid = $this->post('sid');
           $chk = $this->vendor->acceptExchangeOrders($oid,$sid);
           $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='getbasicsubcategories')
       {
           $chk = $this->vendor->fetchbasicsubcategories();
           $this->response($chk, REST_Controller::HTTP_OK);  
       }
        else if($this->post('action')=='delivery_slots')
       {
               $shop_id  =  $this->post('shop_id');
               $sdate  =  $this->post('sdate');
              $chk = $this->vendor->deliverySlots($shop_id,$sdate);
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='products_filters')
      {
              $shop_id  =  $this->post('shop_id');
              $json_data = $this->post('json_data');
              $cat_id= $this->post('cat_id');
              $chk = $this->vendor->getproductsFilters($json_data,$shop_id,$cat_id);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
      }
      else if($this->post('action')=='getVendorDiscount')
      {
              $vendor_id  =  $this->post('vendor_id');
              $chk = $this->vendor->getVendorDiscount($vendor_id);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
      }
      else if($this->post('action')=='getVendorRequests')
      {
              $vendor_id  =  $this->post('vendor_id');
              $chk = $this->vendor->getVendorRequests($vendor_id);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
      }
      else if($this->post('action')=='requestvendorPayments')
      {
              $vendor_id  =  $this->post('vendor_id');
              $requested_amount  =  $this->post('requested_amount');
              $description  =  $this->post('description');
              $total_payment  =  $this->post('total_payment');
              $chk = $this->vendor->requestVendorPayments($vendor_id,$requested_amount,$description,$total_payment);
              $this->response($chk, REST_Controller::HTTP_OK);  
      }
      else if($this->post('action')=='deleteRequest')
      {
              $id  =  $this->post('id');
              $chk = $this->vendor->clrRequest($id);
              $this->response($chk, REST_Controller::HTTP_OK);  
      }
      else if($this->post('action')=='requestbidslist')
      {
              $vendor_id  =  $this->post('vendor_id');
              $bid_status  =  $this->post('bid_status');
              $chk = $this->vendor->getUsersBids($vendor_id,$bid_status);
              $this->response($chk, REST_Controller::HTTP_OK);  
      }
      else if($this->post('action')=='getBidDetails')
      {
              $bid  =  $this->post('bid');
              $vendor_id =  $this->post('vendor_id');
              $chk = $this->vendor->getBidDetails($bid,$vendor_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
      }
      else if($this->post('action')=='shopbanners')
      {
              $vendor_id  =  $this->post('vendor_id');
              $chk = $this->vendor->getShopBanners($vendor_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
      }
      else if($this->post('action')=='uploadBanner')
      {
            $chk = $this->vendor->selectBanner();
            if($chk=='false')
            {
               $ss = array('status'=>false,'message'=>"please upload below 5mb");
            }
            else
            {
              $img = $chk;
            }
            $this->response($img, REST_Controller::HTTP_OK); 
      }
      else if($this->post('action')=='uploadProductImages')
      {
            $chk = $this->vendor->selectProductImages();
            if($chk=='false')
            {
               $ss = array('status'=>false,'message'=>"please upload below 5mb");
            }
            else
            {
              $img = $chk;
            }
            $this->response($img, REST_Controller::HTTP_OK); 
      }
      else if($this->post('action')=='addbanner')
      {
              $vendor_id  =  $this->post('vendor_id');
              $title = $this->post('title');
              $image = $this->post('imagepath');
            $chk = $this->vendor->addBannerImage($vendor_id,$title,$image);
            if($chk=='false')
            {
               $ss = array('status'=>false,'message'=>"Something went wrong ,Please try again");
            }
            else if($chk=='true')
            {
              $ss = array('status'=>true,'message'=>"Banner added Successfully");
            }
            $this->response($ss, REST_Controller::HTTP_OK); 

      }
      else if($this->post('action')=='updatebanner')
      {
              $vendor_id  =  $this->post('vendor_id');
              $title = $this->post('title');
              $image = $this->post('imagepath');
              $id = $this->post('id');
            $chk = $this->vendor->updateBannerImage($vendor_id,$title,$image,$id);
            if($chk=='false')
            {
               $ss = array('status'=>false,'message'=>"Something went wrong ,Please try again");
            }
            else if($chk=='true')
            {
              $ss = array('status'=>true,'message'=>"Banner updated Successfully");
            }
            $this->response($ss, REST_Controller::HTTP_OK); 

      }
      else if($this->post('action')=='couponcodes')
      {
              $vendor_id  =  $this->post('vendor_id');
              $chk = $this->vendor->getCouponcodes($vendor_id);
              $this->response($chk, REST_Controller::HTTP_OK);  
      }
      else if($this->post('action')=='submitBidQuote')
      {
              $user_id  =  $this->post('user_id');
              $vendor_id  =  $this->post('vendor_id');
              $bid_id  =  $this->post('bid_id');
              $total_price  =  $this->post('total_price');

              $chk = $this->vendor->submitBidQuote($user_id,$vendor_id,$bid_id,$total_price);
              $this->response($chk, REST_Controller::HTTP_OK);  
      }
      else if($this->post('action')=='addcouponcodes')
      {
              $vendor_id  =  $this->post('vendor_id');
              $coupon_code  =  $this->post('coupon_code');
              $percentage  =  $this->post('percentage');
              $maximum_amount  =  $this->post('maximum_amount');
              $start_date  =  $this->post('start_date');
              $end_date  =  $this->post('end_date');
              $description  =  $this->post('description');
              $chk = $this->vendor->addCoupon($vendor_id,$coupon_code,$percentage,$maximum_amount,$start_date,$end_date,$description);
              $this->response($chk, REST_Controller::HTTP_OK);  
      }
      else if($this->post('action')=='updatecouponcodes')
      {
              $vendor_id  =  $this->post('vendor_id');
              $coupon_code  =  $this->post('coupon_code');
              $percentage  =  $this->post('percentage');
              $maximum_amount  =  $this->post('maximum_amount');
              $start_date  =  $this->post('start_date');
              $end_date  =  $this->post('end_date');
              $description  =  $this->post('description');
              $id  =  $this->post('id');
              $chk = $this->vendor->updateCoupon($vendor_id,$coupon_code,$percentage,$maximum_amount,$start_date,$end_date,$description,$id);
              $this->response($chk, REST_Controller::HTTP_OK);  
      }
      else if($this->post('action')=='delete_coupon')
      {
              $cid  =  $this->post('cid');

              $chk = $this->vendor->deleteCoupon($cid);
              $this->response($chk, REST_Controller::HTTP_OK);  
      }
      else if($this->post('action')=='delete_banner')
      {
              $cid  =  $this->post('cid');
              $chk = $this->vendor->deleteBanner($cid);
              $this->response($chk, REST_Controller::HTTP_OK);  
      }
      else if($this->post('action')=='searchProducts')
      {
              $keyword  =  $this->post('keyword');
              $shopId =  $this->post('shopId');
              $chk = $this->vendor->searchPreLoadedProducts($keyword,$shopId);
              $this->response($chk, REST_Controller::HTTP_OK);  
      }
      else if ($this->post('action') == 'version_control') {
            $chk = $this->vendor->versionControl();
            $this->response($chk, REST_Controller::HTTP_OK);  
        }
        else if($this->post('action')=='bid_show_status')
      {
          $chk = $this->vendor->bidShowStatus();
          $this->response($chk, REST_Controller::HTTP_OK);
      }
      else if($this->post('action')=='updateMinimumOrder')
       {
              $user_id = $this->post('user_id');
              $min_order_amount = $this->post('min_order_amount');
              $chk = $this->vendor->updateOrder($user_id,$min_order_amount);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='logout')
       {
              $user_id = $this->post('user_id');
              $chk = $this->vendor->logOut($user_id);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       }
       else if($this->post('action')=='homepage_sales_report')
       {
       		  $vendor_id = $this->post('vendor_id');
              $start_date = $this->post('start_date');
              $end_date = $this->post('end_date');
              $chk = $this->vendor->homepageSalesReport($vendor_id,$start_date,$end_date);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       } 
       else if($this->post('action')=='packing_complete')
       {
              $vendor_id = $this->post('vendor_id');
              $id = $this->post('order_id');
              $chk = $this->vendor->packing_complete($vendor_id,$id);
             
              $this->response($chk, REST_Controller::HTTP_OK);  
       } 



      

      

      

      

      


      

      


    }
}

?>