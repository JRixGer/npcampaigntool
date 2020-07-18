<?php
  class Discount_model extends CI_Model {
       
      public function __construct(){
          
        $this->load->database();
        
      }

      //API call - get a book record by isbn
      public function getdiscountsbyname($name){  

        $searchKey = !empty($name) ? " Where description LIKE '%".$name."%' Order by description" :  " Order by description"; 
               
        $qry="SELECT * FROM discounts ".$searchKey ;

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
      public function getdiscountsbyid($id){  

           $this->db->select('*');
           $this->db->from('discounts');
           $this->db->where('disID',$id);
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


      //API call - get a book record by isbn
      public function getdiscounts(){  
               
        $qry="SELECT disID, description, discountCode, numberOfUse, isActive FROM discounts WHERE discountCode<>'' Order by disID DESC";

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
       $this->db->where('disID', $id);
       if($this->db->delete('discounts')){
          return true;
        }else{
          return false;
        }
   }
   
   //API call - add new book record
    public function add($data){
        if($this->db->insert('discounts', $data)){
           return true;
        }else{
           return false;
        }
    }
    
    //API call - update a book record
    public function update($id, $data){
       $this->db->where('disID', $id);
       if($this->db->update('discounts', $data)){
          return true;
        }else{
          return false;
        }
    }
}