<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Lin {

	public function index() {
		$data['username'] = $this->username;
		$this->load->view('Home/index.html', $data);
	}
}
