<?php
class Tag_model extends My_Model {

    var $name   = '';
    var $status = '';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    

    function update_tag($name, $status, $id)
    {
        $this->name = $name;
        $this->status = $status;

        $this->db->update('tags', $this, array('id' => $id));
    }

    function get_tag_array($only_active = "yes")
    {
        $tags = array();
        if($only_active == "yes")
            $query = $this->db->select('*')->where('status = \'Active\'')->order_by('name')->get("tags");
        else
            $query = $this->db->select('*')->order_by('name')->get("tags");

        
        if ($query->num_rows() > 0)
            foreach ($query->result_array() as $row) {        
                array_push($tags, $row);
            }
        return $tags;        
    }

    function fetch_tagIDs($tags = array()) {
        $tagIDs = array();
        $query = $this->db->query("SELECT * FROM tags WHERE status='Active' AND name IN ('" . implode("', '", $tags) . "')");        
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                array_push($tagIDs, $row['id']);
            }
       }
        return $tagIDs;
    } 

    function get_tag($tagID = 0) 
    {
        $query = $this->db->get_where('tags', array('id' => $tagID));
        if($query->num_rows() > 0)
            $row = $query->row_array();
        else 
            $row = "error";
        return $row;        
    }


    function tag_name_by_ID($tagIDs = array()) {
        $tags = array();
        $query = $this->db->query("SELECT * FROM tags WHERE id IN ('" . implode("', '", $tagIDs) . "')");        
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $tags[] = $row->name;
            }
       }
           
        return $tags;
    }

    function insert_tag($name, $status) {
        $this->name = $name;
        $this->status = $status;
        $this->db->insert('tags', $this);

        if($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
    }


    function delete($tagID) {
        $this->delete_relation($tagID, "tags");
    }
  
    function tag_form($url, $name = "", $status = "Active") {
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
          <label for="tag">Tag:</label> <input type="text" id="tag_name" name="tag_name" value="' . $name . '" /><br />
          <label for="Status">Status: </label>
          ' . $statusDropDown . '

        <input type="submit" name="submit" value="Submit" />
        </form>
        ';        
    }
}

?>