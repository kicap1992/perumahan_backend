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
    $this->response(['message' => 'Halo Bosku', 'res' => true], 200);
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
    $username = $this->get('username');
    $password = $this->get('password');

    if($username == '' || $password == '' || $username == null || $password == null) return $this->response(['res' => false,'message' => 'Username / Password Tidak Boleh Kosong' , 'data' => null], 400);    

    $cek_data = $this->model->tampil_data_where('tb_login',['username' => $username, 'password' => $password])->result();

    if (count($cek_data) == 0 )return $this->response(['res' => false,'message' => 'Username / Password Salah' , 'data' => null], 400);

    $level = $cek_data[0]->level;
    $level = $level ==  1 ? 'Admin' : ($level == 2 ? 'Mandor' : 'Pemilik Rumah');

    $this->response(['res' => true,'message' => 'Login Berhasil' , 'data' => ['value' => $cek_data[0],'level' => $level]], 200);


    // $this->response(['res' => 'ok', 'cek_data' => $cek_data], 200);
    
    // redirect(base_url());

  }

  public function cek_rumah_get(){
    $id = $this->get('id');
    if($id == null) return $this->response(['res' => false, 'message' => 'Id Tidak Boleh Kosong'], 400);

    $cek_data = $this->model->tampil_data_where('tb_rumah',['id' => $id])->result();

    if (count($cek_data) == 0 ) return $this->response(['res' => false, 'message' => 'Data Tidak Ditemukan'], 200);

    $this->response(['res' => true, 'message' => 'Data Ditemukan'], 200);
    
  }
 


  public function rumah_get(){
    $id = $this->get('id');
    if($id == null) return $this->response(['res' => false, 'message' => 'Data Tidak Ditemukan'], 400);
    $cek_data = $this->model->tampil_data_where('tb_rumah',['id' => $id])->result();
    if (count($cek_data) == 0 ) return $this->response(['res' => false, 'message' => 'Data Tidak Ditemukan'], 400);
    $cek_progress_rumah = $this->model->custom_query("SELECT * FROM tb_progress_rumah a join tb_mandor b on b.id_mandor = a.id_mandor WHERE a.id_rumah = '$id'")->result();

    $array_progress = [];
    // reverse array
    $i = count($cek_progress_rumah);
    foreach(array_reverse($cek_progress_rumah) as $key => $value){
     
      $array_progress[] = $value;
      $array_progress[$key]->no = $i;
      $i--;
    }

    $this->response(['res' => true, 'message' => 'Data Ditemukan', 'data'=> [ 'rumah' => $cek_data[0], 'progress' => $array_progress ]], 200);
  }

  public function rumahnya_post(){
    $id_rumah = $this->post('id_rumah');
    $nama = $this->post('nama');
    $no_hp = $this->post('no_hp');
    $tanggal_pembelian = $this->post('tanggal_pembelian');
    $harga = $this->post('harga');
    $cicilan = $this->post('cicilan');

    if ($id_rumah == null || $nama == null || $no_hp == null) return $this->response(['res' => false, 'message' => 'Data Tidak Lengkap'], 400);

    $cek_data = $this->model->tampil_data_where('tb_rumah',['id' => $id_rumah])->result();
    

    if(count($cek_data) > 1) return $this->response(['res' => false, 'message' => 'Data Tidak Ditemukan'], 400);

    $data = [
      'id' => $id_rumah,
      'pemilik' => $nama,
      'no_telpon' => $no_hp,
      'tanggal_pembelian' => $tanggal_pembelian,
      'harga' => $harga,
      'cicilan' => $cicilan,
    ];

    $this->model->insert('tb_rumah',$data);
    $this->model->insert('tb_login',['username' => $no_hp, 'password' => 12345678, 'level' => '3', 'id_rumah' => $id_rumah]);

    $this->response(['res' => true, 'message' => 'Data Rumah'.strtoupper($id_rumah).' Berhasil Ditambahkan'], 200);

    // $this->response(['res' => false, 'message' => $no_hp], 200);

  }

  public function pemilik_get() {
    $cek_data = $this->model->tampil_data_keseluruhan('tb_rumah')->result();
    $this->response(['res' => true, 'message' => 'Data Ditemukan', 'data' => $cek_data], 200);
  }

  public function pembangunan_get() {
    $stat = $this->get('stat');
    if($stat == null || $stat == '') return $this->response(['res' => false, 'message' => 'Data Tidak Ditemukan'], 400);

    if ($stat == 'all') {
      $cek_data = $this->model->tampil_data_keseluruhan('tb_progress_rumah')->result();
      
    }

    if($stat == 'mandor') {
      $id = $this->get('id_mandor');
      if($id == null || $id == '') return $this->response(['res' => false, 'message' => 'Data Tidak Ditemukan'], 400);
      $cek_data = $this->model->tampil_data_where('tb_progress_rumah',['id_mandor' => $id])->result();
    }

    $this->response(['res' => true, 'message' => 'Data Ditemukan', 'data' => $cek_data], 200);

  }

  public function pembangunan_post() {
    $img = $_FILES['image'];
    $id_rumah = $this->post('id_rumah');
    $id_mandor = $this->post('id_mandor');
    $ket = $this->post('ket');
    $type = $this->post('type');

    if($img == null || $id_rumah == null || $id_mandor == null || $ket == null || $img == '' || $id_rumah == '' || $id_mandor == '' || $ket == '') return $this->response(['res' => false, 'message' => 'Data Tidak Lengkap'], 400);

    $dir = 'assets/progress/'. $id_rumah .'/';
    if (!file_exists($dir)) {
      mkdir($dir, 0777, true);
    }

    $path = $dir . $img['name'];
    move_uploaded_file($img['tmp_name'], $path);
    
    $this->model->insert('tb_progress_rumah',['id_rumah' => $id_rumah, 'id_mandor' => $id_mandor, 'ket' => $ket, 'img' => $path, 'type' => $type]);


    $this->response(['res' => true, 'message' => 'Progress Berhasil Diupload', 'data' => ''], 200);
  }


  public function progress_get() {
    $id_progress = $this->get('id_progress');
    $id_rumah = $this->get('id_rumah');
    if($id_progress == null || $id_progress == '' || $id_rumah == null || $id_rumah == '') return $this->response(['res' => false, 'message' => 'Data Tidak Ditemukan'], 400);

    $cek_data = $this->model->custom_query("SELECT * FROM tb_progress_rumah a join tb_mandor b on b.id_mandor = a.id_mandor WHERE a.id_rumah = '$id_rumah' AND a.id_progress = '$id_progress'")->result();

    if(count($cek_data) == 0) return $this->response(['res' => false, 'message' => 'Data Tidak Ditemukan'], 400);

    $this->response(['res' => true, 'message' => 'Data Ditemukan', 'data' => $cek_data[0]], 200);
    
  }

  
}

