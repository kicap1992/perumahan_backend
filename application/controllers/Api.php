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
      $this->load->model('model');;
      // $this->db->query("SET sql_mode = '' ");
      date_default_timezone_set("Asia/Kuala_Lumpur");
  }

  public function index_get()
  {
    $this->response(['message' => 'Halo Bosku'], 200);
    // redirect(base_url());

  }
  // -----------------------------------------------------------------------------------------------------------
  
  public function admin_get()
  {
    $where = $this->get('where');

    

    $cek_data = $this->model->tampil_data_where('tb_admin',$where)->result();

    if (count($cek_data) > 0 ){
      $this->response(['res' => 'ok','data' => $cek_data], 200);
    }else{
      $this->response(['res' => 'ko'], 200);
    }
    
    
    // redirect(base_url());

  }

  


  public function login_get()
  {
    $where = $this->get('where');

    $level = $where['level'] ?? null;
    if($level != null){
      $cek_data = $this->model->tampil_data_where('tb_login',[ ($level == 'admin') ? 'nik_admin' : 'nik_user' => $where['nik'] , 'level' => $level])->result();
    }
    else{
      $cek_data = $this->model->tampil_data_where('tb_login',$where)->result();
    }

    

    if (count($cek_data) > 0 ){
      $this->response(['res' => 'ok','url' => $cek_data[0]->level, 'level' => $cek_data[0]->level, 'nik' => ($cek_data[0]->level == 'user') ? $cek_data[0]->nik_user : $cek_data[0]->nik_admin ,'data' => $cek_data], 200);
    }else{
      $this->response(['res' => 'ko'], 200);
    }

    // $this->response(['res' => 'ok', 'cek_data' => $cek_data], 200);
    
    // redirect(base_url());

  }

  public function user_post(){
    $data = $this->post('data');
    // $data = $this->model->serialize($data);

    $cek_data = $this->model->tampil_data_where('tb_user',['nik_user' => $data['nik_user']])->result();
    if(count($cek_data) > 0){
      $this->response(['message' => 'ko'], 400);
    }else{
      $data = array_merge($data,array('tanggal_pendaftaran' => date('Y-m-d H:m:s')));
      $this->model->insert('tb_user',$data);
      $this->model->insert('tb_login',['username' => $data['nik_user'] , 'password' => $data['nik_user'],'nik_user' => $data['nik_user'], 'level' => 'user']);
      $this->response(['message' => 'ok','data' => $data], 200);
    }
    
  }

  public function user_put(){
    $detail = $this->put('detail');
    $where = $this->put('where');
    // $data = $this->model->serialize($data);

    $cek_data = $this->model->tampil_data_where('tb_user',$where)->result();
    if(count($cek_data) > 0){
      // $this->response(['message' => 'ko'], 400);
      $simpanan_wajib = $detail['simpanan_wajib'] ?? null;
      $simpanan_sukarela = $detail['simpanan_sukarela'] ?? null;
      if ($simpanan_wajib != null){
        $array_simpanan_wajib = json_decode($cek_data[0]->simpanan_wajib) ?? null;
        if($array_simpanan_wajib == null){
          $detail = ['simpanan_wajib' => json_encode($detail['simpanan_wajib'])];
        }else{
          $array_simpanan_wajib = array_merge($array_simpanan_wajib,$detail['simpanan_wajib']);
          $detail = ['simpanan_wajib' => json_encode($array_simpanan_wajib)];
        }
      }

      if ($simpanan_sukarela != null){
        $array_simpanan_sukarela = json_decode($cek_data[0]->simpanan_sukarela) ?? null;
        if($array_simpanan_sukarela == null){
          $detail = ['simpanan_sukarela' => json_encode($detail['simpanan_sukarela'])];
        }else{
          $array_simpanan_sukarela = array_merge($array_simpanan_sukarela,$detail['simpanan_sukarela']);
          $detail = ['simpanan_sukarela' => json_encode($array_simpanan_sukarela)];
        }
      }
      
      $this->model->update('tb_user',$where,$detail);
      $this->response(['message' => 'ok'], 200);
    }else{
      $this->response(['message' => 'ko'], 400);
    }

    
    
  }

  public function user_get()
  {
    $where = $this->get('where');

    

    $cek_data = $this->model->tampil_data_where('tb_user',$where)->result();

    $this->response(['res' => 'ok','data' => $cek_data], 200);
  }

}

