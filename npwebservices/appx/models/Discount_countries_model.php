<?php
  class Discount_countries_model extends CI_Model {
       
    public function __construct(){
        
      $this->load->database();
      
    }


    public function getdiscountcountries($disID){  
      
      $criteria = empty($disID)? "":" AND disID=".$disID;             
      $qry="SELECT disCounID, disID, name, code, isActive FROM discount_countries WHERE isActive=1".$criteria;

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

   
    public function getdiscountpcountriesbyid($disCounID){  

      $this->db->select('disCounID, disID, name, code');
      $this->db->from('discount_countries');
      $this->db->where('disCounID',$disCounID);
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

    public function removediscountcountriesbyid($counIDs, $disID){  
             
      $qry = "UPDATE discount_countries SET isActive=0 WHERE disID=".$disID ." AND name NOT IN ('".implode("','", $counIDs)."')";
      $query = $this->db->query($qry);

    }


    public function removediscountcountriesall($disID){  
             
      $qry = "UPDATE discount_countries SET isActive=0 WHERE disID=".$disID;
      $query = $this->db->query($qry);

    }




   //API call - delete a book record
    public function delete($id){
       $this->db->where('disID', $id);
       if($this->db->delete('discount_countries')){
          return true;
        }else{
          return false;
        }
   }
   
   //API call - add new book record
    public function add($data){
        if($this->db->insert('discount_countries', $data)){
           return true;
        }else{
           return false;
        }
    }
    
    //API call - update a book record
    public function update($id, $data){
       $this->db->where('disCounID', $id);
       if($this->db->update('discount_countries', $data)){
          return true;
        }else{
          return false;
        }
    }
}