<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Denied extends CI_Controller {

	public function permission() {

		$this->load->view('Denied/permission.html');
		
	}

	public function disable() {

		$this->load->view('Denied/disable.html');
		
	}
	
}
