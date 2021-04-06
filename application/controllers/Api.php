<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, PUT');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Api extends RestController
{
  function __construct()
  {
      parent::__construct();
      // $this->load->model('model');;
      // $this->db->query("SET sql_mode = '' ");
  }

  public function index_get()
  {
    $this->response(['message' => 'Halo Bosku'], 200);
    // redirect(base_url());

  }
  // -----------------------------------------------------------------------------------------------------------
  public function login_post()
  {
    
    // if ($this->post("proses") == "login") {
      // $data = $this->model->serialize($this->post('data'));
     
      // $username = $this->post("username");
      // $password = $this->post("password");
      // // // print_r($data);
      // // $result = $this->model->tampil_data_where('tb_login',$data)->result();
      // $result = $this->model->tampil_data_where('tb_login',array("username" => $username, "password" => $password))->result();
      // print_r($result[0]->nik);


      // if (count($result) > 0) {
        // $this->session->set_userdata('login', array("level" => "admin" , "nik" => $result[0]->nik));
        $this->session->set_userdata('login', array("level" => "admin" ));
      //   // print_r("data ada");
      //   $this->response(['res' => "ok"], 200);
      // }else{
      //   $this->response(['res'=> "ko"],404);
      // }

      $this->response(['res' => "ok"], 200);


    // }else{
    //   $this->response(['res' => "ko"], 400);
    // }
  }
 

  // public function login_delete()
  // {
  //   $nim = $this->get('nim');
  //   print_r($this->get('nim'));
  //   print_r($this->post('proses'));
  // }

}

