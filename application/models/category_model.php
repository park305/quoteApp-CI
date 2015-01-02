<?php
class Category_model extends CI_Model {

    var $name   = '';
    var $status = '';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function insert_category($name, $status)
    {
        $this->name = $name;
        $this->status = $status;

        $this->db->insert('categories', $this);

        if($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;        
    }

    function update_category($name, $status, $id)
    {
        $this->name = $name;
        $this->status = $status;

        $this->db->update('categories', $this, array('id' => $id));
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


    function delete($categoryID) {
        if(!is_numeric($categoryID))
            errorPage("ID is not a valid #");
        $query = $this->db->get_where('categories', array('id' => $categoryID));
        if($query->num_rows() > 0)
            {
                $this->db->delete('categories', array('id' => $categoryID)); 
                if($this->db->affected_rows() > 0)
                    return "Category " . $categoryID . " successfully deleted";
                else
                    return "Error deleting " . $categoryID;
            }
        else
            return "Category " . $categoryID . " does not exist, could not delete";
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