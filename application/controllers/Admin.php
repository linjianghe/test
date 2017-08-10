<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends Lin {

	public $no_login = array('login', 'login_check', 'logout');
	public $menu_id = 101;//菜单表对应主键
	public $admin_look = array('index', 'role');
	public $admin_add = array('add', 'role_add');
	public $admin_edit = array('update', 'reset', 'role_update');
	public $admin_del = array('del', 'role_del');

	public function __construct() {
		parent::__construct();
	}

	public function index() {
		
		$data['denied_arr']=$this->showAction();
		$condition = ' status!=0 ';
		$search = $this->input->get('search');
		if ($search) {
			$condition .= ' and username like "%' . $search . '%" or name like "%' . $search . '%"';
		}
		$data['search'] = $search;

		$count = $this->db->where($condition)->from("ci_admin")->count_all_results();
		$cur_page = $this->input->get('cur_page');
		$cur_page = is_numeric($cur_page) && $cur_page >= 0 ? $cur_page : 0;

		$per_page = 1;
		if ($cur_page >= $count && $count != 0) {
			$remainder = $count % $per_page != 0 ? $count % $per_page : $per_page;
			$cur_page = $count - $remainder;
		}

		$data['per_page'] = $per_page;
		$data['cur_page'] = $cur_page;
		$data['page'] = $this->page($count, $per_page, '/admin/index?search=' . $search);
		$sql = " SELECT id,username,name,add_time,status,edit_time,role_id FROM ci_admin WHERE {$condition} order by id desc limit {$cur_page},{$per_page} ";
		$data['datalist'] = $this->db->query($sql)->result_array();
		$data['rolelist'] = $this->db->where('status', 1)->get('ci_role')->result_array();

		$this->load->view('Admin/index.html', $data);

	}

	public function login() {
		
		if ($this->username) {
			redirect(base_url() . 'home/index');
		}
		$this->load->view('Admin/login.html');
	}

	public function login_check() {

		$username = $this->input->post('username');
		$password = $this->input->post('password');
		password_preg($password);
		$admin_info = $this->db->select('username,role_id,id,status,password,name')->where(array('username' => $username))->get('ci_admin')->row_array();

		if ($admin_info['status'] == '1' && password_check($admin_info['password'], $password)) {

			$this->session->set_userdata('admin_info', $admin_info);
			//json_return(200, '登录成功', array('url' => base_url() . 'home/index'));
			redirect(base_url() . 'home/index');

		} else if ($admin_info['status'] == '2') {

			json_return(500, '该用户已被禁用');

		} else {

			json_return(500, '帐号或密码错误');

		}
	}

	public function get_info(){

        $id = $this->input->post('id');
        if (!$id) {
            json_return(501, '管理员编号不能为空');
        }
        $data = $this->db->select()->where('id', $id)->get('ci_admin')->row_array();
        if ($data) {
            json_return(200, '成功',$data);
        } else {
            json_return(500, '没有数据');
        }
    }

	public function update() {

		$id = $this->input->post('id');
		//$role_id = $this->input->post('role_id');
		//$status = $this->input->post('status');
		$name = $this->input->post('name');
		if (!$id) {
			json_return(501, '管理员编号不能为空');
		}
		/*
		if (!$role_id) {
			json_return(502, '角色不能为空');
		}*/

		$data = array(
			//'role_id' => $role_id,
			//'status' => $status,
			'name' => $name,
		);
		$result = $this->db->where('id', $id)->update('ci_admin', $data);
		if ($result) {
			json_return(200, '更新成功');
		} else {
			json_return(500, '更新失败');
		}
	}

	public function reset() {

		$id = $this->input->post('id');
		if (!$id) {
			json_return(501, '管理员编号不能为空');
		}
		$admin_info = $this->db->where(array('id' => $id))->get('ci_admin')->row_array();
		if ($admin_info['status'] == 2) {
			json_return(500, '该帐号已被禁用');
		}

		$password = password_md(121212);
		if ($id) {

			$result = $this->db->where('id', $id)->update('ci_admin', array('password' => $password));
			if (!$result) {
				json_return(500, '重置失败');
			}
			json_return(200, '重置成功', array('url' => base_url() . 'admin/index'));
		}
	}

	public function del() {

		$id = $this->input->post('id');
		if (!$id) {
			json_return(501, '管理员编号不能为空');
		}
		$result = $this->db->where('id', $id)->update('ci_admin', array('status' => 2));
		if ($result) {
			json_return(200, '删除成功');
		} else {
			json_return(500, '删除失败');
		}

	}

	public function logout() {
		
		$this->session->unset_userdata('admin_info');
		redirect(base_url() . 'admin/login');
	}

	public function role_update() {

		$id = $this->input->post('id');
		$role_name = $this->input->post('role_name');
		$role_node = $this->input->post('role_node');
		$remark = $this->input->post('remark');

		$data = array(
			'role_name' => $role_name,
			'role_node' => $role_node,
			'remark' => $remark,
		);
		$result = $this->db->where('id', $id)->update('ci_role', $data);
		if ($result) {
			json_return(200, '修改成功', array('url' => base_url() . 'admin/index'));
		} else {
			json_return(500, '修改失败');
		}
	}

	public function info() {
		
		$info = $this->session->userdata('admin_info');
		$this->load->view('Admin/info.html', $info);
	}

	public function password_update() {

		$old_password = $this->input->post('old_password');
		$new_password = $this->input->post('new_password');
		if (!$new_password) {
			json_return(500, '新密码不能为空');
		}
		password_preg($new_password);
		$admin_info = $this->db->select('status,password')->where(array('id' => $this->admin_id))->get('ci_admin')->row_array();
		if ($admin_info['password']) {
			if (!password_check($admin_info['password'], $old_password)) {
				json_return(500, '原始密码错误');
			}
		}
		$new_password = password_md($new_password);
		$result = $this->db->where('id', $this->admin_id)->update('ci_admin', array('password' => $new_password));
		if ($result) {
			json_return(200, '修改成功', array('url' => base_url() . 'admin/index'));
		} else {
			json_return(500, '修改失败');
		}

	}

	public function add() {

		$username = $this->input->post('username');
		$role_id = $this->input->post('role_id');
		$status = $this->input->post('status');
		$password = $this->input->post('password');
		$name = $this->input->post('name');

		password_preg($password);
		$password = password_md($password);

		if ($role_id == '—请选择—') {
			json_return(501, '请选择身份');
		}
		if (!$name) {
			json_return(502, '姓名不能为空');
		}

		if (!$this->db->where("username", $username)->get("ci_admin")->row_array()) {

			$result = $this->db->insert("ci_admin", array(
				'username' => $username,
				'name' => $name,
				'password' => $password,
				'role_id' => $role_id,
				'status' => $status,
			));
			if ($result) {
				json_return(200, '添加成功', array('url' => base_url() . 'admin/index'));
			} else {
				json_return(500, '添加失败');
			}
		} else {
			json_return(500, '账号已存在');
		}
	}

	public function role() {
		
		$data['denied_arr']=$this->showAction();
		$sql = " SELECT id,role_name,remark,add_time,edit_time,role_node from ci_role where status=1 ";
		$data['role_list'] = $this->db->query($sql)->result_array();
		$this->load->model('role');
		$data['template'] = $this->role->template();
		$this->load->view('admin/role.html', $data);

	}

	public function role_add() {

		$role_name = $this->input->post('role_name');
		$role_node = $this->input->post('role_node');
		$remark = $this->input->post('remark');

		$data = array(
			'role_name' => $role_name,
			'role_node' => $role_node,
			'remark' => $remark,
		);
		$result = $this->db->insert('ci_role', $data);
		if ($result) {
			json_return(200, '添加成功', array('url' => base_url() . 'admin/role'));
		} else {
			json_return(500, '添加失败');
		}
	}

	public function role_del() {

		$id = $this->input->post('id');
		$result = $this->db->where('id', $id)->update("ci_role", array('status' => 2));
		if ($result) {
			json_return(200, '删除成功', array('url' => base_url() . 'admin/role'));
		} else {
			json_return(500, '删除失败');
		}

	}

	/*
	 * 获取IP所在城市
	 * 第一种：使用ipip.net 的ＩＰ库
	 * 第二种：使用纯真ＩＰ库
	 */
	private function ipaddr($IP) {
		
		include_once(APPPATH . "/libraries/Ipcity.php");
		$arr = ipcity::find($IP);
		unset($arr[0]);
		return implode("", $arr);

		$this->load->library('iplocation');
		$location = $this->iplocation->getlocation($IP);
		return $location['country'];
	}

}
