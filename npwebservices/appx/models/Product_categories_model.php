<?php
  class Product_categories_model extends CI_Model {
       
      public function __construct(){
          
        $this->load->database();
        
      }
     

    public function getcampaigncategories_bylevel($catID, $level){  

      $qry = "select l1.CatName as lev1_name, l1.ParentID as lev1_PID, l1.CatID as lev1_CID, '1' as lev1
             , l2.CatName as lev2_name, l2.ParentID as lev2_PID, l2.CatID as lev2_CID, '2' as lev2
             , l3.CatName as lev3_name, l3.ParentID as lev3_PID, l3.CatID as lev3_CID, '3' as lev3
              from product_categories as l1
            left outer
              join product_categories as l2
                on l2.ParentID = l1.CatID
            left outer
              join product_categories as l3
                on l3.ParentID = l2.CatID";

      if($level == 1)          
      {
        $qry .= " left outer
              join campaign_categories as c
                on c.catID = l1.CatID";  
      }
      else if($level == 2)          
      {
        $qry .= " left outer
              join campaign_categories as c
                on c.catID = l2.CatID";  
      }
      else if($level == 3)          
      {
        $qry .= " left outer
              join campaign_categories as c
                on c.catID = l3.CatID";  
      }

      $qry .= " where l1.ParentID is null or l1.ParentID = 0 AND c.catID = ".$catID." Group by lev1_CID, lev2_CID, lev3_CID, l3.CatID"; 

      //echo  $qry;

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


    public function getcategoriesbyname($name){  


      if(empty($name))
        $searchKey = " order by lev1_name, lev2_name, lev3_name"; 
      else
        $searchKey = " AND (l3.CatName LIKE '%".$name."%' OR l2.CatName LIKE '%".$name."%' OR l1.CatName LIKE '%".$name."%' OR concat(l1.CatName,' ',l2.CatName,' ',l3.CatName) LIKE '%".$name."%') order by lev1_name, lev2_name, lev3_name"; 


              
      $qry="select l1.CatName as lev1_name, l1.ParentID as lev1_PID, l1.CatID as lev1_CID, '1' as lev1
           , l2.CatName as lev2_name, l2.ParentID as lev2_PID, l2.CatID as lev2_CID, '2' as lev2
           , l3.CatName as lev3_name, l3.ParentID as lev3_PID, l3.CatID as lev3_CID, '3' as lev3
            from product_categories as l1
          left outer
            join product_categories as l2
              on l2.ParentID = l1.CatID
          left outer
            join product_categories as l3
              on l3.ParentID = l2.CatID
           where l1.ParentID is null or l1.ParentID = 0".$searchKey;

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
       $this->db->where('id', $id);
       if($this->db->delete('tbl_books')){
          return true;
        }else{
          return false;
        }
   }
   
   //API call - add new book record
    public function add($data){
        if($this->db->insert('tbl_books', $data)){
           return true;
        }else{
           return false;
        }
    }
    
    //API call - update a book record
    public function update($id, $data){
       $this->db->where('id', $id);
       if($this->db->update('tbl_books', $data)){
          return true;
        }else{
          return false;
        }
    }
}