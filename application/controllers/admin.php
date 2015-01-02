<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

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
	public function index()
	{
		$data['title'] = "Admin Homepage";
		$this->load->view('templates/header', $data);
		$this->load->view('admin/index', $data);		
		$this->load->view('templates/footer');
	}


	public function insertCSV() {
		$quotes = file("QuotesFromForbes.txt");
		$quotes2 = file("QuotesFromBrainyQuote.txt");
		$quotes = array_merge($quotes, $quotes2);


		for ($i = 0; $i < count($quotes); $i++) {
			$pieces = explode(";", $quotes[$i]);
			$quote = trim($pieces[0]);
			$author = trim($pieces[1]);

			if(is_string($quote) AND is_string($author)) {
				$data = array('quote' => $quote, 'author' => $author);
				$this->db->insert("quotes", $data);
			}
		}
	}


	function tag_dup($tag) {
        $this->form_validation->set_message('tag_dup', 'Tag already exists.');		
        $query = $this->db->get_where('tags', array('name' => $tag));
        if($query->num_rows() > 0)
       		return FALSE;
        return TRUE;
	}
	function category_dup($name) {
        $this->form_validation->set_message('category_dup', 'Category already exists.');		
        $query = $this->db->get_where('categories', array('name' => $name));
        if($query->num_rows() > 0)
       		return FALSE;
        return TRUE;
	}

	function status_valid($status) {
        $this->form_validation->set_message('status_valid', 'Status is not valid.');		
        if($status == "Active" || $status == "Hidden")
        	return TRUE;
        return FALSE;
	}

	function tags_valid($tags_arr) {
		if(count($tags_arr) === 0)
			return TRUE;

        $this->form_validation->set_message('tags_valid', 'Chosen tags are not valid.');		
//        $sql = "SELECT * FROM tags WHERE status='Active' AND name IN ('?')";
   //     $tags = implode("', '", $tags_arr);
        $query = $this->db->query("SELECT * FROM tags WHERE status='Active' AND name IN ('" . implode("', '", $tags_arr) . "')");
//        $query = $this->db->query($sql, $tags);
        if ($query->num_rows() > 0)
			return TRUE; 
		return FALSE;
	}	

	function category_valid($str) {
        $this->form_validation->set_message('category_valid', 'Chosen category is not valid.');		
		return TRUE; 
		return FALSE;
	}

	public function manageQuotes($editMsg = "")
	{
		$data['success'] = $editMsg;
		$data['title'] = "Admin : Manage Quotes";
		$data['quotes'] = $this->quote_model->all_quotes();
		$this->load->view('templates/header', $data);
		$this->load->view('admin/manage_quotes', $data);		
		$this->load->view('templates/footer');
	}


	public function addQuote()
	{
		$data['title'] = "Admin : Add Quote";
//checking to make sure quote is unique?
		$this->form_validation->set_rules('quote', 'Quote', 'required|trim');
		$this->form_validation->set_rules('author', 'Author', 'required|trim');
		$this->form_validation->set_rules('category', 'Category', 'required|trim|callback_category_valid');
		$this->form_validation->set_rules('tags', 'Tags', 'callback_tags_valid');

		$quote = $this->input->post('quote');
		$author = $this->input->post('author');
		$categoryID = $this->input->post('category');
		$tags = $this->input->post('tags');

		if ($this->form_validation->run() === FALSE) {
			;
		} else {
			$data['success'] = "Quote by " . $author . " was added!";
			$this->quote_model->insert_quote($quote, $author, $categoryID, $tags);
			$categoryID = $tags = $author = $quote = "";
		}

		$this->load->view('templates/header', $data);
		$data['quote_form'] = $this->quote_model->quote_form("admin/addQuote", $quote, $author, $categoryID, $tags);
		$this->load->view('admin/add_quote', $data);				
		$this->load->view('templates/footer');
	}

	public function editQuote($quoteID = 0) {
		//validate quoteID exists and is a #
		if(!is_numeric($quoteID))
			errorPage("Quote ID is not a valid number");
		
		$quoteRow = $this->quote_model->get_quote($quoteID);		
		if($quoteRow == "error")
			errorPage("Quote does not exist for this ID#");

		$data['title'] = "Admin : Edit Quote";
		$this->load->model('tag_model');
		$this->form_validation->set_rules('quote', 'Quote', 'required|trim');
		$this->form_validation->set_rules('author', 'Author', 'required|trim');
		$this->form_validation->set_rules('category', 'Category', 'required|trim|callback_category_valid');
		$this->form_validation->set_rules('tags', 'Tags', 'callback_tags_valid');

		if ($this->form_validation->run() === FALSE) {			
			;
		} else {
			$quote = $this->input->post('quote');
			$author = $this->input->post('author');
			$categoryID = $this->input->post('category');
			$tags = $this->input->post('tags');
			$data['success'] = "Quote by " . $author . " was changed!";
			$this->quote_model->update_quote($quote, $author, $quoteID, $categoryID, $tags);
			$categoryID = $tags = $author = $quote = "";
		}

		$quoteRow = $this->quote_model->get_quote($quoteID);		
		$quote = $quoteRow['quote'];
		$author = $quoteRow['author'];
		//$categoryID

		//$tags
        $tags = array();
        $query = $this->db->get_where('quotes_relationships', array('quoteID' => $quoteID));
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                //array_push($tags, $row['name']);
                if($row['relationType'] == "category") {
                	$categoryID = $row['relationID'];
                } else if($row['relationType'] == "tag") {
                	array_push($tags, $row['relationID']);
                }
            }        
            $tags = $this->tag_model->tag_name_by_ID($tags);
        }		
		$data['quote_form'] = $this->quote_model->quote_form("admin/editQuote/" . $quoteID, $quote, $author, $categoryID, $tags);		
		$this->load->view('templates/header', $data);
		$this->load->view('admin/edit_quote', $data);				
		$this->load->view('templates/footer');		
	}


	public function deleteQuote($quoteID) {
		$result = $this->quote_model->delete($quoteID);
		//$this->db->delete('quotes', array('id' => $quoteID)); 
		$this->manageQuotes($result);
	}

	public function uploadCSV()
	{
		$data['results'] = array();

		if($this->input->server('REQUEST_METHOD') === 'POST') {
		  $target_dir = "uploads/";
		  $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]) ;
		  $uploadOk = 1;
		  $uploadFileType = pathinfo($target_file, PATHINFO_EXTENSION);

		  switch($_FILES['fileToUpload']['error'])
		  {
		  case 0:
		    // file found
		    if($_FILES['fileToUpload']['name'] != NULL)  {

		        // Check if file already exists
		        if (file_exists($target_file)) {
		            $data['results'][] = "Sorry, file already exists.";
		            $uploadOk = 0;
		        }
		        // Check file size
		        if ($_FILES["fileToUpload"]["size"] > 500000) {
		            $data['results'][] =  "Sorry, your file is too large.";
		            $uploadOk = 0;
		        }
		        // Allow certain file formats
		        if($uploadFileType != "csv" ) {
		            $data['results'][] = "Sorry, only CSV files are allowed.";
		            $uploadOk = 0;
		        }


		        // Check if $uploadOk is set to 0 by an error
		        if ($uploadOk == 0 ) {
		            $data['results'][] = "Error uploading file.";	        	
		        // if everything is ok, try to upload file
		        } else {
		            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
		                $data['results'][] = "The file ". basename($_FILES["fileToUpload"]["name"] ) . " has been uploaded.";
		                //do the uploading shit here

		                $quotes = file( $target_file );

		                for ($i = 0; $i < count($quotes ); $i++) {
		                  $pieces = explode(";", $quotes[$i]);
		                  if(!isset($pieces[0]) OR !isset($pieces[1]))
		                  	continue;
		                  $quote = trim($pieces[0]);
		                  $author = trim($pieces[1]);

	  					  $result = $this->quote_model->insert_quote($quote, $author);
	                  	  if($result === TRUE)
	                  	  	  $data['results'][] = "Quote \"" . $quote . "\" added to database";
	                  	  else
	                  	  	  $data['results'][] = $result;

		                } 		//end for loop iteration through CSV rows

		            } else {
		                $data['results'][] = "Sorry, there was an error uploading your file.";
		            }
		        }
		      }
		    break;

		  case (1|2):
		    // upload too large
			$data['results'][] = "file upload is too large";
		    break;

		  case 4:
		    // no file uploaded
		    break;

		  case (6|7):
		    // no temp folder or failed write – server config errors
		    $data['results'][] = "internal error – flog the webmaster";
		    break;
		  }
		}

		$data['title'] = "Admin : Upload Quotes CSV";
		$this->load->view('templates/header', $data);
		$this->load->view('admin/upload_csv', $data);		
		$this->load->view('templates/footer');
	}

	public function manageTags($editMsg = "")
	{
		$this->load->model("tag_model");
		$data['tags'] = $this->tag_model->get_tag_array("all");		
		$data['title'] = "Admin : Manage Tags";
		$data['success'] = $editMsg;

		$this->load->view('templates/header', $data);
		$this->load->view('admin/manage_tags', $data);		
		$this->load->view('templates/footer');
	}

	public function editTag($tagID = 0)
	{
		//validate tagID is # and exists in DB
		if(!is_numeric($tagID))
			errorPage("Tag ID is not a valid number");

		$this->load->model("tag_model");
		$tagrow = $this->tag_model->tag_name_by_ID(array($tagID));		
		if(empty($tagrow))
			errorPage("Tag does not exist for this ID#");
			
		$this->form_validation->set_rules('tag_name', 'Tag Name', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim|callback_tagstatus_valid');
	

		if ($this->form_validation->run() === FALSE) {			
			;
		} else {
			$name = $this->input->post('tag_name');
			$status = $this->input->post('status');
			$data['success'] = "Tag has been changed!";
			$this->tag_model->update_tag($name, $status, $tagID);
			$name = $status = "";
		}
		//fetch tag info
		$tag = $this->tag_model->get_tag($tagID);
		$data['tag_form'] = $this->tag_model->tag_form("admin/editTag/" . $tagID, $tag['name'], $tag['status']);		

		$data['title'] = "Admin : Edit Tag: " . $tag['name'];
		$this->load->view('templates/header', $data);
		$this->load->view('admin/edit_tag', $data);		
		$this->load->view('templates/footer');
	}

	public function deleteTag($tagID = 0)
	{
		$this->load->model("tag_model");
		$result = $this->tag_model->delete($tagID);
		$this->manageTags($result);
	}

	public function addTag()
	{
		$data['title'] = "Admin : Add Tag";
		$this->load->model("tag_model");
		$this->form_validation->set_rules('tag_name', 'Tag Name', 'required|trim|callback_tag_dup');
		$this->form_validation->set_rules('status', 'Status', 'required|trim|callback_tagstatus_valid');
		$name = $status = "";

		if($this->input->server('REQUEST_METHOD') === 'POST') {
			$name = $this->input->post('tag_name');
			$status = $this->input->post('status');
		}


		if ($this->form_validation->run() === FALSE) {
			;
		} else {
			$result = $this->tag_model->insert_tag($name, $status);
			if($result === TRUE)
			{
				$data['success'] = "Successfully added tag: " . $name;
				$status = $name =  "";
			}
			else if($result === FALSE)
				$data['success'] = "Unknown error adding tag";
		}

		$this->load->view('templates/header', $data);
		$data['tag_form'] = $this->tag_model->tag_form("admin/addTag", $name, $status);
		$this->load->view('admin/add_tag', $data);				
		$this->load->view('templates/footer');
	}


	public function manageCategories($editMsg = "")
	{
		$data['title'] = "Admin : Manage Categories";
		$data['success'] = $editMsg;
		
		$this->load->model("category_model");
		$data['categories'] = $this->category_model->get_category_array("all");		
		$this->load->view('templates/header', $data);
		$this->load->view('admin/manage_categories', $data);		
		$this->load->view('templates/footer');
	}	

	public function editCategory($categoryID)
	{
		if(!is_numeric($categoryID))
			errorPage("ID is not a valid number");

		$this->load->model("category_model");
		

		$row = $this->category_model->get_category($categoryID);		
		if(empty($row))
			errorPage("Category does not exist for this ID#");
			
		$this->form_validation->set_rules('name', 'Name', 'required|trim');
		$this->form_validation->set_rules('status', 'Status', 'required|trim|callback_status_valid');
	

		if ($this->form_validation->run() === FALSE) {			
			;
		} else {
			$name = $this->input->post('name');
			$status = $this->input->post('status');
			$data['success'] = "Category has been changed!";
			$this->category_model->update_category($name, $status, $categoryID);
			$name = $status = "";
		}

		$row = $this->category_model->get_category($categoryID);
		$data['category_form'] = $this->category_model->category_form("admin/editCategory/" . $categoryID, $row['name'], $row['status']);		

		$data['title'] = "Admin : Edit Category: " . $row['name'];
		$this->load->view('templates/header', $data);
		$this->load->view('admin/edit_category', $data);		
		$this->load->view('templates/footer');
	}

	public function addCategory()
	{
		$data['title'] = "Admin : Add Category";
		$this->load->model("category_model");
		$this->form_validation->set_rules('name', 'Category Name', 'required|trim|callback_category_dup');
		$this->form_validation->set_rules('status', 'Status', 'required|trim|callback_categorystatus_valid');
		$name = $status = "";

		if($this->input->server('REQUEST_METHOD') === 'POST') {
			$name = $this->input->post('name');
			$status = $this->input->post('status');
		}


		if ($this->form_validation->run() === FALSE) {
			;
		} else {
			$result = $this->category_model->insert_category($name, $status);
			if($result === TRUE)
			{
				$data['success'] = "Successfully added category: " . $name;
				$status = $name =  "";
			}
			else if($result === FALSE)
				$data['success'] = "Unknown error adding category";
		}

		$this->load->view('templates/header', $data);
		$data['tag_form'] = $this->category_model->category_form("admin/addCategory", $name, $status);
		$this->load->view('admin/add_category', $data);				
		$this->load->view('templates/footer');		
	}
	
	public function deleteCategory($categoryID = 0)
	{
		$this->load->model("category_model");
		$result = $this->category_model->delete($categoryID);
		$this->manageCategories($result);
	}


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */