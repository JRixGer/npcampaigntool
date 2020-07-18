<?php
  class Campaign_countries_model extends CI_Model {
       
    public function __construct(){
        
      $this->load->database();
      
    }

  
    public function getcampaigncountries($camID){  
      
      $criteria = empty($camID)? "":" AND camID=".$camID;             
      $qry="SELECT camCounID, camID, name, isDiscount, isActive FROM campaign_countries WHERE isActive=1".$criteria;

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

   
    public function getcampaignpcountriesbyid($camCounID){  

      $this->db->select('camCounID, camID, name');
      $this->db->from('campaign_countries');
      $this->db->where('camCounID',$camCounID);
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

    public function removecampaigncountriesbyid($counNames, $camID){  
             
      $qry = "UPDATE campaign_countries SET isActive=0 WHERE camID=".$camID ." AND name NOT IN ('".implode("','", $counNames)."')";
      $query = $this->db->query($qry);

    }


    public function removecampaigncountriesall($camID){  
             
      $qry = "UPDATE campaign_countries SET isActive=0 WHERE camID=".$camID;
      $query = $this->db->query($qry);

    }



   //API call - delete a book record
    public function delete_all($id){
       $this->db->where('camID', $id);
       if($this->db->delete('campaign_countries')){
          return true;
        }else{
          return false;
        }
   }


   //API call - delete a book record
    public function delete($id){
       $this->db->where('camCounID', $id);
       if($this->db->delete('campaign_countries')){
          return true;
        }else{
          return false;
        }
   }
   
   //API call - add new book record
    public function add($data){
        if($this->db->insert('campaign_countries', $data)){
           return true;
        }else{
           return false;
        }
    }
    
    //API call - update a book record
    public function update($id, $data){
       $this->db->where('camCounID', $id);
       if($this->db->update('campaign_countries', $data)){
          return true;
        }else{
          return false;
        }
    }
}