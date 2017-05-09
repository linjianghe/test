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
		$config['query_string_segment'] = '位置';
		$config['first_link'] = '首页';
		$config['last_link'] = '最后一页';
		$config['display_pages'] = true;
		$config['prev_link'] = '上一页';
		$config['next_link'] = '下一页';
		$config['cur_tag_open'] = '<a class="current" href ="javascript:void(0);" >';
		$config['cur_tag_close'] = '</a>';
		$this->pagination->initialize($config);
		return $this->pagination->create_links();
	}

	protected function upload($file, $filename, $type = 'gif|png|jpg|jpeg|bmp', $path = 'news', $size = '2048') {

		$config['upload_path'] = UPLOADS . $path . '/';
		$config['allowed_types'] = $type;
		$config['max_size'] = $size;
		$config['file_name'] = $filename;
		if (!is_dir($path)) {
			$mkdir = mkdir($path, 0777, true);
			if (!$mkdir) {
				die('创建目录失败');
			}
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

}

