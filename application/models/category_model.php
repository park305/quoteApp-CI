<?php
class Category_model extends My_Model {

    var $name   = '';
    var $status = '';
    var $db_table = "categories";

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function insert_category($name, $status)
    {
        return $this->insert($name, $status);
    }

    function update_category($name, $status, $id)
    {
        $this->update($name, $status, $id);
    }

    function get_category_array($only_active = "yes")
    {
        $categories = array ();        
        if($only_active == "yes")
            $query = $this->db->query("SELECT * FROM categories WHERE status='Active' ORDER BY name");
        else
            $query = $this->db->query("SELECT * FROM categories ORDER BY name");
        
        if ($query->num_rows() > 0)
            foreach ($query->result_array() as $row) {        
                array_push($categories, $row);
            }
        return $categories;
    }


    function get_category_by_name($categoryName = "") {
        $query = $this->db->get_where('categories', array('name' => $categoryName));        
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row;
       }
        return FALSE;
    } 

    function get_category($categoryID)
    {
        if(!is_numeric($categoryID))
            errorPage("Error");
        //verify categoryID is #
        $query = $this->db->get_where('categories', array('id' => $categoryID));
        if ($query->num_rows() > 0) {
            return $row = $query->row_array();
            //return $row['name'];
        } else {
            errorPage("error, category does not exist");
        }
    }


  
    function category_form($url, $name = "", $status = "Active") {
        if($status == "")
            $status = "Active";        
        $statusDropDown = '<select name="status" id="status">';                      
        $statusDropDown .= '<option value="' . $status . '" selected>' . $status . '</option>';

        if($status == "Active")
            $statusDropDown .= '<option value="Hidden">Hidden</option>';
        else if ($status == "Hidden")
            $statusDropDown .= '<option value="Active">Active</option>';            
        
        $statusDropDown .= "</select>";

        return form_open($url) . '
          <label for="tag">Tag:</label> <input type="text" id="name" name="name" value="' . $name . '" /><br />
          <label for="Status">Status: </label>
          ' . $statusDropDown . '

        <input type="submit" name="submit" value="Submit" />
        </form>
        ';        
    }    
}

?>