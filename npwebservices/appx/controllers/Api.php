<?php
require(APPPATH.'/libraries/REST_Controller.php');
use Restserver\Libraries\REST_Controller;


class Api extends REST_Controller{
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('product_model');
        $this->load->model('product_categories_model');
        $this->load->model('campaign_model');
        $this->load->model('campaign_categories_model');
        $this->load->model('campaign_products_model');
        $this->load->model('campaign_countries_model');
        $this->load->model('campaign_discounts_model');
        $this->load->model('country_model');
    }
    
    function campaigns_get(){

        $campaignName = empty($this->get('name'))? '':$this->get('name');

        $result = $this->campaign_model->getcampaigns($campaignName);
        if($result){
            $this->response(['posts'=>$result], 200); 
            exit;
        } 
        else{
             $this->response("Invalid Campaign", 404);
            exit;
        }
    } 


    function campaignsByName_get(){

        $name = $this->get('search');
        $draw = $this->get('draw');

        $result = $this->campaign_model->getcampaigns($name);
        if($result){
            $this->response(['posts'=>$result, 'draw'=>$draw], 200); 
            exit;
        } 
        else{
             $this->response("Invalid Name", 404);
            exit;
        }
    } 


    function campaignpreview_get(){

        $campaign = json_decode($_GET['campaign'], true);
        $category = json_decode($_GET['category'], true);
        $product = json_decode($_GET['product'], true);

        $prodIDs1 = array();
        $prodIDs2 = array();
        $catIDs = array();

        $prodIDs1 = array();
        for($i = 0; $i < sizeof($product); $i ++)
            $prodIDs1[] = $product[$i]['prodID'];


        for($i = 0; $i < sizeof($category); $i ++)
        {
            $catID = $category[$i]['catID'];
            $level = $category[$i]['level'];
            $catByLevels = $this->product_categories_model->getcampaigncategories_bylevel($catID, $level);

            foreach ($catByLevels as $catByLevel) 
            {
                $cl = (array) $catByLevel;
                if(!empty($cl['lev3_CID']))
                    $catIDs[] = $cl['lev3_CID'];
                else if(!empty($cl['lev2_CID']))
                    $catIDs[] = $cl['lev2_CID'];
                else if(!empty($cl['lev1_CID']))
                    $catIDs[] = $cl['lev1_CID'];
            }
           
        }

        if(sizeof($catIDs) > 0)
        {
            $prodidsbycat = $this->campaign_categories_model->getcampaigncategories_prodids($catIDs);
            if($prodidsbycat)
            {
                foreach ($prodidsbycat as $prodid) 
                {
                    $p = (array) $prodid;
                    if(!empty($p['ProdID']))
                        $prodIDs2[] = $p['ProdID'];
                }
            }
        }

        $products = $this->campaign_products_model->getcampaignproducts_preview($campaign['camID'], $campaign['discountType'],  $campaign['discountAmount'],  array_merge($prodIDs1, $prodIDs2));

        $campaigns = $this->campaign_model->getcampaign_byid($campaign['camID']);
        $categories = $this->campaign_categories_model->getcampaigncategories($campaign['camID']);
        $discounts = $this->campaign_discounts_model->getcampaigndiscounts($campaign['camID']);
        $countries = $this->campaign_countries_model->getcampaigncountries($campaign['camID']);

        if($campaigns || $campaignsWithProducts || $categories || $products ||$discounts ||$countries){
            $this->response(['campaigns'=>$campaigns, 'categories'=>$categories,'products'=>$products, 'discounts'=>$discounts,'countries'=>$countries], 200); 
            exit;
        } 
        else{
             $this->response("Invalid Campaign", 404);
            exit;
        }
    } 


    // function productsWithCampaign_get(){

    //     $campaign = json_decode($_GET['campaign'], true);
    //     $product = json_decode($_GET['product'], true);

    //     $prodIDs = array();
    //     $prod = array();
    //     for($i = 0; $i < sizeof($product); $i ++)
    //     {
    //         $prodIDs[] = $product[$i]['prodID'];
    //         $prod[$i]['prodAmount'][$product[$i]['prodID']] = 0;
    //         $prod[$i]['noOfInstance'][$product[$i]['prodID']] = 0;
    //         $prod[$i]['prodID'] [$product[$i]['prodID']]= $product[$i]['prodID'];
    //     }

    //     $campaignsWithProducts = $this->campaign_model->getcampaignswithproductsdetailed($prodIDs);
    //     $camIDs = array();
    //     foreach ($campaignsWithProducts as $campaignsWithProduct) 
    //     {
    //         $camwp = (array) $campaignsWithProduct;
    //         $camIDs[] = $camwp['camID'];

    //         for($i = 0; $i < sizeof($prodIDs); $i++) 
    //             if($prodIDs[$i] == $camwp['prodID']  && $camwp['Price']>0)
    //             {
    //                 $prod[$i]['prodAmount'][$camwp['prodID']] = $camwp['Price'];
    //                 $prod[$i]['noOfInstance'][$camwp['prodID']] ++;
    //                 $prod[$i]['prodID'][$camwp['prodID']] = $camwp['prodID'];
    //             }

    //     }
       

    //     $p = $prod[0]['prodID'][$prodIDs[0]];
    //     for($i = 0; $i < sizeof($prodIDs)-1; $i++) 
    //         if($prod[$i]['prodAmount'][$prodIDs[$i]] < $prod[$i+1]['prodAmount'][$prodIDs[$i+1]])
    //             $p = $prod[$i+1]['prodID'][$prodIDs[$i+1]];

    //     $campaigns = $this->campaign_model->getcampaignswithproducts($camIDs, $prodIDs, $p);
    //     $categories = $this->campaign_categories_model->getcategorieswithcampaigns($camIDs);
    //     $products = $this->campaign_products_model->getproductswithcampaigns($camIDs);
    //     $discounts = $this->campaign_discounts_model->getdiscountswithcampaigns($camIDs);
    //     $countries = $this->campaign_countries_model->getcountrieswithcampaigns($camIDs);
        
    //     if($campaigns || $campaignsWithProducts || $categories || $products ||$discounts ||$countries)
    //     {
    //         $this->response(['campaigns'=>$campaigns, 'campaignsWithProducts'=>$campaignsWithProducts, 'categories'=>$categories,'products'=>$products, 'discounts'=>$discounts,'countries'=>$countries,'p'=>$p], 200); 
    //         exit;
    //     } 
    //     else{
    //          $this->response("Invalid Request", 404);
    //         exit;
    //     }
    // } 


    function categoriesByName_get(){

        $name = $this->get('search');
        $draw = $this->get('draw');

        $result = $this->product_categories_model->getcategoriesbyname( $name );
        if($result){
            $this->response(['posts'=>$result, 'draw'=>$draw], 200); 
            exit;
        } 
        else{
             $this->response("Invalid Name", 404);
            exit;
        }
    } 


    function productsByName_get(){

        $name = $this->get('search');
        $draw = $this->get('draw');
      
        $result = $this->product_model->getproductsbyname( $name );
        if($result){
            $this->response(['posts'=>$result, 'draw'=>$draw], 200); 
            exit;
        } 
        else{
             $this->response("Invalid Name", 404);
            exit;
        }
    } 
    

    function countriesByName_get(){

        $name = $this->get('search');
        $draw = $this->get('draw');
       
        $result = $this->country_model->getcountriesbyname( $name );
        if($result){
            $this->response(['posts'=>$result, 'draw'=>$draw], 200); 
            exit;
        } 
        else{
             $this->response("Invalid Name", 404);
            exit;
        }
    } 

    function couponByName_get(){

        $name = $this->get('discountCode');
        $draw = $this->get('draw');

       
        $result = $this->campaign_discounts_model->getcouponbyname( $name );
        if($result){
            $this->response(['posts'=>true, 'draw'=>$draw], 200); 
            exit;
        } 
        else{
             $this->response("Invalid Name", 404);
            exit;
        }
    } 

    function campaignCategories_get(){

        $camID = $this->get('camid');
        $result = $this->campaign_categories_model->getcampaigncategories($camID);
        if($result){
            $this->response(['posts'=>$result], 200); 
            exit;
        } 
        else{
             $this->response("Invalid Product", 404);
            exit;
        }
    } 

    function campaignProducts_get(){

        $camID = $this->get('camid');
        $result = $this->campaign_products_model->getcampaignproducts($camID);
        if($result){
            $this->response(['posts'=>$result], 200); 
            exit;
        } 
        else{
             $this->response("Invalid Product", 404);
            exit;
        }
    } 


    function campaignCountries_get(){

        $camID = $this->get('camid');
        $result = $this->campaign_countries_model->getcampaigncountries($camID);
        if($result){
            $this->response(['posts'=>$result], 200); 
            exit;
        } 
        else{
             $this->response("Invalid Product", 404);
            exit;
        }
    } 

    function campaignDiscounts_get(){

        $camID = $this->get('camid');
        $result = $this->campaign_discounts_model->getcampaigndiscounts($camID);
        if($result){
            $this->response(['posts'=>$result], 200); 
            exit;
        } 
        else{
             $this->response("Invalid Product", 404);
            exit;
        }
    }

    function discounts_get(){

        $result = $this->discount_model->getdiscounts();
        if($result){
            $this->response(['posts'=>$result], 200); 
            exit;
        } 
        else{
             $this->response("Invalid Name", 404);
            exit;
        }
    } 


    // codes below are for CRUD
    function copyCampaign_post(){

        $campaign = json_decode($_POST['campaign'], true);

        $result = $this->campaign_model->add(array("name"=>$campaign['name'], "description"=>$campaign['description'], "startDate"=>$campaign['startDate'], "endDate"=>$campaign['endDate'], "isAllowCombine"=>$campaign['isAllowCombine'], "isSiteWide"=>$campaign['isSiteWide'], "isActive"=>$campaign['isActive'], "discountCode"=>$campaign['discountCode'], "discountAmount"=>$campaign['discountAmount']));

        if($result === 0){
             $this->response([false,'category information could not be saved. Try again.'], 404);
        }else{

            $camNewID = $this->db->insert_id();

            $cat = json_decode($_POST['campaignCategories'], true);
            for($i = 0; $i < sizeof($cat); $i++) 
                $this->campaign_categories_model->add(array("camID"=>$camNewID, "catID"=>$cat[$i]['catID'], "level"=>$cat[$i]['level']));
            
            $prod = json_decode($_POST['campaignProducts'], true);
            for($i = 0; $i < sizeof($prod); $i++) 
                $this->campaign_products_model->add(array("camID"=>$camNewID, "prodID"=>$prod[$i]['prodID']));

            $disc = json_decode($_POST['campaignDiscounts'], true);

            for($i = 0; $i < sizeof($disc); $i++) 
                $this->campaign_discounts_model->add(array("camID"=>$camNewID, "discountCode"=>$disc[$i]['discountCode'], "numberOfUse"=>$disc[$i]['numberOfUse'], "description"=>$disc[$i]['description']));

            $coun = json_decode($_POST['campaignCountries'], true);
            for($i = 0; $i < sizeof($coun); $i++) 
                $this->campaign_countries_model->add(array("camID"=>$camNewID, "name"=>$coun[$i]['name']));

            $this->response([true,'New campaign copied'], 200);  
        }

    }
    

    function addCampaign_post(){

        

        $campaign = json_decode($_POST['campaign'], true);
        $result = $this->campaign_model->add(array("name"=>$campaign['name'], "description"=>$campaign['description'], "startDate"=>$campaign['startDate'], "endDate"=>$campaign['endDate'], "isAllowCombine"=>$campaign['isAllowCombine'], "isSiteWide"=>$campaign['isSiteWide'], "isActive"=>$campaign['isActive'], "discountCode"=>$campaign['discountCode'], "discountAmount"=>$campaign['discountAmount'], "discountType"=>$campaign['discountType']));
        if($result === 0){
             $this->response([false,'category information could not be saved. Try again.'], 404);
        }else{

            $camNewID = $this->db->insert_id();

            $cat = json_decode($_POST['campaignCategories'], true);
            for($i = 0; $i < sizeof($cat); $i++) 
                $this->campaign_categories_model->add(array("camID"=>$camNewID, "catID"=>$cat[$i]['catID'], "level"=>$cat[$i]['level']));
            

            $prod = json_decode($_POST['campaignProducts'], true);
            for($i = 0; $i < sizeof($prod); $i++) 
                $this->campaign_products_model->add(array("camID"=>$camNewID, "prodID"=>$prod[$i]['prodID']));

            $disc = json_decode($_POST['campaignDiscounts'], true);
            for($i = 0; $i < sizeof($disc); $i++) 
                $this->campaign_discounts_model->add(array("camID"=>$camNewID,  "discountCode"=>$disc[$i]['discountCode'], "numberOfUse"=>$disc[$i]['numberOfUse'], "description"=>$disc[$i]['description']));

            $coun = json_decode($_POST['campaignCountries'], true);
            for($i = 0; $i < sizeof($coun); $i++) 
                $this->campaign_countries_model->add(array("camID"=>$camNewID, "name"=>$coun[$i]));

            $this->response([true,'New campaign created'], 200);  
        }

    }
    
    function updateCampaign_post(){
        
        if(isset($_POST['ACTION']))
        {   
            if($_POST['ACTION'] == 'isExcluded')
            {
                if($_POST['whichTable'] == 'campaign category' && $_POST['whichPart'] == 'sitewide')
                    $result = $this->campaign_categories_model->update($_POST['id'], array("isExcluded" => $_POST['isExcluded'])); // It should be possible to make site wide campaigns but exclude categories and/or products.
                else if($_POST['whichTable'] == 'campaign category' && $_POST['whichPart'] == 'discount')  
                    $result = $this->campaign_categories_model->update($_POST['id'], array("isDiscount" => $_POST['isExcluded'])); // It should be possible to exclude campaigns from discount codes
                else if($_POST['whichTable'] == 'campaign product' && $_POST['whichPart'] == 'sitewide')   
                    $result = $this->campaign_products_model->update($_POST['id'], array("isExcluded" => $_POST['isExcluded'])); // It should be possible to make site wide campaigns but exclude categories and/or products.
                else if($_POST['whichTable'] == 'campaign product' && $_POST['whichPart'] == 'discount')  
                    $result = $this->campaign_products_model->update($_POST['id'], array("isDiscount" => $_POST['isExcluded'])); //It should be possible to exclude products from discount codes
                if($result === 0)
                    $this->response([false, ucfirst ($_POST['whichTable']).' information could not be saved. Try again.'], 404);
                else
                    $this->response([true, ucfirst ($_POST['whichTable']).' updated'], 200);

            }
        }
        else
        {
            $campaign = json_decode($_POST['campaign'], true);
            $result = $this->campaign_model->update($campaign['camID'], array("name"=>$campaign['name'], "description"=>$campaign['description'], "startDate"=>$campaign['startDate'], "endDate"=>$campaign['endDate'], "isAllowCombine"=>$campaign['isAllowCombine'], "isSiteWide"=>$campaign['isSiteWide'], "isActive"=>$campaign['isActive'], "discountCode"=>$campaign['discountCode'], "discountAmount"=>$campaign['discountAmount'], "discountType"=>$campaign['discountType']));
            if($result === 0){
                $this->response([false,'product information could not be saved. Try again.'], 404);
            }else{

                $camID = $campaign['camID'];

                $catIDs = array();
                $cat = json_decode($_POST['campaignCategories'], true);
                for($i = 0; $i < sizeof($cat); $i++) 
                {
                    if(!empty($cat[$i]['catID']))
                        $catIDs[] = $cat[$i]['catID'];
                    
                    if(isset($cat[$i]['camCatID']))                    
                        $this->campaign_categories_model->update($cat[$i]['camCatID'], array("camID"=>$camID, "catID"=>$cat[$i]['catID'], "level"=>$cat[$i]['level']));
                    else
                        $this->campaign_categories_model->add(array("camID"=>$camID, "catID"=>$cat[$i]['catID'], "level"=>$cat[$i]['level']));

                }

                if(sizeof($catIDs)>0)
                    $this->campaign_categories_model->removecampaigncategoriesbyid($catIDs, $camID);
                else
                    $this->campaign_categories_model->removecampaigncategoriesall($camID);



                $prodIDs = array();
                $prod = json_decode($_POST['campaignProducts'], true);
                for($i = 0; $i < sizeof($prod); $i++) 
                {
                    if(!empty($prod[$i]['prodID']))
                        $prodIDs[] = $prod[$i]['prodID'];

                    if(isset($prod[$i]['camProdID']))
                        $this->campaign_products_model->update($prod[$i]['camProdID'], array("camID"=>$camID, "prodID"=>$prod[$i]['prodID']));
                    else
                        $this->campaign_products_model->add(array("camID"=>$camID, "prodID"=>$prod[$i]['prodID']));

                }

                if(sizeof($prodIDs)>0)
                    $this->campaign_products_model->removecampaignproductsbyid($prodIDs, $camID);
                else
                    $this->campaign_products_model->removecampaignproductsall($camID);


                $camDiscID = array();
                $disc = json_decode($_POST['campaignDiscounts'], true);
                for($i = 0; $i < sizeof($disc); $i++) 
                {
                    if(!empty($disc[$i]['camDiscID']))
                        $camDiscID[] = $disc[$i]['camDiscID'];

                    if(isset($disc[$i]['camDiscID']))
                        $this->campaign_discounts_model->update($disc[$i]['camDiscID'], array("camID"=>$camID,  "discountCode"=>$disc[$i]['discountCode'], "numberOfUse"=>$disc[$i]['numberOfUse'], "description"=>$disc[$i]['description']));
                    else
                    {
                        $this->campaign_discounts_model->add(array("camID"=>$camID, "discountCode"=>$disc[$i]['discountCode'], "numberOfUse"=>$disc[$i]['numberOfUse'], "description"=>$disc[$i]['description']));
                        
                        $camDiscID[] = $this->db->insert_id();
                    }

                }

                if(sizeof($camDiscID)>0)
                    $this->campaign_discounts_model->removecampaigndiscountsbyid($camDiscID, $camID);
                else
                    $this->campaign_discounts_model->removecampaigndiscountsall($camID);



                $counIDs = array();
                $coun = json_decode($_POST['campaignCountries'], true);
                for($i = 0; $i < sizeof($coun); $i++) 
                {
                    if(!empty($coun[$i]))
                        $counIDs[] = $coun[$i]['name'];

                    if(isset($coun[$i]['camCounID']))
                        $this->campaign_countries_model->update($coun[$i]['camCounID'], array("camID"=>$camID, "name"=>$coun[$i]['name']));
                    else
                        $this->campaign_countries_model->add(array("camID"=>$camID, "name"=>$coun[$i]['name']));

                }

                if(sizeof($counIDs)>0)
                    $this->campaign_countries_model->removecampaigncountriesbyid($counIDs, $camID);
                else
                    $this->campaign_countries_model->removecampaigncountriesall($camID);


                $this->response([true,'Campaign updated'], 200);
            }            
        }
    }


    function updatecategoryput_put(){

        $this->response("Success", 200);

    }

    function deleteCampaign_post()
    {
        
        if($this->post('action') == 'campaign')
        {
            $campaign = json_decode($_POST['campaign'], true);
            
            if(empty($campaign['camID'])){
                $this->response("Parameter missing", 404);
            }
             
            if($this->campaign_model->update($campaign['camID'], array('isArchived' => 1)))
            {
                // $this->campaign_categories_model->delete_all($campaign['camID']);
                // $this->campaign_products_model->delete_all($campaign['camID']); 
                // $this->campaign_discounts_model->delete_all($campaign['camID']);
                // $this->campaign_countries_model->delete_all($campaign['camID']);            
                $this->response("Success", 200);
            } 
            else
            {
                $this->response("Failed", 400);
            }            
        }
        else if($this->post('action') == 'detail')
        {
            if(empty($this->post('id'))){
                $this->response("Parameter missing", 404);
            }

            if($this->post('tableName') == "campaign category")
                $this->campaign_categories_model->delete($this->post('id'));
            else if($this->post('tableName') == "campaign product")
                $this->campaign_products_model->delete($this->post('id')); 
            else if($this->post('tableName') == "campaign discount")
                $this->campaign_discounts_model->delete($this->post('id'));
            else if($this->post('tableName') == "campaign country")
                $this->campaign_countries_model->delete($this->post('id'));    

            $this->response("Success", 200);
        }
    }


    function deleteDiscount_post()
    {
        
        $discount = json_decode($_POST['discount'], true);
        
        if(empty($discount['disID'])){
            $this->response("Parameter missing", 404);
        }
         
        if($this->discount_model->delete($discount['disID']))
        {
            $this->discount_countries_model->delete($discount['disID']); 
            $this->response("Success", 200);
        } 
        else
        {
            $this->response("Failed", 400);
        }
    }

    // function deleteBook_delete()
    // {
    //     $id  = $this->delete('id');

    //     if(!$id){
    //         $this->response("Parameter missing", 404);
    //     }
         
    //     if($this->campaign_model->delete($id))
    //     {
    //         $this->response("Success", 200);
    //     } 
    //     else
    //     {
    //         $this->response("Failed", 400);
    //     }
    // }



    // function books_get(){
    //     $result = $this->book_model->getallbooks();
    //     if($result){
    //         $this->response($result, 200); 
    //     } 
    //     else{
    //         $this->response("No record found", 404);
    //     }
    // }
     
    // function addBook_post(){
    //      $name      = $this->post('name');
    //      $price     = $this->post('price');
    //      $author    = $this->post('author');
    //      $category  = $this->post('category');
    //      $language  = $this->post('language');
    //      $isbn      = $this->post('isbn');
    //      $pub_date  = $this->post('publish_date');
        
    //      if(!$name || !$price || !$author || !$price || !$isbn || !$category){
    //             $this->response("Enter complete book information to save", 400);
    //      }else{
    //         $result = $this->book_model->add(array("name"=>$name, "price"=>$price, "author"=>$author, "category"=>$category, "language"=>$language, "isbn"=>$isbn, "publish_date"=>$pub_date));
    //         if($result === 0){
    //             $this->response("Book information coild not be saved. Try again.", 404);
    //         }else{
    //             $this->response("success", 200);  
           
    //         }
    //     }
    // }
    
    // function updateBook_put(){
         
    //      $name      = $this->put('name');
    //      $price     = $this->put('price');
    //      $author    = $this->put('author');
    //      $category  = $this->put('category');
    //      $language  = $this->put('language');
    //      $isbn      = $this->put('isbn');
    //      $pub_date  = $this->put('publish_date');
    //      $id        = $this->put('id');
         
    //      if(!$name || !$price || !$author || !$price || !$isbn || !$category){
    //             $this->response("Enter complete book information to save", 400);
    //      }else{
    //         $result = $this->book_model->update($id, array("name"=>$name, "price"=>$price, "author"=>$author, "category"=>$category, "language"=>$language, "isbn"=>$isbn, "publish_date"=>$pub_date));
    //         if($result === 0){
    //             $this->response("Book information coild not be saved. Try again.", 404);
    //         }else{
    //             $this->response("success", 200);  
    //         }
    //     }
    // }

    // function deleteBook_delete()
    // {
    //     $id  = $this->delete('id');
    //     if(!$id){
    //         $this->response("Parameter missing", 404);
    //     }
         
    //     if($this->book_model->delete($id))
    //     {
    //         $this->response("Success", 200);
    //     } 
    //     else
    //     {
    //         $this->response("Failed", 400);
    //     }
    // }
}