<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_user extends MY_Controller
{

	public $languages = '';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();

		if(!$this->login->is_admin())
		{
			redirect('/admin/access_denied', 'refresh');
		}

		// access granted, loading modules
		$this->load->model('user_model');
		$this->load->helper('form');

		$this->languages = array	(
										array(	'language_abbr' => 'se',
												'language_name' => 'Svenska',
												'id' => 1),
										array(	'language_abbr' => 'en',
												'language_name' => 'English',
												'id' => 2)
									);
    }

	public function index()
	{
		$this->user_start();
	}

	function user_start()
	{

		// Data for overview view
		$this->load->model('User_model');
		$main_data['user_list'] = $this->User_model->get_all_users();
		$main_data['lang'] = $this->lang_data;

		// composing the views
		$template_data['menu'] = $this->load->view('includes/menu',$this->lang_data, true);
		$template_data['main_content'] = $this->load->view('admin/user/start',  $main_data, true);
		$template_data['sidebar_content'] =  $this->sidebar->get_standard();
		$this->load->view('templates/main_template',$template_data);
	}

	function user_list($option = 'all', $page = 0)
	{
		// Pass along informatino about what is beeing looked at
		$currentview = array(
							'page' => $page,
							'rowsperpage' => 30,
							'option' => $option
						);

		// load model and table library
		$this->load->model('User_model');
		$this->load->library('table');

		// Data for overview view
		$main_data['user_list'] = $this->User_model->get_all_users($currentview['rowsperpage'], $page, $option);
		$main_data['user_num'] = $this->User_model->count_all_users($option);
		$main_data['lang'] = $this->lang_data;
		$main_data['currentview'] = $currentview;

		// composing the views
		$template_data['menu'] = $this->load->view('includes/menu',$this->lang_data, true);
		$template_data['main_content'] = $this->load->view('admin/user/list',  $main_data, true);
		$template_data['sidebar_content'] =  $this->sidebar->get_standard();
		$this->load->view('templates/main_template',$template_data);
	}

	function edit_user($id, $do = '')
	{
		$this->load->model('User_model');

		if($do == 'edit')
		{
			$web = $this->input->post('web');
			$li = $this->input->post('linkedin');
			$twitter = $this->input->post('twitter');
			$presentation = $this->input->post('presentation');
			$firstname = $this->input->post('firstname');
			$lastname = $this->input->post('lastname');
			$lukasid = $this->input->post('lukasid');
			$password = $this->input->post('password');

			$main_data['edit_data'] = $this->User_model->edit_user_data($id, $web, $li, $twitter, $presentation, '');
			$main_data['edit_user'] = $this->User_model->edit_user($id, $firstname, $lastname, $lukasid, $password);
		}
		elseif($do == 'chstatus')
		{
			$main_data['chstatus'] = $this->User_model->disableswitch($id);
		}

		// Data for overview view
		$main_data['user'] = $this->User_model->get_user_profile($id);
		$main_data['lang'] = $this->lang_data;
		$main_data['whattodo'] = $do;

		// composing the views
		$template_data['menu'] = $this->load->view('includes/menu',$this->lang_data, true);
		$template_data['main_content'] = $this->load->view('admin/user/edit',  $main_data, true);
		$template_data['sidebar_content'] =  $this->sidebar->get_standard();
		$this->load->view('templates/main_template',$template_data);
	}

	function user_add($do = '')
	{
		$main_data['lang'] = $this->lang_data;
		$this->load->model('User_model');

		if($do == 'create') // if form is sent
		{

			$fn = $this->input->post('firstname');
			$ln = $this->input->post('lastname');
			$lid = $this->input->post('lukasid');
			$pwd = $this->input->post('password');

			$main_data['entered'] = array(
										'fname' => $fn,
										'lname' => $ln,
										'lid' => $lid,
										'pwd' => $pwd,
									);

			$createuser = $this->User_model->add_user($fn, $ln, $lid, $pwd);

			// pass along error messages
			if(!$createuser)
			{
				$errormsg = '';

				if(strlen(trim($fn)) == 0)
					$errormsg .= $this->lang_data['admin_addusers_error_fname'].' ';
				if(strlen(trim($ln)) == 0)
					$errormsg .= $this->lang_data['admin_addusers_error_lname'].' ';
				if(strlen(trim($lid)) !== 8 || $this->user_model->lukasid_exists($lid))
					$errormsg .= $this->lang_data['admin_addusers_error_lid'].' ';
				if(strlen(trim($pwd)) <= 5)
					$errormsg .= $this->lang_data['admin_addusers_error_pwd'].' ';

				$main_data['errormsg'] = $errormsg;
			}

			$main_data['status'] = $createuser;
		}

		// composing the views
		$template_data['menu'] = $this->load->view('includes/menu',$this->lang_data, true);
		$template_data['main_content'] = $this->load->view('admin/user/add',  $main_data, true);
		$template_data['sidebar_content'] =  $this->sidebar->get_standard();
		$this->load->view('templates/main_template',$template_data);
	}

	function user_search($do = '', $search = '')
	{
		$main_data['lang'] = $this->lang_data;
		$this->load->model('User_model');
		$this->load->library('table');

		if($do == 'run') // if form is sent
		{
			redirect('/admin_user/user_search/find/'.$this->input->post('search'), 'refresh');
		}
		elseif($do == 'find')
		{
			$main_data['result'] = $this->User_model->search_user($search);
			$main_data['query'] = $search;
		}

		// composing the views
		$template_data['menu'] = $this->load->view('includes/menu', $this->lang_data, true);
		$template_data['main_content'] = $this->load->view('admin/user/search',  $main_data, true);
		$template_data['sidebar_content'] =  $this->sidebar->get_standard();
		$this->load->view('templates/main_template',$template_data);
	}

}
?>
