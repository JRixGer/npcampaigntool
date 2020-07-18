<?php
  class Campaign_model extends CI_Model {
       
      public function __construct(){
          
        $this->load->database();
        
      }


      //API call - get a book record by isbn
      public function getcampaign_byid($camID){  

        $qry="SELECT campaigns.camID, 
        campaigns.name, 
        campaigns.description, 
        campaigns.startDate, 
        campaigns.endDate, 
        campaigns.discountCode, 
        campaigns.discountAmount, 
        campaigns.discountType, 
        campaigns.isAllowCombine,
        campaigns.isSiteWide, 
        campaigns.isActive, 
        campaigns.dateCreated,
        GROUP_CONCAT(DISTINCT campaign_discounts.discountCode SEPARATOR ', ') as coupons,
        GROUP_CONCAT(DISTINCT campaign_countries.name SEPARATOR ', ') as locations
        FROM campaigns 
        LEFT JOIN campaign_discounts on campaign_discounts.camID = campaigns.camID AND campaign_discounts.isActive=1
        LEFT JOIN campaign_countries on campaign_countries.camID = campaigns.camID AND campaign_countries.isActive=1
        WHERE campaigns.camID =".$camID." AND campaigns.isArchived = 0 Group by campaigns.camID Order by campaigns.camID DESC";

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

      public function getcampaigns($name){  

        $searchKey = !empty($name) ? " Where campaigns.name<>'' AND campaigns.isArchived = 0 AND (campaigns.name LIKE '%".$name."%' OR campaigns.description LIKE '%".$name."%') Group by campaigns.camID Order by campaigns.camID DESC LIMIT 100" :  " Where campaigns.name<>'' AND campaigns.isArchived = 0 Group by campaigns.camID Order by campaigns.camID DESC LIMIT 100"; 

        $qry="SELECT campaigns.camID, 
        campaigns.name, 
        campaigns.description, 
        campaigns.startDate, 
        campaigns.endDate, 
        campaigns.discountCode, 
        campaigns.discountAmount, 
        campaigns.discountType, 
        campaigns.isAllowCombine,
        campaigns.isSiteWide, 
        campaigns.isActive, 
        campaigns.dateCreated,
        GROUP_CONCAT(DISTINCT campaign_discounts.discountCode SEPARATOR ', ') as coupons,
        GROUP_CONCAT(DISTINCT campaign_countries.name SEPARATOR ', ') as locations
        FROM campaigns 
        LEFT JOIN campaign_discounts on campaign_discounts.camID = campaigns.camID AND campaign_discounts.isActive=1
        LEFT JOIN campaign_countries on campaign_countries.camID = campaigns.camID AND campaign_countries.isActive=1".$searchKey;

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


      //API call - get a book record by isbn
      public function getproductbyid($id){  

           $this->db->select('*');
           $this->db->from('product');
           $this->db->where('ProdID',$id);
           $query = $this->db->get();
           
           if($query->num_rows() == 1)
           {
               return $query->result_array();
           }
           else
           {
             return 0;
          }
      }
      
    //API call - get all books record
    public function getallbooks(){   
        $this->db->select('id, name, price, author, category, language, ISBN, publish_date');
        $this->db->from('tbl_books');
        $this->db->order_by("id", "desc"); 
        $query = $this->db->get();
        if($query->num_rows() > 0){
          return $query->result_array();
        }else{
          return 0;
        }
    }
   
   //API call - delete a book record
    public function delete($id){
       $this->db->where('camID', $id);
       if($this->db->delete('campaigns')){
          return true;
        }else{
          return false;
        }
   }
   
   //API call - add new book record
    public function add($data){
        if($this->db->insert('campaigns', $data)){
           return true;
        }else{
           return false;
        }
    }
    
    //API call - update a book record
    public function update($id, $data){

       $this->db->where('camID', $id);
       if($this->db->update('campaigns', $data)){
          return true;
        }else{
          return false;
        }
    }
}