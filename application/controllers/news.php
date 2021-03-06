<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
class News extends MY_Controller 
{

	public function index()
	{
		
		// Data for news view
		$this->load->model('News_model');
		$main_data['news_array'] = $this->News_model->get_latest_news();
		$main_data['lang'] = $this->lang_data;
		
		// composing the views
		$template_data['menu'] = $this->load->view('includes/menu',$this->lang_data, true);
		$template_data['main_content'] = $this->load->view('carouselle','', true);
		$template_data['main_content'] .= $this->load->view('news', $main_data, true);						
		$template_data['sidebar_content'] =  $this->sidebar->get_standard();
		$this->load->view('templates/main_template',$template_data);
	}
	
	public function view($id) 
	{
		// Data for news view
		$this->load->model('News_model');
		$main_data['news'] = $this->News_model->get_news($id);
		$main_data['lang'] = $this->lang_data;
		
		// composing the views
		$template_data['menu'] = $this->load->view('includes/menu',$this->lang_data, true);
		$template_data['main_content'] = $this->load->view('news_full', $main_data, true);						
		$template_data['sidebar_content'] =  $this->sidebar->get_standard();
		$this->load->view('templates/main_template',$template_data);
	}
}
