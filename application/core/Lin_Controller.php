<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Lin extends CI_Controller {

	public $control, $action, $usernmae, $role_id, $admin_id, $menu_id, $name;
	public $no_login = array();
	public $admin_look = array();
	public $admin_add = array();
	public $admin_edit = array();
	public $admin_del = array();
	public $admin = array();

	public function __construct() {
		parent::__construct();
		$this->control = $this->uri->segment(1);
		$this->action = $this->uri->segment(2);
		$admin_info = $this->session->userdata('admin_info');
		$this->username = $admin_info['username'];
		$this->name = $admin_info['name'];
		$this->role_id = $admin_info['role_id'];
		$this->admin_id = $admin_info['id'];
		if (!in_array($this->action, $this->no_login)) {
			$this->login_status();
			$this->role_denied($this->action);
		}
	}

	private function login_status() {
		if (!$this->username) {
			redirect(base_url() . 'admin/login');
		}
	}

	private function role_denied($action) {
		$this->load->model('role');
		if (in_array($action, $this->admin_look)) {
			$this->role->role_denied($this->role_id, $this->admin_id, $this->menu_id . '_' . ADMIN_LOOK);
		}
		if (in_array($action, $this->admin_add)) {
			$this->role->role_denied($this->role_id, $this->admin_id, $this->menu_id . '_' . ADMIN_ADD);
		}
		if (in_array($action, $this->admin_edit)) {
			$this->role->role_denied($this->role_id, $this->admin_id, $this->menu_id . '_' . ADMIN_EDIT);
		}
		if (in_array($action, $this->admin_del)) {
			$this->role->role_denied($this->role_id, $this->admin_id, $this->menu_id . '_' . ADMIN_DEL);
		}
	}

	protected function page($count, $per_page, $url) {
		$this->load->library('pagination');
		$config['base_url'] = $url;
		$config['total_rows'] = $count;
		$config['per_page'] = $per_page;
		$config['page_query_string'] = TRUE;
		$config['query_string_segment'] = 'cur_page';
		$config['first_link'] = '首页';
		$config['last_link'] = '最后一页';
		$config['display_pages'] = true;
		$config['prev_link'] = '上一页';
		$config['next_link'] = '下一页';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a href ="javascript:void(0);" >';
		$config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['full_tag_open'] = '<ul class="pagination pull-right">';
        $config['full_tag_close'] = '</ul>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
		$this->pagination->initialize($config);
		return $this->pagination->create_links();
	}
	/*
	 * 如果只要简单分页数据，调用 分页 方法，如果还要当前页等信息，调用此方法
	 * */
	protected function page_info($count, $per_page = 10, $url = '') {
		$cur_page = $this->input->get('cur_page');
		$cur_page = is_numeric($cur_page) && $cur_page >= 0 ? $cur_page : 0;

		if (empty($url)) {
			$GET = $this->input->get();
			unset($GET["cur_page"]);
			$this->load->helper('url');
			$url = '/' . uri_string() . '?' . http_build_query($GET);
		}

		if ($cur_page >= $count && $count != 0) {
			$floor = $count % $per_page != 0 ? $count % $per_page : $per_page;
			$cur_page = $count - $floor;
		}
		$array = array();
		$array['per_page'] = $per_page;
		$array['cur_page'] = $cur_page;
		$array['count'] = ceil($count / $per_page);
		$array['url'] = $url;
		$array['page'] = $this->page($count, $per_page, $url);
		return $array;
	}
	protected function upload($file, $filename, $type = 'gif|png|jpg|jpeg|bmp', $path = 'news', $size = '2048') {

		$config['upload_path'] = UPLOADS . $path . '/';
		$config['allowed_types'] = $type;
		$config['max_size'] = $size;
		$config['file_name'] = $filename;
		if (!is_dir($path)) {
			mkdirs($path);
		}
		$this->load->library('upload', $config);
		if (!$this->upload->do_upload($file)) {
			return false;
		} else {
			$data = $this->upload->data();
			return array(
				'file_path' => $config['upload_path'] . $data['file_name'],
				'file_name' => $data['file_name'],
			);
		}
	}

	protected function showAction() {
		$this->load->model('role');
		$admin_role_node = $this->role->admin_role_node($this->admin_id);
		$arr = array(
			'look' => 1,
			'add' => 1,
			'edit' => 1,
			'del' => 1,
		);
		if (!in_array($this->role_id, $this->role->admin_arr)) {
			if (!in_array($this->menu_id . '_' . ADMIN_LOOK, $admin_role_node)) {
				$arr['look'] = 0;
			}
			if (!in_array($this->menu_id . '_' . ADMIN_ADD, $admin_role_node)) {
				$arr['add'] = 0;
			}
			if (!in_array($this->menu_id . '_' . ADMIN_EDIT, $admin_role_node)) {
				$arr['edit'] = 0;
			}
			if (!in_array($this->menu_id . '_' . ADMIN_DEL, $admin_role_node)) {
				$arr['del'] = 0;
			}
		}
		return $arr;
	}

}

