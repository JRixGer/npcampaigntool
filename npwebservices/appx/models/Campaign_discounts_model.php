<?php
  class Campaign_discounts_model extends CI_Model {
       
    public function __construct(){
        
      $this->load->database();
      
    }


    public function getcouponbyname($name){  
      
      $searchKey = " discountCode='".$name."'"; 

      $qry="SELECT camDiscID, camID, discountCode, description, numberOfUse, isActive FROM campaign_discounts WHERE ".$searchKey;
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


    public function getcampaigndiscounts($camID){  
      
      $criteria = empty($camID)? "":" AND camID=".$camID;             
      $qry="SELECT camDiscID, camID, discountCode, description, numberOfUse, isActive FROM campaign_discounts WHERE isActive=1".$criteria;
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

   
    public function getcampaigndiscountsbyid($camDiscID){  

      $this->db->select('camDiscID, camID, discountCode, description, numberOfUse, isActive');
      $this->db->from('campaign_discounts');
      $this->db->where('camDiscID',$camDiscID);
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

    public function removecampaigndiscountsbyid($camDiscID, $camID){  
             
      $qry = "UPDATE campaign_discounts SET isActive=0 WHERE camID=".$camID ." AND camDiscID NOT IN (".implode(",", $camDiscID).")";
      $query = $this->db->query($qry);

    }


    public function removecampaigndiscountsall($camDiscID){  
             
      $qry = "UPDATE campaign_discounts SET isActive=0 WHERE camDiscID=".$camDiscID;
      $query = $this->db->query($qry);

    }



   //API call - delete a book record
    public function delete_all($id){
       $this->db->where('camID', $id);
       if($this->db->delete('campaign_discounts')){
          return true;
        }else{
          return false;
        }
   }

   //API call - delete a book record
    public function delete($id){
       $this->db->where('camDiscID', $id);
       if($this->db->delete('campaign_discounts')){
          return true;
        }else{
          return false;
        }
   }
   
   //API call - add new book record
    public function add($data){
        if($this->db->insert('campaign_discounts', $data)){
           return true;
        }else{
           return false;
        }
    }
    
    //API call - update a book record
    public function update($id, $data){
       $this->db->where('camDiscID', $id);
       if($this->db->update('campaign_discounts', $data)){
          return true;
        }else{
          return false;
        }
    }
}