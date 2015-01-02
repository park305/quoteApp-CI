<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Homepage extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */

	//JSON the data random quote

    public function __construct(){
        parent::__construct();
        $this->load->model('tag_model');
        $this->load->model('category_model');        
    }

	public function index()
	{
		$data['random_quote'] = $this->quote_model->random_quote();
		$this->load->view('templates/header');
		$this->load->view('homepage/index', $data);
		$this->load->view('templates/footer');
	}

	public function ajaxQuote() 
	{
		echo $this->quote_model->random_quote();		
	}

	public function author($author) 
	{
		$data['author'] = $author;
		$data['quotes'] = $this->quote_model->get_author($author);		
		$data['title'] = "Author : " . $author;
		$this->load->view('templates/header', $data);
		$this->load->view('homepage/author', $data);
		$this->load->view('templates/footer');

	}

	public function single($quoteID) 
	{
		if(!is_numeric($quoteID)) 
			errorPage("error here, not numeric given");

		$data['quoteRow'] = $this->quote_model->get_quote($quoteID);
		if($data['quoteRow'] == "error") 
			errorPage("error quote doesn't exist for ID given");

		//fetch category
		//fetch tags
        $query = $this->db->get_where('quotes_relationships', array('quoteID' => $quoteID));
        if ($query->num_rows() > 0) {
        	$tags = array();
            foreach ($query->result_array() as $row) {
                //array_push($tags, $row['name']);
                if($row['relationType'] == "category") {
                	$category_row = $this->category_model->get_category($row['relationID']);                	
                	$data['category'] = $category_row['name'];
                } else if($row['relationType'] == "tag") {
                	array_push($tags, $row['relationID']);
                }
            }        
            $data['tags'] = $this->tag_model->tag_name_by_ID($tags);
        }

		$data['title'] = $data['quoteRow']['quote'];		
		$this->load->view('templates/header', $data);
		$this->load->view('homepage/single', $data);		
		$this->load->view('templates/footer');

	}
	public function category($category = "General")
	{
		if(!is_string($category)) 
			errorPage("error incorrect category given");

		$quotes = array();
		$category_row = $this->category_model->get_category_by_name($category);
		if($category_row === FALSE)
			errorPage("could not find such category");
		
        $query = $this->db->get_where('quotes_relationships', array('relationType' => 'category', 'relationID' => $category_row['id']));
        if($query->num_rows() > 0) {
        	foreach($query->result() as $row) {
	        	$quote = $this->quote_model->get_quote($row->quoteID);
	        	if($quote != "error")
	        		$quotes[] = $quote;
        	}
        	$data['quotes']['rows'] = $quotes;
        	$data['quotes']['count'] = count($quotes);
        } else {
        	$data['quotes']['count'] = 0;
        	$data['quotes']['rows'] = "There are no quotes for this category";
        }

		$data['title'] = "Category: " . $category;		
		$data['category'] = $category;
		$this->load->view('templates/header', $data);
		$this->load->view('homepage/category', $data);		
		$this->load->view('templates/footer');
	}

	public function tag($tagName = "General")
	{
		if(!is_string($tagName)) 
			errorPage("error incorrect tag given");

		$quotes = array();
		$tagID = $this->tag_model->fetch_tagIDs(array($tagName));
		if(empty($tagID))
			errorPage("could not find such tag");
		$tagID = $tagID[0];
        $query = $this->db->get_where('quotes_relationships', array('relationType' => 'tag', 'relationID' => $tagID));
        if($query->num_rows() > 0) {
        	foreach($query->result() as $row) {
	        	$quote = $this->quote_model->get_quote($row->quoteID);
	        	if($quote != "error")
	        		$quotes[] = $quote;
        	}
        	$data['quotes']['rows'] = $quotes;
        	$data['quotes']['count'] = count($quotes);
        } else {
        	$data['quotes']['count'] = 0;
        	$data['quotes']['rows'] = "There are no quotes for this tag";
        }

		$data['title'] = "Tag: " . $tagName;		
		$data['tag'] = $tagName;
		$this->load->view('templates/header', $data);
		$this->load->view('homepage/tag', $data);		
		$this->load->view('templates/footer');

	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */