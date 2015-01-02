<?php
class My_Model extends CI_Model {


    function insert($name, $status)
    {
        $data = array(
            'name' => $name,
            'status' => $status
            );

        $this->db->insert($this->db_table, $data);

        if($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;        
    }	

    function update($name, $status, $id)
    {
    	$table = $this->db_table;

        $data = array(
            'name' => $name,
            'status' => $status
            );

        $this->db->update($this->db_table, $data, array('id' => $id));

        if($this->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;        
    }

    function delete($id) {
        if(!is_numeric($id))
            errorPage("ID is not a valid #");
    	$dbtable = $this->db_table;

        $query = $this->db->get_where($dbtable, array('id' => $id));
        if($query->num_rows() > 0)
            {
                $this->db->delete($dbtable, array('id' => $id)); 
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