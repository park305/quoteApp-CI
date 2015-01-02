<?php
function errorPage($message = "", $title = "") 
{
	$CI =& get_instance();
	$data['title'] = $title;
	$CI->load->view('templates/header', $data);
	$data['errorMsg'] = "<hr />" . $message;
	$CI->load->view('templates/error', $data);		
	$CI->load->view('templates/footer', $data);		
	$CI->output->_display();
	exit();		
	
}

function check_category($quoteID, $categoryID = 0) {
	$CI =& get_instance();
    $query = $CI->db->select('*')->where('quoteID = ' . $quoteID)->where('relationType = \'category\'')->limit(1)->get("quotes_relationships");

    if($query->num_rows() > 0)
    {
    	$row = $query->row();
    	if($categoryID === $row->relationID)
    		return "same";
    	else 
    		return $row->id;
    } else
    	return false;

	}

  function check_relation_exists($table, $col1, $col2, $col3)
{
	$CI =& get_instance();
	$col3 = "'" . $col3 . "'";
    //$query = $CI->db->select('1', FALSE)->where('quoteID = ' . $col1)->where('relationID = ' . $col2)->where('relationType = ' . $col3)->limit(1)->get($table);
    $query = $CI->db->select('*')->where('quoteID = ' . $col1)->where('relationID = ' . $col2)->where('relationType = ' . $col3)->limit(1)->get($table);

    if($query->num_rows() > 0)
    {
    	$row = $query->row();
    	return $row->id;
    } else
    	return false;
    //return ($query->num_rows() > 0) ? TRUE : FALSE;
}  

?>