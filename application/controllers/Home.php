<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

  public function __construct()
  {
    parent::__construct();
    $this->load->model('model');
    $this->load->model('m_tabel_ss');
  }
  
  function index(){
    if ($this->input->post('proses') == "table_user") {
      $list = $this->m_tabel_ss->get_datatables(array('nik_user','nama','tanggal_daftar','status'),array(null, 'nik_user','nama','tanggal_daftar','simpanan_pokok','status',null),array('status' => 'desc'),"tb_user",null,null,"*");
      $data = array();
      $no = $_POST['start'];
      foreach ($list as $field) {

        $no++;
        $row = array();
        $row[] = $no;
        $row[] = $field->nik_user;
        // $row[] = "<a href='#' onclick='coba_dulu(".'"'.(string)$field->nik_user.'"'.")'>$field->nik_user</a>";
        $row[] = $field->nama;
        $row[] = $field->tanggal_pendaftaran;
        // $row[] = 'Rp. ' .  number_format($field->simpanan_pokok);
        // $row[] = 'Rp. ' .  number_format($field->simpanan_pokok);
        // $row[] = $field->status;       
        $row[] = "<center><button type='button' onclick='user_change(".'"'.(string)$field->nik_user.'"'.")' class='btn btn-primary btn-circle btn-sm waves-effect waves-light'><i class='ico fa fa-edit'></i></button></center>";
        $data[] = $row;
      }

      $output = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->m_tabel_ss->count_all("tb_user",null,null,"*"),
        "recordsFiltered" => $this->m_tabel_ss->count_filtered(array('nik_user','nama','tanggal_daftar','status'),array(null, 'nik_user','nama','tanggal_daftar','simpanan_pokok','status',null),array('status' => 'desc'),"tb_user",null,null,"*"),
        "data" => $data,
      );
      //output dalam format JSON
      echo json_encode($output);
    }

    if ($this->input->post('proses') == "table_laporan") {
      $list = $this->m_tabel_ss->get_datatables(array('tahun','bulan'),array(null, 'tahun','bulan',null),array('no' => 'desc'),"tb_laporan",null,null,"*");
      $data = array();
      $no = $_POST['start'];
      foreach ($list as $field) {

        $no++;
        $row = array();
        $row[] = $no;
        $row[] = $field->tahun;
        $row[] = $this->model->bulan($field->bulan);
        $row[] = "<center><button type='button' onclick='href_laporan(".'"'.$field->bulan.'"'.",".$field->tahun.")' class='btn btn-primary btn-circle btn-sm waves-effect waves-light'><i class='ico fa fa-edit'></i></button></center>";
        $data[] = $row;
      }

      $output = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->m_tabel_ss->count_all("tb_user",null,null,"*"),
        "recordsFiltered" => $this->m_tabel_ss->count_filtered(array('nik_user','nama','tanggal_daftar','status'),array(null, 'nik_user','nama','tanggal_daftar','simpanan_pokok','status',null),array('status' => 'desc'),"tb_user",null,null,"*"),
        "data" => $data,
      );
      //output dalam format JSON
      echo json_encode($output);
    }

    if ($this->input->post('proses') == 'table_laporan_detail') {
      $ii = 1;
      $bulan = $this->input->post('bulan');
      $tahun = $this->input->post('tahun');
      $cek_data = $this->model->tampil_data_keseluruhan('tb_laporan',['tahun' => $tahun,'bulan' => $bulan])->result();

      $data = json_decode($cek_data[0]->laporan,true);

      foreach ($data as $key => $value) {
        // $data1[$ii]['no'] = $ii;
        $data1[$ii]['tanggal'] = $value['tanggal'];
        $data1[$ii]['ket'] = $value['ket'];
        $data1[$ii]['ket_all'] = json_encode($value['ket_all']);
        $ii++;
        
      }
      // print_r($data1);
      // $data1 = array_reverse($data1, true);
      $out = array_values($data1);
      echo json_encode($out);
      
    }

    if ($this->input->post('proses') == "table_simpanan_pokok") {
      $list = $this->m_tabel_ss->get_datatables(array('nik_user','nama','tanggal_daftar','status','simpanan_pokok'),array(null, 'nik_user','nama','tanggal_daftar','simpanan_pokok','status'),array('status' => 'desc'),"tb_user",null,null,"*");
      $data = array();
      $no = $_POST['start'];
      foreach ($list as $field) {

        $no++;
        $row = array();
        $row[] = $no;
        $row[] = $field->nik_user;
        $row[] = $field->nama;
        $row[] = $field->tanggal_pendaftaran;
        $row[] = 'Rp. ' .  number_format($field->simpanan_pokok);
        $row[] = $field->status;       
        $row[] = '<center><button type="button" onclick="detail_user('.$field->nik_user.')" class="btn btn-primary btn-circle btn-sm waves-effect waves-light"><i class="ico fa fa-edit"></i></button></center>';
        $data[] = $row;
      }

      $output = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->m_tabel_ss->count_all("tb_user",null,null,"*"),
        "recordsFiltered" => $this->m_tabel_ss->count_filtered(array('nik_user','nama','tanggal_daftar','status','simpanan_pokok'),array(null, 'nik_user','nama','tanggal_daftar','simpanan_pokok','status'),array('status' => 'desc'),"tb_user",null,null,"*"),
        "data" => $data,
      );
      //output dalam format JSON
      echo json_encode($output);
    }

    if ($this->input->post('proses') == "table_list_guru_simpanan_wajib") {
      $list = $this->m_tabel_ss->get_datatables(array('nik_user','nama'),array(null, 'nik_user','nama',null,null,null),array('tanggaL_daftar' => 'desc'),"tb_user",null,['status' => 'aktif'],"*");
      $data = array();
      $no = $_POST['start'];
      function date_simpanan_wajib($a,$b)
      {
        return strcmp($a['tanggal_simpanan'],$b['tanggal_simpanan']);
      }
      foreach ($list as $field) {
        

        $simpanan_wajib = json_decode($field->simpanan_wajib,true) ?? null;
        if($simpanan_wajib != null){

          
          /// atur kembali array berdasarkan tanggal
          usort($simpanan_wajib , 'date_simpanan_wajib');
          end($simpanan_wajib); 
          $key = key($simpanan_wajib);
          $simpanan_wajib = $simpanan_wajib[$key];
          ////pilih array yg terakhir dari key
          
        }

        $no++;
        $row = array();
        $row[] = $no;
        $row[] = $field->nik_user;
        $row[] = $field->nama;
        $row[] = $simpanan_wajib['tanggal_simpanan'] ?? 'Belum Pernah Melakukan Simpanan Wajib ';
        $row[] = ($simpanan_wajib) ? 'Rp. '.number_format( $simpanan_wajib['simpanan']) : '-';
        $row[] = '<center><button type="button" onclick="detail_user('.$field->nik_user.')" class="btn btn-primary btn-circle btn-sm waves-effect waves-light"><i class="ico fa fa-edit"></i></button></center>';
        $data[] = $row;
      }

      $output = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->m_tabel_ss->count_all("tb_user",null,['status' => 'aktif'],"*"),
        "recordsFiltered" => $this->m_tabel_ss->count_filtered(array('nik_user','nama'),array(null, 'nik_user','nama',null,null,null),array('tanggaL_daftar' => 'desc'),"tb_user",null,['status' => 'aktif'],"*"),
        "data" => $data,
      );
      //output dalam format JSON
      echo json_encode($output);
    }

    if ($this->input->post('proses') == "table_list_guru_simpanan_sukarela") {
      $list = $this->m_tabel_ss->get_datatables(array('nik_user','nama'),array(null, 'nik_user','nama',null,null,null),array('tanggaL_daftar' => 'desc'),"tb_user",null,['status' => 'aktif'],"*");
      $data = array();
      function date_simpanan($a,$b)
      {
        return strcmp($a['tanggal_simpanan'],$b['tanggal_simpanan']);
      }
      $no = $_POST['start'];
      foreach ($list as $field) {
        

        $simpanan_sukarela = json_decode($field->simpanan_sukarela,true) ?? null;
        if($simpanan_sukarela != null){

         
          /// atur kembali array berdasarkan tanggal
          usort($simpanan_sukarela , 'date_simpanan');
          end($simpanan_sukarela); 
          $key = key($simpanan_sukarela);
          $simpanan_sukarela = $simpanan_sukarela[$key];
          ////pilih array yg terakhir dari key
          
        }

        $no++;
        $row = array();
        $row[] = $no;
        $row[] = $field->nik_user;
        $row[] = $field->nama;
        $row[] = $simpanan_sukarela['tanggal_simpanan'] ?? 'Belum Pernah Melakukan Simpanan Sukarela ';
        $row[] = ($simpanan_sukarela) ? 'Rp. '.number_format( $simpanan_sukarela['simpanan']) : '-';
        $row[] = '<center><button type="button" onclick="detail_user('.$field->nik_user.')" class="btn btn-primary btn-circle btn-sm waves-effect waves-light"><i class="ico fa fa-edit"></i></button></center>';
        $data[] = $row;
      }

      $output = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->m_tabel_ss->count_all("tb_user",null,['status' => 'aktif'],"*"),
        "recordsFiltered" => $this->m_tabel_ss->count_filtered(array('nik_user','nama'),array(null, 'nik_user','nama',null,null,null),array('tanggaL_daftar' => 'desc'),"tb_user",null,['status' => 'aktif'],"*"),
        "data" => $data,
      );
      //output dalam format JSON
      echo json_encode($output);
    }

    if ($this->input->post('proses') == 'cari_usernya') {
      $search = $this->input->post('searchTerm');
	    $fetchData = $this->model->tampil_data_where('tb_user',"status = 'Aktif' and (nik_user like '%".$search."%' or nama like '%".$search."%' ) limit 5")->result();
      $data = array();

      // while ($row = mysqli_fetch_array($fetchData)) {
      //     $data[] = array("id"=>$row['id'], "text"=>$row['name']);
      // }
      foreach ($fetchData as $key => $value) {
        $data[] = array("id" => $value->nik_user, "text" => $value->nik_user.' | '.$value->nama);
      }

      echo json_encode($data);
    }

    if ($this->input->post('proses') == 'cari_barangnya') {
      $search = $this->input->post('searchTerm');
	    $fetchData = $this->model->tampil_data_where('tb_barang',"nama_barang like '%".$search."%'  limit 5")->result();
      $data = array();

      // while ($row = mysqli_fetch_array($fetchData)) {
      //     $data[] = array("id"=>$row['id'], "text"=>$row['name']);
      // }
      foreach ($fetchData as $key => $value) {
        $data[] = array("id" => $value->id_barang.'/'.$value->satuan.'/'.$value->jumlah.'/'.$value->nama_barang, "text" => $value->nama_barang.' | Stok :  '.$value->jumlah.' ' .$value->satuan);
      }

      echo json_encode($data);
    }


    if ($this->input->post('proses') == 'table_simpanan_user_wajib') {
      $i = 1;
      $cek_data = $this->model->tampil_data_where('tb_user',array('nik_user' => $this->input->post('nik_user')))->result();
      function date_simpanan($a,$b)
      {
        return strcmp($a['tanggal_simpan'],$b['tanggal_simpan']);
      }
      
      if(count($cek_data) > 0){
        $ket = json_decode($cek_data[0]->simpanan_wajib,true);
        
        /// atur kembali array berdasarkan tanggal
        usort($ket , 'date_simpanan');
        foreach ($ket as $key => $value) {
          // $data[$i]['no'] = $i;
          $data[$i]['waktu'] = $value['tanggal_simpan'];
          $data[$i]['tahun'] = $value['tahun'];
          $data[$i]['bulan'] = $value['bulan'];
          $data[$i]['ket'] = 'Rp. '. number_format($value['simpanan']);
          // $data[$i]['foto'] = $value['foto'];

          $i++;
          
        }
        $data = array_reverse($data, true);
        $out = array_values($data);
        echo json_encode($out);
      }
      else
      {
        echo json_encode(array());
      }
    }

    if ($this->input->post('proses') == 'table_simpanan_user_sukarela') {
      $i = 1;
      $cek_data = $this->model->tampil_data_where('tb_user',array('nik_user' => $this->input->post('nik_user')))->result();
      
      if(count($cek_data) > 0){
        $ket = ($cek_data[0]->simpanan_sukarela != null) ? json_decode($cek_data[0]->simpanan_sukarela,true) : null;
        function date_simpanan($a,$b)
        {
          return strcmp($a['tanggal'],$b['tanggal']);
        }
        /// atur kembali array berdasarkan tanggal
        if ($ket != null){
          usort($ket , 'date_simpanan');
          foreach ($ket as $key => $value) {
            // $data[$i]['no'] = $i;
            $data[$i]['waktu'] = $value['tanggal'];
            $data[$i]['ket'] = 'Rp. '. number_format($value['simpanan']);
            // $data[$i]['foto'] = $value['foto'];

            $i++;
            
          }
          $data = array_reverse($data, true);
          $out = array_values($data);
          echo json_encode($out);
        }else{
          echo json_encode(array());
        }
        
      }
      else
      {
        echo json_encode(array());
      }
    }

    if ($this->input->post('proses') == 'table_pinjaman_user') {
      $i = 1;
      $cek_data = $this->model->tampil_data_where('tb_user',array('nik_user' => $this->input->post('nik_user')))->result();
      function date_pinjaman($a,$b)
      {
        return strcmp($a['tanggal'],$b['tanggal']);
      }
      
      $ket = json_decode($cek_data[0]->pinjaman,true) ?? null;
      if($ket != null){
        
        
        /// atur kembali array berdasarkan tanggal
        usort($ket , 'date_pinjaman');
        foreach ($ket as $key => $value) {
          // $data[$i]['no'] = $i;
          $data[$i]['waktu'] = $value['tanggal'];
          $data[$i]['ket'] = 'Rp. '. number_format($value['pinjaman']);
          // $data[$i]['foto'] = $value['foto'];

          $i++;
          
        }
        $data = array_reverse($data, true);
        $out = array_values($data);
        echo json_encode($out);
      }
      else
      {
        echo json_encode(array());
      }
    }

    if ($this->input->post('proses') == 'table_pengembalian_user') {
      $i = 1;
      $cek_data = $this->model->tampil_data_where('tb_user',array('nik_user' => $this->input->post('nik_user')))->result();
      function date_pengembalian($a,$b)
      {
        return strcmp($a['tanggal'],$b['tanggal']);
      }
      
      $ket = json_decode($cek_data[0]->pengembalian,true);
      if($ket != null){
        
        
        /// atur kembali array berdasarkan tanggal
        usort($ket , 'date_pengembalian');
        foreach ($ket as $key => $value) {
          // $data[$i]['no'] = $i;
          $data[$i]['waktu'] = $value['tanggal'];
          $data[$i]['ket'] = 'Rp. '. number_format($value['pengembalian']);
          // $data[$i]['foto'] = $value['foto'];

          $i++;
          
        }
        $data = array_reverse($data, true);
        $out = array_values($data);
        echo json_encode($out);
      }
      else
      {
        echo json_encode(array());
      }
    }
    
    if ($this->input->post('proses') == "table_pinjaman_pengembalian") {
      $list = $this->m_tabel_ss->get_datatables(array('nik_user','nama'),array(null, 'nik_user','nama',null,null,null,null),array('tanggaL_daftar' => 'desc'),"tb_user",null,['status' => 'aktif'],"*");
      $data = array();
      $no = $_POST['start'];
      foreach ($list as $field) {
        
        $pinjaman_array = json_decode($field->pinjaman,true) ?? null;
        $pengembalian_array = json_decode($field->pengembalian,true) ?? null;

        $pinjaman = 0;
        $pengembalian = 0;

        $ket_pinjaman = null;
        $ket_pengembalian = null;
        

        if($pinjaman_array != null){
          foreach ($pinjaman_array as $key => $value) {
            $pinjaman += $value['pinjaman'];
          }

          end($pinjaman_array); 
          $key = key($pinjaman_array);
          $ket_pinjaman = $pinjaman_array[$key]['tanggal'] . ' | Rp. '. number_format($pinjaman_array[$key]['pinjaman']);
        }

        if($pengembalian_array != null){
          foreach ($pengembalian_array as $key => $value) {
            $pengembalian += $value['pengembalian'];
          }

          end($pengembalian_array); 
          $key = key($pengembalian_array);
          $ket_pengembalian = $pengembalian_array[$key]['tanggal'] . ' | Rp. '. number_format($pengembalian_array[$key]['pengembalian']);
        }

        $allnya = $pinjaman - $pengembalian;

        $no++;
        $row = array();
        $row[] = $no;
        $row[] = $field->nik_user;
        $row[] = $field->nama;
        $row[] = ($ket_pinjaman) ? $ket_pinjaman : 'Belum Pernah Melakukan Peminjaman';
        $row[] = ($ket_pengembalian) ? $ket_pengembalian : '-';
        // $row[] = ($allnya == 0) ? 'Tiada Pinjaman Tersisa' : $allnya;
        $row[] = ($pinjaman_array != null) ? (($allnya == 0) ? 'Tiada Pinjaman Tersisa' : 'Rp.'. number_format( $allnya)) : '-';
        $row[] = '<center><button type="button" onclick="detail_user('.$field->nik_user.')" class="btn btn-primary btn-circle btn-sm waves-effect waves-light"><i class="ico fa fa-edit"></i></button></center>';
        $data[] = $row;
      }

      $output = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->m_tabel_ss->count_all("tb_user",null,['status' => 'aktif'],"*"),
        "recordsFiltered" => $this->m_tabel_ss->count_filtered(array('nik_user','nama'),array(null, 'nik_user','nama',null,null,null,null),array('tanggaL_daftar' => 'desc'),"tb_user",null,['status' => 'aktif'],"*"),
        "data" => $data,
      );
      //output dalam format JSON
      echo json_encode($output);
    }

    if ($this->input->post('proses') == "table_barang") {
      $list = $this->m_tabel_ss->get_datatables(array('nama_barang','satuan','jumlah','harga'),array(null, 'nama_barang','jumlah',null,null),array('jumlah' => 'desc'),"tb_barang",null,null,"*");
      $data = array();
      $no = $_POST['start'];
      
      foreach ($list as $field) {
        $row_pinjaman_terakhir = ($field->log_pinjaman == null) ? null : json_decode($field->log_pinjaman,true);

        if($row_pinjaman_terakhir != null) {
          end($row_pinjaman_terakhir); 
          $key = key($row_pinjaman_terakhir);
          $row_pinjaman_terakhir = $row_pinjaman_terakhir[$key];
          $cek_data_user = $this->model->tampil_data_where('tb_user',['nik_user' => $row_pinjaman_terakhir['nik_user']])->result()[0];
          $ket_pinjaman_terakhir = $row_pinjaman_terakhir['tanggal']. ' | '.$cek_data_user->nama .' | '.$row_pinjaman_terakhir['pinjaman'].' | '.$field->satuan;
        }

        $no++;
        $row = array();
        $row[] = $no;
        $row[] = $field->nama_barang;
        $row[] = ( $field->jumlah == 0) ? 'Habis Stok' :$field->jumlah.' '.$field->satuan;
        $row[] = ($field->log_pinjaman == null) ? 'Tiada Pinjaman Pernah Dilakukan' : $ket_pinjaman_terakhir;
        $row[] = '<center><button type="button" onclick="detail_barang('.$field->id_barang.')" class="btn btn-primary btn-circle btn-sm waves-effect waves-light"><i class="ico fa fa-edit"></i></button></center>';
        $data[] = $row;
      }

      $output = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->m_tabel_ss->count_all("tb_barang",null,null,"*"),
        "recordsFiltered" => $this->m_tabel_ss->count_filtered(array('nama_barang','satuan','jumlah','harga'),array(null, 'nama_barang','jumlah',null,null),array('jumlah' => 'desc'),"tb_barang",null,null,"*"),
        "data" => $data,
      );
      //output dalam format JSON
      echo json_encode($output);
    }

    if ($this->input->post('proses') == 'table_pinjaman_barang_detail') {
      $i = 1;
      $cek_data = $this->model->tampil_data_where('tb_barang',array('id_barang' => $this->input->post('id_barang')))->result()[0];
      $ket = json_decode($cek_data->log_pinjaman,true) ?? null;
      if($ket != null){
        
        
       
        foreach ($ket as $key => $value) {
          $cek_data_user = $this->model->tampil_data_where('tb_user',['nik_user' => $value['nik_user']])->result()[0];


          // $data[$i]['no'] = $i;
          $data[$i]['peminjam'] = $cek_data_user->nama;
          $data[$i]['stok_sebelumnya'] = $value['stok_sebelumnya'].' '. $cek_data->satuan;
          $data[$i]['pinjaman'] = $value['pinjaman'].' '. $cek_data->satuan;
          $data[$i]['jumlah_stok'] = $value['jumlah_stok'].' '. $cek_data->satuan;
          $data[$i]['waktu'] = $value['tanggal'];
          // $data[$i]['foto'] = $value['foto'];

          $i++;
          
        }
        $out = array_values($data);
        echo json_encode($out);
      }
      else
      {
        echo json_encode(array());
      }
    }

    if ($this->input->post('proses') == 'table_pengembalian_barang_detail') {
      $i = 1;
      $cek_data = $this->model->tampil_data_where('tb_barang',array('id_barang' => $this->input->post('id_barang')))->result()[0];
      $ket = json_decode($cek_data->log_pengembalian,true) ?? null;
      if($ket != null){
        
        
       
        foreach ($ket as $key => $value) {
          $cek_data_user = $this->model->tampil_data_where('tb_user',['nik_user' => $value['nik_user']])->result()[0];


          // $data[$i]['no'] = $i;
          $data[$i]['peminjam'] = $cek_data_user->nama;
          $data[$i]['stok_sebelumnya'] = $value['stok_sebelumnya'].' '. $cek_data->satuan;
          $data[$i]['pengembalian'] = $value['pengembalian'].' '. $cek_data->satuan;
          $data[$i]['jumlah_stok'] = $value['jumlah_stok'].' '. $cek_data->satuan;
          $data[$i]['waktu'] = $value['tanggal'];
          // $data[$i]['foto'] = $value['foto'];

          $i++;
          
        }
        $out = array_values($data);
        echo json_encode($out);
      }
      else
      {
        echo json_encode(array());
      }
    }
    

    if ($this->input->post('proses') == 'table_simpanan_wajib_laporan_all') {
      $i = 1;
      $cek_data = $this->model->tampil_data_keseluruhan('tb_user')->result();

      // $array_simpnanan_wajib_detail =[];
      $data=null;
      foreach ($cek_data as $key => $value) {
        $ket_simpanan_wajib = json_decode($value->simpanan_wajib,true) ?? null;
        
        
        if ($ket_simpanan_wajib !=null) {
          foreach ($ket_simpanan_wajib as $key1 => $value1) {
            $data[$i]['nik_user'] = $value->nik_user;
            $data[$i]['nama'] = $value->nama;
            $data[$i]['tanggal_simpanan'] = $value1['tanggal_simpanan'];
            $data[$i]['simpanan'] = $value1['simpanan'];
            $i ++;
            
          }
        }

      }



      function date_simpanan($a,$b)
      {
        return strcmp($a['tanggal_simpanan'],$b['tanggal_simpanan']);
      }
      
      if($data != null){
        // $ket = json_decode($cek_data[0]->simpanan_wajib,true);
        
        $ii = 1;
        /// atur kembali array berdasarkan tanggal
        usort($data , 'date_simpanan');
        foreach ($data as $key => $value) {
          $data1[$ii]['no'] = $ii;
          $data1[$ii]['nik'] = $value['nik_user'];
          $data1[$ii]['nama'] = $value['nama'];
          $data1[$ii]['tanggal_simpanan'] = $value['tanggal_simpanan'];
          $data1[$ii]['simpanan'] = 'Rp. '. number_format($value['simpanan']);
          // $data[$ii]['foto'] = $value['foto'];

          $ii++;
          
        }
        // print_r($data1);
        $data1 = array_reverse($data1, true);
        $out = array_values($data1);
        echo json_encode($out);
      }
      else
      {
        echo json_encode(array());
      }
      
    }


    if ($this->input->post('proses') == 'table_simpanan_sukarela_laporan_all') {
      $i = 1;
      $cek_data = $this->model->tampil_data_keseluruhan('tb_user')->result();

      // $array_simpnanan_sukarela_detail =[];
      $data=null;
      foreach ($cek_data as $key => $value) {
        $ket_simpanan_sukarela = json_decode($value->simpanan_sukarela,true) ?? null;
        
        
        if ($ket_simpanan_sukarela !=null) {
          foreach ($ket_simpanan_sukarela as $key1 => $value1) {
            $data[$i]['nik_user'] = $value->nik_user;
            $data[$i]['nama'] = $value->nama;
            $data[$i]['tanggal_simpanan'] = $value1['tanggal_simpanan'];
            $data[$i]['simpanan'] = $value1['simpanan'];
            $i ++;
            
          }
        }

      }



      function date_simpanan($a,$b)
      {
        return strcmp($a['tanggal_simpanan'],$b['tanggal_simpanan']);
      }
      
      if($data != null){
        // $ket = json_decode($cek_data[0]->simpanan_sukarela,true);
        
        $ii = 1;
        /// atur kembali array berdasarkan tanggal
        usort($data , 'date_simpanan');
        foreach ($data as $key => $value) {
          $data1[$ii]['no'] = $ii;
          $data1[$ii]['nik'] = $value['nik_user'];
          $data1[$ii]['nama'] = $value['nama'];
          $data1[$ii]['tanggal_simpanan'] = $value['tanggal_simpanan'];
          $data1[$ii]['simpanan'] = 'Rp. '. number_format($value['simpanan']);
          // $data[$ii]['foto'] = $value['foto'];

          $ii++;
          
        }
        // print_r($data1);
        $out = array_values($data1);
        echo json_encode($out);
      }
      else
      {
        echo json_encode(array());
      }
      
    }

    if ($this->input->post('proses') == 'table_pinjaman_barang_laporan_all') {
      $i = 1;
      $cek_data = $this->model->tampil_data_keseluruhan('tb_barang')->result();

      // $array_simpnanan_sukarela_detail =[];
      $data=null;
      foreach ($cek_data as $key => $value) {
        $pinjaman_barang = json_decode($value->log_pinjaman,true) ?? null;
        
        
        if ($pinjaman_barang !=null) {
          foreach ($pinjaman_barang as $key1 => $value1) {
            $cek_data_user = $this->model->tampil_data_where('tb_user',['nik_user' => $value1['nik_user']])->result();
            $data[$i]['nik_user'] = $cek_data_user[0]->nik_user;
            $data[$i]['nama'] = $cek_data_user[0]->nama;
            $data[$i]['barang'] = $value->nama_barang;
            $data[$i]['waktu'] = $value1['tanggal'];
            $data[$i]['pinjaman'] = $value1['pinjaman'].' '.$value->satuan;
            $i ++;
            
          }
        }

      }



      function date_simpanan($a,$b)
      {
        return strcmp($a['waktu'],$b['waktu']);
      }
      
      if($data != null){
        // $ket = json_decode($cek_data[0]->simpanan_sukarela,true);
        
        $ii = 1;
        /// atur kembali array berdasarkan tanggal
        usort($data , 'date_simpanan');
        foreach ($data as $key => $value) {
          $data1[$ii]['no'] = $ii;
          $data1[$ii]['nik'] = $value['nik_user'];
          $data1[$ii]['nama'] = $value['nama'];
          $data1[$ii]['barang'] = $value['barang'];
          $data1[$ii]['waktu'] = $value['waktu'];
          $data1[$ii]['pinjaman'] = $value['pinjaman'];
          // $data[$ii]['foto'] = $value['foto'];

          $ii++;
          
        }
        // print_r($data1);
        $out = array_values($data1);
        echo json_encode($out);
      }
      else
      {
        echo json_encode(array());
      }
      
    }


    if ($this->input->post('proses') == 'table_pengembalian_barang_laporan_all') {
      $i = 1;
      $cek_data = $this->model->tampil_data_keseluruhan('tb_barang')->result();

      // $array_simpnanan_sukarela_detail =[];
      $data=null;
      foreach ($cek_data as $key => $value) {
        $pengembalian_barang = json_decode($value->log_pengembalian,true) ?? null;
        
        
        if ($pengembalian_barang !=null) {
          foreach ($pengembalian_barang as $key1 => $value1) {
            $cek_data_user = $this->model->tampil_data_where('tb_user',['nik_user' => $value1['nik_user']])->result();
            $data[$i]['nik_user'] = $cek_data_user[0]->nik_user;
            $data[$i]['nama'] = $cek_data_user[0]->nama;
            $data[$i]['barang'] = $value->nama_barang;
            $data[$i]['waktu'] = $value1['tanggal'];
            $data[$i]['pengembalian'] = $value1['pengembalian'].' '.$value->satuan;
            $i ++;
            
          }
        }

      }



      function date_simpanan($a,$b)
      {
        return strcmp($a['waktu'],$b['waktu']);
      }
      
      if($data != null){
        // $ket = json_decode($cek_data[0]->simpanan_sukarela,true);
        
        $ii = 1;
        /// atur kembali array berdasarkan tanggal
        usort($data , 'date_simpanan');
        foreach ($data as $key => $value) {
          $data1[$ii]['no'] = $ii;
          $data1[$ii]['nik'] = $value['nik_user'];
          $data1[$ii]['nama'] = $value['nama'];
          $data1[$ii]['barang'] = $value['barang'];
          $data1[$ii]['waktu'] = $value['waktu'];
          $data1[$ii]['pengembalian'] = $value['pengembalian'];
          // $data[$ii]['foto'] = $value['foto'];

          $ii++;
          
        }
        // print_r($data1);
        $out = array_values($data1);
        echo json_encode($out);
      }
      else
      {
        echo json_encode(array());
      }
      
    }


    if ($this->input->post('proses') == 'table_simpanan_wajib_laporan_berdasarkan_tahun_bulan') {
      $i = 1;
      $bulan = $this->input->post('bulan');
      $tahun = $this->input->post('tahun');
      $cek_data = $this->model->tampil_data_keseluruhan('tb_user')->result();

      // $array_simpnanan_wajib_detail =[];
      $data=null;
      foreach ($cek_data as $key => $value) {
        $ket_simpanan_wajib = json_decode($value->simpanan_wajib,true) ?? null;
        
        
        if ($ket_simpanan_wajib !=null) {
          foreach ($ket_simpanan_wajib as $key1 => $value1) {
            $datetime = new DateTime($value1['tanggal_simpanan']);
            $bulannya = $datetime->format('m');
            $tahunnya = $datetime->format('Y');
            
            if($bulannya == $bulan and $tahunnya == $tahun){
              $data[$i]['nik_user'] = $value->nik_user;
              $data[$i]['nama'] = $value->nama;
              $data[$i]['tanggal_simpanan'] = $value1['tanggal_simpanan'];
              $data[$i]['simpanan'] = $value1['simpanan'];
              $i ++;
            }  
            
          }
        }

      }



      function date_simpanan($a,$b)
      {
        return strcmp($a['tanggal_simpanan'],$b['tanggal_simpanan']);
      }
      
      if($data != null){
        // $ket = json_decode($cek_data[0]->simpanan_wajib,true);
        
        $ii = 1;
        /// atur kembali array berdasarkan tanggal
        usort($data , 'date_simpanan');
        foreach ($data as $key => $value) {
          $data1[$ii]['no'] = $ii;
          $data1[$ii]['nik'] = $value['nik_user'];
          $data1[$ii]['nama'] = $value['nama'];
          $data1[$ii]['tanggal_simpanan'] = $value['tanggal_simpanan'];
          $data1[$ii]['simpanan'] = 'Rp. '. number_format($value['simpanan']);
          // $data[$ii]['foto'] = $value['foto'];

          $ii++;
          
        }
        // print_r($data1);
        $out = array_values($data1);
        echo json_encode($out);
      }
      else
      {
        echo json_encode(array());
      }
      
    }

    if ($this->input->post('proses') == 'table_simpanan_sukarela_laporan_berdasarkan_tahun_bulan') {
      $i = 1;
      $bulan = $this->input->post('bulan');
      $tahun = $this->input->post('tahun');
      $cek_data = $this->model->tampil_data_keseluruhan('tb_user')->result();

      // $array_simpnanan_sukarela_detail =[];
      $data=null;
      foreach ($cek_data as $key => $value) {
        $ket_simpanan_sukarela = json_decode($value->simpanan_sukarela,true) ?? null;
        
        
        if ($ket_simpanan_sukarela !=null) {
          foreach ($ket_simpanan_sukarela as $key1 => $value1) {
            $datetime = new DateTime($value1['tanggal_simpanan']);
            $bulannya = $datetime->format('m');
            $tahunnya = $datetime->format('Y');
            
            if($bulannya == $bulan and $tahunnya == $tahun){
              $data[$i]['nik_user'] = $value->nik_user;
              $data[$i]['nama'] = $value->nama;
              $data[$i]['tanggal_simpanan'] = $value1['tanggal_simpanan'];
              $data[$i]['simpanan'] = $value1['simpanan'];
              $i ++;
            }  
            
          }
        }

      }



      function date_simpanan($a,$b)
      {
        return strcmp($a['tanggal_simpanan'],$b['tanggal_simpanan']);
      }
      
      if($data != null){
        // $ket = json_decode($cek_data[0]->simpanan_sukarela,true);
        
        $ii = 1;
        /// atur kembali array berdasarkan tanggal
        usort($data , 'date_simpanan');
        foreach ($data as $key => $value) {
          $data1[$ii]['no'] = $ii;
          $data1[$ii]['nik'] = $value['nik_user'];
          $data1[$ii]['nama'] = $value['nama'];
          $data1[$ii]['tanggal_simpanan'] = $value['tanggal_simpanan'];
          $data1[$ii]['simpanan'] = 'Rp. '. number_format($value['simpanan']);
          // $data[$ii]['foto'] = $value['foto'];

          $ii++;
          
        }
        // print_r($data1);
        $out = array_values($data1);
        echo json_encode($out);
      }
      else
      {
        echo json_encode(array());
      }
      
    }

    if ($this->input->post('proses') == 'table_pinjaman_barang_laporan_bulan_tahun') {
      $i = 1;
      $bulan = $this->input->post('bulan');
      $tahun = $this->input->post('tahun');
      $cek_data = $this->model->tampil_data_keseluruhan('tb_barang')->result();

      // $array_simpnanan_sukarela_detail =[];
      $data=null;
      foreach ($cek_data as $key => $value) {
        $pinjaman_barang = json_decode($value->log_pinjaman,true) ?? null;
        
        
        if ($pinjaman_barang !=null) {
          foreach ($pinjaman_barang as $key1 => $value1) {
            $datetime = new DateTime($value1['tanggal']);
            $bulannya = $datetime->format('m');
            $tahunnya = $datetime->format('Y');
            if($bulannya == $bulan and $tahunnya == $tahun){
              $cek_data_user = $this->model->tampil_data_where('tb_user',['nik_user' => $value1['nik_user']])->result();
              $data[$i]['nik_user'] = $cek_data_user[0]->nik_user;
              $data[$i]['nama'] = $cek_data_user[0]->nama;
              $data[$i]['barang'] = $value->nama_barang;
              $data[$i]['waktu'] = $value1['tanggal'];
              $data[$i]['pinjaman'] = $value1['pinjaman'].' '.$value->satuan;
              $i ++;
            }
              
            
          }
        }

      }



      function date_simpanan($a,$b)
      {
        return strcmp($a['waktu'],$b['waktu']);
      }
      
      if($data != null){
        // $ket = json_decode($cek_data[0]->simpanan_sukarela,true);
        
        $ii = 1;
        /// atur kembali array berdasarkan tanggal
        usort($data , 'date_simpanan');
        foreach ($data as $key => $value) {
          $data1[$ii]['no'] = $ii;
          $data1[$ii]['nik'] = $value['nik_user'];
          $data1[$ii]['nama'] = $value['nama'];
          $data1[$ii]['barang'] = $value['barang'];
          $data1[$ii]['waktu'] = $value['waktu'];
          $data1[$ii]['pinjaman'] = $value['pinjaman'];
          // $data[$ii]['foto'] = $value['foto'];

          $ii++;
          
        }
        // print_r($data1);
        $out = array_values($data1);
        echo json_encode($out);
      }
      else
      {
        echo json_encode(array());
      }
      
    }

    if ($this->input->post('proses') == 'table_pengembalian_barang_laporan_bulan_tahun') {
      $i = 1;
      $bulan = $this->input->post('bulan');
      $tahun = $this->input->post('tahun');
      $cek_data = $this->model->tampil_data_keseluruhan('tb_barang')->result();

      // $array_simpnanan_sukarela_detail =[];
      $data=null;
      foreach ($cek_data as $key => $value) {
        $pengembalian_barang = json_decode($value->log_pengembalian,true) ?? null;
        
        
        if ($pengembalian_barang !=null) {
          foreach ($pengembalian_barang as $key1 => $value1) {
            $datetime = new DateTime($value1['tanggal']);
            $bulannya = $datetime->format('m');
            $tahunnya = $datetime->format('Y');
            if($bulannya == $bulan and $tahunnya == $tahun){
              $cek_data_user = $this->model->tampil_data_where('tb_user',['nik_user' => $value1['nik_user']])->result();
              $data[$i]['nik_user'] = $cek_data_user[0]->nik_user;
              $data[$i]['nama'] = $cek_data_user[0]->nama;
              $data[$i]['barang'] = $value->nama_barang;
              $data[$i]['waktu'] = $value1['tanggal'];
              $data[$i]['pengembalian'] = $value1['pengembalian'].' '.$value->satuan;
              $i ++;
            }
              
            
          }
        }

      }



      function date_pengembalian($a,$b)
      {
        return strcmp($a['waktu'],$b['waktu']);
      }
      
      if($data != null){
        // $ket = json_decode($cek_data[0]->simpanan_sukarela,true);
        
        $ii = 1;
        /// atur kembali array berdasarkan tanggal
        usort($data , 'date_pengembalian');
        foreach ($data as $key => $value) {
          $data1[$ii]['no'] = $ii;
          $data1[$ii]['nik'] = $value['nik_user'];
          $data1[$ii]['nama'] = $value['nama'];
          $data1[$ii]['barang'] = $value['barang'];
          $data1[$ii]['waktu'] = $value['waktu'];
          $data1[$ii]['pengembalian'] = $value['pengembalian'];
          // $data[$ii]['foto'] = $value['foto'];

          $ii++;
          
        }
        // print_r($data1);
        $out = array_values($data1);
        echo json_encode($out);
      }
      else
      {
        echo json_encode(array());
      }
      
    }

  }


  // function coba2(){
  //   $i = 1;
  //   $bulan = '02';
  //   $tahun = '2021';
  //   $cek_data = $this->model->tampil_data_keseluruhan('tb_user')->result();

  //   // $array_simpnanan_wajib_detail =[];
  //   $data=null;
  //   foreach ($cek_data as $key => $value) {
  //     $ket_simpanan_wajib = json_decode($value->simpanan_wajib,true) ?? null;
      
      
  //     if ($ket_simpanan_wajib !=null) {
  //       foreach ($ket_simpanan_wajib as $key1 => $value1) {
  //         $datetime = new DateTime($value1['tanggal_simpanan']);
  //         $bulannya = $datetime->format('m');
  //         $tahunnya = $datetime->format('Y');
          
  //         if($bulannya == $bulan and $tahunnya == $tahun){
  //           $data[$i]['nik_user'] = $value->nik_user;
  //           $data[$i]['nama'] = $value->nama;
  //           $data[$i]['tanggal_simpanan'] = $value1['tanggal_simpanan'];
  //           $data[$i]['simpanan'] = $value1['simpanan'];
  //           $i ++;
  //         }  
          
          
  //       }
  //     }

  //   }

  //   // print_r($data);



  //     function date_simpanan($a,$b)
  //     {
  //       return strcmp($a['tanggal_simpanan'],$b['tanggal_simpanan']);
  //     }
      
  //     if($data != null){
  //       // $ket = json_decode($cek_data[0]->simpanan_wajib,true);
        
  //       $ii = 1;
  //       /// atur kembali array berdasarkan tanggal
  //       usort($data , 'date_simpanan');
  //       foreach ($data as $key => $value) {
  //         $data1[$ii]['no'] = $ii;
  //         $data1[$ii]['nik'] = $value['nik_user'];
  //         $data1[$ii]['nama'] = $value['nama'];
  //         $data1[$ii]['tanggal_simpanan'] = $value['tanggal_simpanan'];
  //         $data1[$ii]['simpanan'] = 'Rp. '. number_format($value['simpanan']);
  //         // $data[$ii]['foto'] = $value['foto'];

  //         $ii++;
          
  //       }
  //       // print_r($data1);
  //       $out = array_values($data1);
  //       echo json_encode($out);
  //     }
  //     else
  //     {
  //       echo json_encode(array());
  //     }
  // }


  

   
}
?>