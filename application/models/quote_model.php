<?php
class Quote_model extends My_Model {

    var $quote   = '';
    var $author = '';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function random_quote()
    {
        $query = $this->db->query('SELECT quote, author FROM quotes AS quotetbl JOIN
            (SELECT (RAND() * (SELECT MAX(id) FROM quotes)) AS id) AS r2
            WHERE quotetbl.id >= r2.id
            ORDER BY quotetbl.id ASC LIMIT 1');
        $row = $query->row();

        if(is_string($row->quote) AND is_string($row->author))
           return $row->quote . " - " . $row->author . "<br />";      
    }
    function all_quotes() 
    {
        $query = $this->db->get("quotes");
        return $query;
    }

    function get_author($author) {
        $author = urldecode($author);
        $query = $this->db->get_where('quotes', array('author' => $author));
        if($query->num_rows() > 0)
            return $query;
        else
            errorPage("Author (" . $author . ") does not exist");
    }

    function get_quote($quoteID = 0) 
    {
        if(!is_numeric($quoteID)) 
            errorPage("error here, not numeric given");

        //validate quoteID is an integer
        //fetch from db
        $query = $this->db->get_where('quotes', array('id' => $quoteID));
        if($query->num_rows() > 0)
            $row = $query->row_array();
        else 
            $row = "error";
        return $row;

    }

    function delete($quoteID) {
        if(!is_numeric($quoteID))
            errorPage("Quote ID is not a valid #");
        $query = $this->db->get_where('quotes', array('id' => $quoteID));
        if($query->num_rows() > 0)
            {
                $this->db->delete('quotes', array('id' => $quoteID)); 
                if($this->db->affected_rows() > 0)
                    return "Quote " . $quoteID . " successfully deleted";
                else
                    return "Error deleting " . $quoteID;
            }
        else
            return "Quote " . $quoteID . " does not exist, could not delete";
    }

    function quote_exists($quote = "")
    {
        $query = $this->db->get_where('quotes', array('quote' => $quote));
        if($query->num_rows() > 0)
            return TRUE;
        return FALSE;
    }

    function insert_quote($quote = "", $author = "", $categoryID = 1, $tags = array())
    {

        if($quote === "" OR $author === "")
            return "Quote or author field is empty";

        if($this->quote_exists($quote))
            return "Quote: \"" . $quote . "\" already exists";

        $this->quote = $quote;
        $this->author = $author;
        $this->db->insert('quotes', $this);
        if($this->db->affected_rows() > 0) {
            $quoteID = $this->db->insert_id();

            $data = array(
                'quoteID' => $quoteID,
                'relationID' => $categoryID,
                'relationType' => 'category'
                );
            $this->db->insert('quotes_relationships',$data);


            if(!empty($tags)) {
                $this->load->model("tag_model");
                $data = array(
                    'quoteID' => $quoteID,           
                    'relationType' => 'tag'
                    );
                $tagIDs = $this->tag_model->fetch_tagIDs($tags);
                foreach($tagIDs as $tagID) {
                    $data['relationID'] = $tagID;
                    $this->db->insert('quotes_relationships',$data);                
                }
            }
        }
        return TRUE;
    }

    function update_quote($quote, $author, $id, $categoryID = 0, $tags = array())
    {
        $this->quote = $quote;
        $this->author = $author;

        $this->db->update('quotes', $this, array('id' => $id));



        $data = array(
            'quoteID' => $id,
            'relationID' => $categoryID,
            'relationType' => 'category'
            );
         $row_id = check_category($id, $categoryID);
        if($categoryID != 0 AND $row_id > 0)
            $this->db->update('quotes_relationships', $data, array('id' => $row_id));
        else if($categoryID != 0 AND $row_id != "same") 
            $this->db->insert('quotes_relationships',$data);
        //else do nothing, it's the same category

        if(empty($tags)) 
        {
            $sql = "DELETE FROM quotes_relationships WHERE quoteID=? AND relationType='tag'";
            $this->db->query($sql, array($id));
        } else {
            $this->load->model("tag_model");
            $data['relationType'] = 'tag';
            $tagIDs = $this->tag_model->fetch_tagIDs($tags);
            //delete from relationships where quoteid=id and type='tag' and relationID NOT IN (tagids array)            
            $sql = "DELETE FROM quotes_relationships WHERE quoteID=? AND relationType='tag' AND relationID NOT IN (?)";
            $tags_db = implode("', '", $tagIDs);
            $this->db->query($sql, array($id, $tags_db));
            foreach($tagIDs as $tagID) {
                $data['relationID'] = $tagID;
                if(!check_relation_exists("quotes_relationships", $id, $tagID, 'tag'))
                    $this->db->insert('quotes_relationships',$data);                
            }
        }

    }

    function quote_form($url, $quote = "", $author = "", $categoryID = 1, $tags = array())
    {
        $tagRadioElements = "";
        $categoryDropDown = "";
        if($tags === "")
            $tags = array();


        $this->load->model("category_model");
        $categories = $this->category_model->get_category_array();
        $categoryDropDown = '<select name="category" id="category">';                  
        if (count($categories) > 0)
            foreach ($categories as $row) {
                if(is_string($row['name']) AND $row['status'] == "Active" AND $row['id'] == $categoryID)
                   $categoryDropDown .= '<option value="' . $row['id'] . '" selected>' . $row['name'] . '</option>';
                 else if(is_string($row['name']) AND $row['status'] == "Active")
                   $categoryDropDown .= '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
            }
        $categoryDropDown .= "</select>";


        $this->load->model("tag_model");
        $dbtags = $this->tag_model->get_tag_array();
        if (count($dbtags) > 0)
            foreach ($dbtags as $row) {
                if(is_string($row['name']) AND !empty($tags) AND $row['status'] == "Active" AND in_array($row['name'], $tags))
                    $tagRadioElements .= '<input type="checkbox" id="tags" name="tags[]" value="' . $row['name'] . '" checked> ' . $row['name'] . '<br />';          
                else if(is_string($row['name']) AND $row['status'] == "Active")
                    $tagRadioElements .= '<input type="checkbox" id="tags" name="tags[]" value="' . $row['name'] . '"> ' . $row['name'] . '<br />';
            }

        return form_open($url) . '
          <label for="quote">Quote:</label> <textarea id="quote" name="quote">' . $quote . '</textarea> <br />
          <label for="author">Author: </label> <input type="text" id="author" value="' . $author . '" name="author" /><br />
          <label for="tags">Tags: </label>
          ' . $tagRadioElements . '
          <label for="category">Category: </label>
          ' . $categoryDropDown . '

        <input type="submit" name="submit" value="Submit" />
        </form>
        ';

    }

}

?>