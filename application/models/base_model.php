<?php
class Model_base extends CI_Model {



    function delete_relation($id, $type) {
        if(!is_numeric($id))
            errorPage("ID is not a valid #");
        $query = $this->db->get_where($type, array('id' => $id));
        if($query->num_rows() > 0)
            {
                $this->db->delete($type, array('id' => $id)); 
                if($this->db->affected_rows() > 0)
                    return $id . " successfully deleted";
                else
                    return "Error deleting " . $id;
            }
        else
            return $id . " does not exist, could not delete";
    }
  
}
?>