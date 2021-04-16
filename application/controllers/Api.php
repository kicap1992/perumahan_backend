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
      $this->response(['res' => 'ko'], 400);
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
      $pinjaman = $detail['pinjaman'] ?? null;
      $pengembalian = $detail['pengembalian'] ?? null;

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

      if ($pinjaman != null){
        $array_pinjaman = json_decode($cek_data[0]->pinjaman) ?? null;
        if($array_pinjaman == null){
          $detail = ['pinjaman' => json_encode($detail['pinjaman'])];
        }else{
          $array_pinjaman = array_merge($array_pinjaman,$detail['pinjaman']);
          $detail = ['pinjaman' => json_encode($array_pinjaman)];
        }
      }

      if ($pengembalian != null){
        // $this->response(['message' => 'oknya'], 200);
        $array_pinjaman = json_decode($cek_data[0]->pinjaman,true) ?? null;
        $array_pengembalian = json_decode($cek_data[0]->pengembalian,true) ?? null;

        if($array_pinjaman == null){
          // $detail = ['pinjaman' => json_encode($detail['pinjaman'])];
          $this->response(['message' => 'tiada pinjaman','data' => $cek_data[0]->nama.' Belum Pernah Melakukan Pinjaman Sebelumnya'], 200);
        }else{
          // $array_pinjaman = array_merge($array_pinjaman,$detail['pinjaman']);
          // $detail = ['pinjaman' => json_encode($array_pinjaman)];
          $pinjaman_sebelumnya = 0;
          foreach ($array_pinjaman as $key => $value) {
            $pinjaman_sebelumnya += $value['pinjaman'];
          }
          if ($detail['pengembalian'][0]['pengembalian'] > $pinjaman_sebelumnya) {
            $this->response(['message' => 'terlebih pengembalian1','data' => 'Pengembalian '.$cek_data[0]->nama.' yang dimasukkan lebih besar dari pinjaman sebelumnya ,
            Sila Cek List Pinjaman / Pengembalian Untuk Konfirmasi Jumlah Pinjaman '], 200);
          }else{
            if($array_pengembalian == null){
              $detail = ['pengembalian' => json_encode($detail['pengembalian'])];
            }else{
              
              $pengembalian_sebelumnya = 0;

              foreach ($array_pengembalian as $key => $value) {
                $pengembalian_sebelumnya += $value['pengembalian'];
              }

              $pengembalian_sepenuhnya =  $pengembalian_sebelumnya + $detail['pengembalian'][0]['pengembalian'];
              
              if ($pengembalian_sepenuhnya > $pinjaman_sebelumnya) {
                $sisa = $pinjaman_sebelumnya - $pengembalian_sebelumnya;
                if($sisa != 0){
                  $this->response(['message' => 'terlebih pengembalian2','data' => 'Pengembalian '.$cek_data[0]->nama.' yang dimasukkan lebih besar dari pinjaman sebelumnya, 
                  Sisa Pengembalian Adalah Rp. '.number_format($sisa), 'pengembalian' => number_format($sisa)], 200);
                }else{
                  $this->response(['message' => 'terlebih pengembalian3','data' => 'Semua Pinjaman Yang Dilakukan Oleh '.$cek_data[0]->nama. ' Telah Dikembalikan Sebelumnya'], 200);
                }
                
              }else{
                $array_pengembalian = array_merge($array_pengembalian,$detail['pengembalian']);
                $detail = ['pengembalian' => json_encode($array_pengembalian)];
              }
            }
          }
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


  public function barang_post(){
    $detail = $this->post('detail');
    $cek_data = $this->model->tampil_data_where('tb_barang',['nama_barang' => $detail['nama_barang']])->result();
    if(count($cek_data) > 0){
      $this->response(['res' => 'ko',], 400);
    }else{
      $this->model->insert('tb_barang',$detail);
      $this->response(['res' => 'ok','data' => $detail['nama_barang']], 200);
    }
    
  }

  public function barang_get()
  {
    $where = $this->get('where');

    

    $cek_data = $this->model->tampil_data_where('tb_barang',$where)->result();

    $this->response(['res' => 'ok','data' => $cek_data], 200);
  }

  public function barang_put()
  {
    $where = $this->put('where');
    $detail = $this->put('detail');
    $cek_data = $this->model->tampil_data_where('tb_barang',$where)->result();

    $tambah_stok = $detail['log_penambahan_stok'] ?? null;
    $pinjam_stok = $detail['log_pinjaman'] ?? null;
    $kembalian_stok = $detail['log_pengembalian'] ?? null;

    if ($tambah_stok != null) {
      $log_penambahan_stok = json_decode($cek_data[0]->log_penambahan_stok,true) ?? null;

      if ($log_penambahan_stok != null){
        $arraynya = array_merge($log_penambahan_stok,json_decode($detail['log_penambahan_stok']));
        unset($detail['log_penambahan_stok']);
        $detail = array_merge($detail,array('log_penambahan_stok' => json_encode($arraynya)));
      }

      
      
    }

    if ($pinjam_stok != null) {
      $cek_user = $this->model->tampil_data_where('tb_user',['nik_user' => json_decode($detail['log_pinjaman'],true)[0]['nik_user'] ])->result()[0];
      $pinjaman_user = json_decode($cek_user->pinjaman_barang,true) ?? null;
      $temp_detail = json_decode($detail['log_pinjaman'],true)[0];
      unset($temp_detail['nik_user']);
      $array_pinjaman_user = array(array_merge($where,$temp_detail));
      if ($pinjaman_user == null){
        $this->model->update('tb_user',['nik_user' => json_decode($detail['log_pinjaman'],true)[0]['nik_user'] ],['pinjaman_barang' => json_encode($array_pinjaman_user)]);
      }else{
        $array_pinjaman_user = array_merge($pinjaman_user,$array_pinjaman_user);

        $this->model->update('tb_user',['nik_user' => json_decode($detail['log_pinjaman'],true)[0]['nik_user'] ],['pinjaman_barang' => json_encode($array_pinjaman_user)]);
      }


      $log_pinjaman = json_decode($cek_data[0]->log_pinjaman,true) ?? null;

      if ($log_pinjaman != null){
        $arraynya = array_merge($log_pinjaman,json_decode($detail['log_pinjaman']));
        unset($detail['log_pinjaman']);
        $detail = array_merge($detail,array('log_pinjaman' => json_encode($arraynya)));
      }
    }


    if ($kembalian_stok != null) {
      $cek_user = $this->model->tampil_data_where('tb_user',['nik_user' => json_decode($detail['log_pengembalian'],true)[0]['nik_user'] ])->result()[0];
      $kembalian_user = json_decode($cek_user->pengembalian_barang,true) ?? null;
      $temp_detail = json_decode($detail['log_pengembalian'],true)[0];
      unset($temp_detail['nik_user']);
      $array_kembalian_user = array(array_merge($where,$temp_detail));
      if ($kembalian_user == null){
        $this->model->update('tb_user',['nik_user' => json_decode($detail['log_pengembalian'],true)[0]['nik_user'] ],['pengembalian_barang' => json_encode($array_kembalian_user)]);
      }else{
        $array_kembalian_user = array_merge($kembalian_user,$array_kembalian_user);

        $this->model->update('tb_user',['nik_user' => json_decode($detail['log_pengembalian'],true)[0]['nik_user'] ],['pengembalian_barang' => json_encode($array_kembalian_user)]);
      }


      $log_pengembalian = json_decode($cek_data[0]->log_pengembalian,true) ?? null;

      if ($log_pengembalian != null){
        $arraynya = array_merge($log_pengembalian,json_decode($detail['log_pengembalian']));
        unset($detail['log_pengembalian']);
        $detail = array_merge($detail,array('log_pengembalian' => json_encode($arraynya)));
      }
    }


    

    $this->model->update('tb_barang',$where,$detail);

    $this->response(['res' => 'ok','data' => $temp_detail ?? 'tiada'], 200);
  }

}

