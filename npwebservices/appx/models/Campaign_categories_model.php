<?php
  class Campaign_categories_model extends CI_Model {
       
    public function __construct(){
        
      $this->load->database();
      
    }


    public function getcampaigncategories($camID){  

      $criteria = empty($camID)? "":" AND camID=".$camID;        
      $qry="SELECT camCatID, camID, product_categories.catID, product_categories.CatName, isExcluded, level FROM campaign_categories
            LEFT JOIN product_categories
            on campaign_categories.catID = product_categories.catID WHERE campaign_categories.isActive=1".$criteria;

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


    public function getcampaigncategories_prodids($catIDs){  
      
      if(sizeof($catIDs)>0) 
      {
        $qry="SELECT product.ProdID FROM product_categories
              LEFT JOIN product_categories_lookup on product_categories.catID = product_categories_lookup.CatID
              LEFT JOIN product on product_categories_lookup.ProdID = product.ProdID
              WHERE product.ProdID is NOT NULL AND product_categories_lookup.catID IN (".implode(",", $catIDs).") Group by product_categories.catID, product.ProdID";

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
      else
        return 0;
    }

    public function getcampaigncategoriesbyid($camCatID){  

       $this->db->select('camCatID, camID, catID, level');
       $this->db->from('campaign_categories');
       $this->db->where('camCatID',$camCatID);
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

    public function removecampaigncategoriesbyid($catIDs, $camID){  
             
      $qry = "UPDATE campaign_categories SET isActive=0 WHERE camID=".$camID ." AND catID NOT IN (".implode(",", $catIDs).")";
      $query = $this->db->query($qry);

    }

    public function removecampaigncategoriesall($camID){  
             
      $qry = "UPDATE campaign_categories SET isActive=0 WHERE camID=".$camID;
      $query = $this->db->query($qry);

    }

   
   //API call - delete a book record
    public function delete_all($id){
       $this->db->where('camID', $id);
       if($this->db->delete('campaign_categories')){
          return true;
        }else{
          return false;
        }
   }
   
   //API call - delete a book record
    public function delete($id){
       $this->db->where('camCatID', $id);
       if($this->db->delete('campaign_categories')){
          return true;
        }else{
          return false;
        }
   }

   //API call - add new book record
    public function add($data){
        if($this->db->insert('campaign_categories', $data)){
           return true;
        }else{
           return false;
        }
    }
    
    //API call - update a book record
    public function update($id, $data){
       $this->db->where('camCatID', $id);
       if($this->db->update('campaign_categories', $data)){
          return true;
        }else{
          return false;
        }
    }
}