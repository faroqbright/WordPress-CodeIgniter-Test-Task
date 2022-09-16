<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cake extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }
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
	 * @see https://codeigniter.com/userguide3/general/urls.html
	 */
	 
	 public function index()
	 {
	     echo "asd";die;
	 }
	public function save()
	{
        $this->form_validation->set_rules('cake_name', 'cake_name', 'required');
        $this->form_validation->set_rules('cake_type', 'cake_type', 'required');
        $this->form_validation->set_rules('cake_price', 'cake_price', 'numeric|required');

		if ($this->form_validation->run() === FALSE)
        {  
			$arr = array('success' => false, 'message'=>  'All Fields are required');    
        }else{
			$cake_name = $this->input->post('cake_name');
			$cake_type = $this->input->post('cake_type');
			$cake_price = $this->input->post('cake_price');
			
			$cake = [
				'name'=> $cake_name,
				'type'=> $cake_type,
				'price'=> $cake_price,
			];

			$status = $this->db->insert('cakes',$cake);
			if($status){
				$arr = array('success' => true, 'message'=>  'Data Saved Successfully');    
			}else{
				$arr = array('success' => false, 'message'=>  'Some Thing Went Wrong, Try Again later');    
			}
		}

		return $this->response($arr);
	}

	public function get()
	{
		$cakes = $this->db->get('cakes')->result_array();
		$arr = array('success' => true, 'message'=>  'Data retrived', 'data'=> $cakes);  
		return $this->response($arr);
	}

	public function get_cake_info($cake_id = "")
	{
		if(empty($cake_id)){
			$arr = array('success' => false, 'message'=>  'Cake Id required');  
			return $this->response($arr);
		}

		$cake_info = $this->db->get_where('cakes',array('id' => $cake_id))->row();
		$arr = array('success' => true, 'message'=>  'Data retrived', 'data'=> $cake_info);  
		return $this->response($arr);
	}

	public function del_cake($cake_id = "")
	{
		if(empty($cake_id)){
			$arr = array('success' => false, 'message'=>  'Cake Id required');  
			return $this->response($arr);
		}

		$delete = $this->db->delete('cakes', array('id' => $cake_id));
		if($delete){
			$arr = array('success' => true, 'message'=>  'Cake info deleted successfully');    
		}else{
			$arr = array('success' => true, 'message'=>  'Some thing went wrong, try again later');
		}
		return $this->response($arr);
	}

	public function sale()
	{
		$this->form_validation->set_rules('user_id', 'user_id', 'required');
        $this->form_validation->set_rules('cake_id', 'cake_id', 'required');

		if ($this->form_validation->run() === FALSE)
        {  
			$arr = array('success' => false, 'message'=>  'All Fields are required');    
        }else{
			$user_id = $this->input->post('user_id');
			$cake_id = $this->input->post('cake_id');
	
			$cake_info = $this->db->get_where('cakes',array('id' => $cake_id))->row();
			if(empty($cake_info)){
				$arr = array('success' => false, 'message'=>  'Invalid Cake Selected'); 
			}else{
				$sale = [
					'user_id' => $user_id,
					'cake_id' => $cake_id,
				];
		
				$status = $this->db->insert('sale',$sale);
				if($status){
					$arr = array('success' => true, 'message'=>  'Data Saved Successfully');    
				}else{
					$arr = array('success' => false, 'message'=>  'Some Thing Went Wrong, Try Again later');    
				}
			}
		}

		return $this->response($arr);
	}

	public function response($arr)
	{
		header('Content-Type: application/json');
		echo json_encode( $arr );
	}
	
	
	
	public function bilal()
	{
	    // echo "bilal";
	    $this->form_validation->set_rules('name', 'name', 'required');
	    $cakes = $this->db->get('cakes')->result_array();
		$arr = array('success' => true, 'message'=>  'Data retrived', 'data'=> $cakes);  
	    return $this->response($arr);
	}
	
	public function search()
	{
	    $this->form_validation->set_rules('search_with', 'search_with', 'required');
		if ($this->form_validation->run() === FALSE)
        {  
			$arr = array('success' => false, 'message'=>  'search With Field Is required');   
			
        }else{
             $this->db->like('name', $this->input->post('search_with'));
            $this->db->order_by("name", "asc");
            $cakes = $this->db->get('cakes')->result();
            $arr = array('success' => true, 'message'=>  'Data retrived', 'data'=> $cakes);  
        }
         return $this->response($arr);
	}
	
	
}
