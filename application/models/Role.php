<?php

class Role extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	public function role_name($role_id) {

		$role_info = $this->db->select("role_name")->where('id', $role_id)->get("ci_role")->row_array();
		return $role_info['role_name'] ? $role_info['role_name'] : false;

	}

	public function admin_role_node($admin_id) {

		$admin_info= $this->db->select("role_id")->where(array('status' => 1, 'id' => $admin_id))->get("ci_admin")->row_array();
		$role_info = $this->db->select("role_node")->where(array('status' => 1, 'id' => $admin_info['role_id']))->get("ci_role")->row_array();
		$role_node = explode(',', $role_info['role_node']);
		return $role_node;
	}

	public function template() {
		$menu_list= $this->config->item("menu");
		$template = '<table class="table" width="100%" border="0" cellspacing="0" cellpadding="0"><thead><tr><td><label id="authorityAll" width="100"><span class="checkbox_ui"></span>全部权限</label></td></tr></thead><tbody>';
		foreach ($menu_list as $key => $menu) {
			$template .= '<tr><td class="categroy"><label class="authority_1st"><span class="checkbox_ui" data-val="' . $key . '"></span>' . $menu['0'] . '</label></td>';
			$template .= '<td>';
			if ($menu['1'] == 0) {
				$template .= '<label class="authority_2nd" ><span class="checkbox_ui" data-val="' . $key . '_1' . '"></span>查看</label>';
			}
			if ($menu['2'] == 0) {
				$template .= '<label class="authority_2nd" ><span class="checkbox_ui" data-val="' . $key . '_2' . '"></span>新增</label>';
			}
			if ($menu['3'] == 0) {
				$template .= '<label class="authority_2nd" ><span class="checkbox_ui" data-val="' . $key . '_3' . '"></span>编辑</label>';
			}
			if ($menu['4'] == 0) {
				$template .= '<label class="authority_2nd" ><span class="checkbox_ui" data-val="' . $key . '_4' . '"></span>删除</label>';
			}
			$template .= '</td></tr>';
		}
		$template .= '</tbody></table>';
		return $template;
	}

	public function role_denied($role_id, $admin_id, $role_node) {
		$admin_role_node = $this->admin_role_node($admin_id);
		if(!in_array($role_id, array(0,1))){
			if (!in_array($role_node, $admin_role_node)) {
				redirect(base_url() . 'denied/permission');
				//return false;
			}
		}
		return true;
	}

}
