<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

  public function __construct()
  {
    parent::__construct();
    $this->load->model('model');
  }
  
  function index()
  {
    // $this->load->view('home/login');
    // print_r('sini login');
    if ($this->input->post('proses') == 'login') {
      // print_r('sini login asdasd');
      $username = $this->input->post('username');
      $password = $this->input->post('password');

      $cek_data =$this->model->tampil_data_where('tb_login',['username' =>$username , 'password' =>$password ])->result();
      // print_r($cek_data[0]->level);

      if (count($cek_data) > 0) {
        switch ($cek_data[0]->level) {
          case 'admin':
            $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode(array("res" => "ok" , 'level' => $cek_data[0]->level, 'nik' => $cek_data[0]->nik_admin)));
            break;
          
          case 'user':
            $this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode(array("res" => "ok" , 'level' => $cek_data[0]->level, 'nik' => $cek_data[0]->nik_user)));
            break;
        }
        
      }
      else
      {
        $this->output->set_status_header(400)->set_content_type('application/json')->set_output(json_encode(array("res" => "ko" )));
      }

    }

    else if ($this->input->post('proses') == 'cek_data') {
      // print_r('cek data');
      $nik = $this->input->post('nik');
      $level = $this->input->post('level');
      // print_r($level);
      $cek_data =$this->model->tampil_data_where('tb_login',['nik_'.$level =>$nik ])->result();

      // print_r(count($cek_data));
      if (count($cek_data) > 0) {
        print_r(json_encode(['res' => 'ok' , 'url' => $level.'/']));
      }
      else
      {
        print_r(json_encode(['res' => 'ko' ]));
      }
    }

    else if ($this->input->post('proses') == 'cek_data_detail') {
      // print_r('cek data');
      $nik = $this->input->post('nik');
      $level = $this->input->post('level');
      // print_r($level);
      $cek_data =$this->model->tampil_data_where('tb_'.$level,['nik_'.$level =>$nik ])->result();

      // print_r(count($cek_data));
      if (count($cek_data) > 0) {
        print_r(json_encode(['res' => 'ok' , 'nama' => $cek_data[0]->nama]));
      }
      else
      {
        print_r(json_encode(['res' => 'ko' ]));
      }
    }
  }


  
  



  
}
?>