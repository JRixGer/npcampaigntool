<?php
  class Campaign_products_model extends CI_Model {
       
    public function __construct(){
        
      $this->load->database();
      
    }

    // $campaign['camID'], $campaign['discountType'],  $campaign['discountAmount'],  array_merge($prodIDs1, $prodIDs2)
    public function getcampaignproducts_preview($camID, $camDT, $camDA, $prodIDs){  
      
      if(sizeof($prodIDs) > 0)
      {
        $qry="
            SELECT DISTINCT
              ".$camID." As camID, 
              IF('".$camDT."' = '$', ".$camDA.",(".$camDA."/100)*product_price.Price) As actualDiscount,
              IF('".$camDT."' = '$', (product_price.Price-".$camDA."),(product_price.Price - (".$camDA."/100)*product_price.Price)) As salesPrice, 
              product_categories_lookup.prodID, 
              product_price.Price,
              product.ProdName                
            FROM product
            LEFT JOIN product_categories_lookup on  product.ProdID = product_categories_lookup.ProdID
            LEFT JOIN product_price on product_categories_lookup.prodID = product_price.ProdID
            Where (product.ProdID IN (".implode(",", $prodIDs).")) AND product_price.Price is not NULL AND product.ProdName is not NULL Group by product.ProdName Order by product.ProdName";
        $query = $this->db->query($qry);
        if($query->num_rows() > 0)
          return $query->result_array();
        else
          return 0;
      }
      else
        return 0; 

    }

    public function getcampaignproducts($camID){  
             
      $criteria = empty($camID)? "":" AND camID=".$camID;       
      $qry="SELECT camProdID, camID, campaign_products.prodID, product.ProdName, product_price.Price, isDiscount, isExcluded, isActive, dateCreated 
            FROM campaign_products
            LEFT JOIN product
            on product.ProdID = campaign_products.prodID
            LEFT JOIN product_price
            on product.ProdID = product_price.ProdID WHERE campaign_products.isActive=1".$criteria;

      $query = $this->db->query($qry);
      if($query->num_rows() > 0)
      {
        return $query->result_array();
      }
      else
      {
        return 0;
      }
    }

   
    public function getcampaignproductsbyid($camProdID){  

      $this->db->select('camProdID, camID, prodID');
      $this->db->from('campaign_products');
      $this->db->where('camProdID',$camProdID);
      $query = $this->db->get();
     
      if($query->num_rows() > 0)
      {
         return $query->result_array();
      }
      else
      {
       return 0;
      }
    }

    public function removecampaignproductsbyid($prodIDs, $camID){  
             
      $qry = "UPDATE campaign_products SET isActive=0 WHERE camID=".$camID ." AND prodID NOT IN (".implode(",", $prodIDs).")";
      $query = $this->db->query($qry);

    }


    public function removecampaignproductsall($camID){  
             
      $qry = "UPDATE campaign_products SET isActive=0 WHERE camID=".$camID;
      $query = $this->db->query($qry);

    }




   //API call - delete a book record
    public function delete_all($id){
       $this->db->where('camID', $id);
       if($this->db->delete('campaign_products')){
          return true;
        }else{
          return false;
        }
   }
   

   //API call - delete a book record
    public function delete($id){
       $this->db->where('camProdID', $id);
       if($this->db->delete('campaign_products')){
          return true;
        }else{
          return false;
        }
   }

   //API call - add new book record
    public function add($data){
        if($this->db->insert('campaign_products', $data)){
           return true;
        }else{
           return false;
        }
    }
    
    //API call - update a book record
    public function update($id, $data){
       $this->db->where('camProdID', $id);
       if($this->db->update('campaign_products', $data)){
          return true;
        }else{
          return false;
        }
    }
}