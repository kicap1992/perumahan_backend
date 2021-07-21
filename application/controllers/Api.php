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
    $total_simpanan_wajib = $data['simpanan_wajib'];
    unset($data['simpanan_wajib']);
    // $data = array_merge($data);
    // $data = $this->model->serialize($data);

    $cek_data = $this->model->tampil_data_where('tb_user',['nik_user' => $data['nik_user']])->result();
    if(count($cek_data) > 0){
      $this->response(['message' => 'ko'], 400);
    }else{
      $simpanan_wajib = array(
        array(
          'tahun' => date('Y'),
          'bulan' => date('m'),
          'tanggal_simpan' =>  date('Y-m-d H:i:s'),
          'simpanan' => $total_simpanan_wajib
        )
      );
      $data = array_merge($data,array('tanggal_pendaftaran' => date('Y-m-d H:i:s'),'simpanan_wajib' =>json_encode($simpanan_wajib),'total_simpanan_wajib' => $total_simpanan_wajib));
      $this->model->insert('tb_user',$data);
      $this->model->insert('tb_login',['username' => $data['nik_user'], 'password' => $data['nik_user'],'nik_user' => $data['nik_user'], 'level' => 'user']);

     
      $array_laporan = array(
        array(
          'tanggal' => date('Y-m-d H:i:s'),
          'ket' => 'Penambahan User',
          'ket_all' => array(
            'nik_user' => $data['nik_user'],
            'nama' => $data['nama'],
            'alamat' => $data['alamat'],
            'simpanan_pokok' => $data['simpanan_pokok'],
            'simpanan_wajib' => $data['simpanan_wajib'],
          )
        )
      );

      $cek_laporan = $this->model->tampil_data_where('tb_laporan',['tahun' => date('Y'), 'bulan' => date('m')])->result();

      if(count($cek_laporan) > 0){
        $array_ket_laporan = json_decode($cek_laporan[0]->laporan);
        $array_laporan =array_merge($array_ket_laporan,$array_laporan);
        $this->model->update('tb_laporan',['tahun' => date('Y') , 'bulan' => date('m')],['laporan' => json_encode($array_laporan)]);
      }else{
        $this->model->insert('tb_laporan',['tahun' => date('Y') , 'bulan' => date('m'),'laporan' => json_encode($array_laporan)]);
      }


      $this->response(['message' => 'ok','data' => $data], 200);
    }

    // $this->response(['message' => 'sini untuk tambah guru','data' => $data], 200);
    
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
      
      // $this->model->update('tb_user',$where,$detail);
      $this->response(['message' => 'ok',$detail], 200);
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

  public function simpanan_sukarela_put(){
    $simpanan_sukarela = $this->put('simpanan_sukarela');
    $nik_user = $this->put('nik_user');

    $cek_data = $this->model->tampil_data_where('tb_user',['nik_user' => $nik_user])->result();

    $ket_simpanan_sukarela = ($cek_data[0]->simpanan_sukarela == null) ? null : json_decode($cek_data[0]->simpanan_sukarela);

    $array_simpanan_sukarela = array(
      array(
        'simpanan' => $simpanan_sukarela,
        'tanggal' => date('Y-m-d H:i:s')
      )
    );

    if ($ket_simpanan_sukarela == null) {
      $this->model->update('tb_user',['nik_user' => $nik_user], ['simpanan_sukarela' => json_encode($array_simpanan_sukarela)]);
    } else {
      $array_simpanan_sukarela = array_merge($ket_simpanan_sukarela,$array_simpanan_sukarela);
      $this->model->update('tb_user',['nik_user' => $nik_user], ['simpanan_sukarela' => json_encode($array_simpanan_sukarela)]);
    }
    

    $array_laporan = array(
      array(
        'tanggal' => date('Y-m-d H:i:s'),
        'ket' => 'Update Simpanan Sukarela',
        'ket_all' => array(
          'nik_user' => $nik_user,
          'data' => array(
            'simpanan' => $simpanan_sukarela,
            'tanggal' => date('Y-m-d H:i:s')
          ),
        )
      )
    );

    $cek_laporan = $this->model->tampil_data_where('tb_laporan',['tahun' => date('Y'), 'bulan' => date('m')])->result();

    if(count($cek_laporan) > 0){
      $array_ket_laporan = json_decode($cek_laporan[0]->laporan);
      $array_laporan =array_merge($array_ket_laporan,$array_laporan);
      $this->model->update('tb_laporan',['tahun' => date('Y') , 'bulan' => date('m')],['laporan' => json_encode($array_laporan)]);
    }else{
      $this->model->insert('tb_laporan',['tahun' => date('Y') , 'bulan' => date('m'),'laporan' => json_encode($array_laporan)]);
    }

    $this->response(['message' => 'sini simpanan sukarela','data' => $array_simpanan_sukarela], 200);
  }
  

  public function simpanan_wajib_put(){
    $data = $this->put('data');
    $nik_user = $this->put('nik_user');
    $total_simpanan_wajib = $this->put('simpanan_wajib');

    $cek_data = $this->model->tampil_data_where('tb_user',['nik_user' => $nik_user])->result();

    $array_simpanan_wajib = json_decode($cek_data[0]->simpanan_wajib);

    foreach ($data as $key => $value) {
      $datanya = explode(',', $value);
      $tahun = $datanya[0];
      $bulan = (strlen($datanya[1]) == 1) ? '0'.$datanya[1] : $datanya[1];
      $arraynya = array(
        array(
          'tahun' => $tahun,
          'bulan' => $bulan,
          'tanggal_simpan' =>  date('Y-m-d H:i:s'),
          'simpanan' => $total_simpanan_wajib
        )
      );

      $array_simpanan_wajib = array_merge($array_simpanan_wajib,$arraynya);
    }

    $this->model->update('tb_user',['nik_user' => $nik_user],['simpanan_wajib' => json_encode($array_simpanan_wajib)]);

    // $data = explode(',', $data);
    $array_laporan = array(
      array(
        'tanggal' => date('Y-m-d H:i:s'),
        'ket' => 'Update Simpanan Wajib',
        'ket_all' => array(
          'nik_user' => $nik_user,
          'data' => $data,
        )
      )
    );

    $cek_laporan = $this->model->tampil_data_where('tb_laporan',['tahun' => date('Y'), 'bulan' => date('m')])->result();

    if(count($cek_laporan) > 0){
      $array_ket_laporan = json_decode($cek_laporan[0]->laporan);
      $array_laporan =array_merge($array_ket_laporan,$array_laporan);
      $this->model->update('tb_laporan',['tahun' => date('Y') , 'bulan' => date('m')],['laporan' => json_encode($array_laporan)]);
    }else{
      $this->model->insert('tb_laporan',['tahun' => date('Y') , 'bulan' => date('m'),'laporan' => json_encode($array_laporan)]);
    }

    $this->response(['message' => 'sini untuk updatenya' , 'data' => $nik_user], 200);
  }

  public function detail_koperasi_get(){
    $all_user = $this->model->tampil_data_keseluruhan('tb_user')->result();
    $simpanan_pokok = 0; 
    $simpanan_wajib = 0; 
    $simpanan_sukarela = 0; 

    foreach ($all_user as $key => $value) {
      $simpanan_pokok += $value->simpanan_pokok;
      $array_simpanan_wajib = json_decode($value->simpanan_wajib,true);

      foreach ($array_simpanan_wajib as $key1 => $value1) {
        $simpanan_wajib += $value1['simpanan'];
      }

      $array_simpanan_sukarela = ($value->simpanan_sukarela != null) ? json_decode($value->simpanan_sukarela,true) : null;

      if ($array_simpanan_sukarela != null) {
        foreach ($array_simpanan_sukarela as $key1 => $value1) {
          $simpanan_sukarela += $value1['simpanan'];
        }
      }
    }

    $all_simpanan = $simpanan_pokok + $simpanan_wajib + $simpanan_sukarela;

    $this->response(['total_simpanan' => $all_simpanan , 'all_user' => count($all_user)], 200);
  }

  public function pinjaman_put(){
    $nik_user = $this->put('nik_user');
    $pinjaman = $this->put('pinjaman');

    $cek_data = $this->model->tampil_data_where('tb_user',['nik_user' => $nik_user])->result();

    $array_pinjaman = ($cek_data[0]->pinjaman != null ) ? json_decode($cek_data[0]->pinjaman)  : null ;
    $pinjaman_array = array(
      array(
        'tanggal_pinjam' =>  date('Y-m-d H:i:s'),
        'pinjaman' => $pinjaman
      )
    );

    if ($array_pinjaman != null) {
      $pinjaman_array = array_merge($array_pinjaman,$pinjaman_array);
      
    }

    $this->model->update('tb_user',['nik_user' => $nik_user],['pinjaman' => json_encode($pinjaman_array)]);


    $array_laporan = array(
      array(
        'tanggal' => date('Y-m-d H:i:s'),
        'ket' => 'Pinjaman User',
        'ket_all' => array(
          'nik_user' => $nik_user,
          'data' => array(
            'pinjaman' => $pinjaman,
            'tanggal' => date('Y-m-d H:i:s')
          ),
        )
      )
    );

    $cek_laporan = $this->model->tampil_data_where('tb_laporan',['tahun' => date('Y'), 'bulan' => date('m')])->result();

    if(count($cek_laporan) > 0){
      $array_ket_laporan = json_decode($cek_laporan[0]->laporan);
      $array_laporan =array_merge($array_ket_laporan,$array_laporan);
      $this->model->update('tb_laporan',['tahun' => date('Y') , 'bulan' => date('m')],['laporan' => json_encode($array_laporan)]);
    }else{
      $this->model->insert('tb_laporan',['tahun' => date('Y') , 'bulan' => date('m'),'laporan' => json_encode($array_laporan)]);
    }
    
    $this->response(['message' => 'sini untuk updatenya' ], 200);
  }

  public function cek_laporan_get()
  {
    $where = $this->get('where');

    

    $cek_data = $this->model->tampil_data_where('tb_laporan',$where)->result();


    $this->response(['res' => 'ok','data' => count($cek_data)], 200);
  }
}

