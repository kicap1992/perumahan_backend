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
        "recordsFiltered" => $this->m_tabel_ss->count_filtered(array('nik_user','nama','tanggal_daftar','status'),array(null, 'nik_user','nama','tanggal_daftar','simpanan_pokok','status',null),array('status' => 'desc'),"tb_user",null,null,"*"),
        "data" => $data,
      );
      //output dalam format JSON
      echo json_encode($output);
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
      $list = $this->m_tabel_ss->get_datatables(array('nik_user','nama'),array(null, 'nik_user','nama',null,null,null),array('tanggaL-daftar' => 'desc'),"tb_user",null,['status' => 'aktif'],"*");
      $data = array();
      $no = $_POST['start'];
      foreach ($list as $field) {
        

        $simpanan_wajib = json_decode($field->simpanan_wajib,true) ?? null;
        if($simpanan_wajib != null){

          function date_simpanan($a,$b)
          {
            return strcmp($a['tanggal_simpanan'],$b['tanggal_simpanan']);
          }
          /// atur kembali array berdasarkan tanggal
          usort($simpanan_wajib , 'date_simpanan');
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
        "recordsFiltered" => $this->m_tabel_ss->count_filtered(array('nik_user','nama'),array(null, 'nik_user','nama',null,null,null),array('tanggaL-daftar' => 'desc'),"tb_user",null,['status' => 'aktif'],"*"),
        "data" => $data,
      );
      //output dalam format JSON
      echo json_encode($output);
    }

    if ($this->input->post('proses') == "table_list_guru_simpanan_sukarela") {
      $list = $this->m_tabel_ss->get_datatables(array('nik_user','nama'),array(null, 'nik_user','nama',null,null,null),array('tanggaL-daftar' => 'desc'),"tb_user",null,['status' => 'aktif'],"*");
      $data = array();
      $no = $_POST['start'];
      foreach ($list as $field) {
        

        $simpanan_sukarela = json_decode($field->simpanan_sukarela,true) ?? null;
        if($simpanan_sukarela != null){

          function date_simpanan($a,$b)
          {
            return strcmp($a['tanggal_simpanan'],$b['tanggal_simpanan']);
          }
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
        "recordsFiltered" => $this->m_tabel_ss->count_filtered(array('nik_user','nama'),array(null, 'nik_user','nama',null,null,null),array('tanggaL-daftar' => 'desc'),"tb_user",null,['status' => 'aktif'],"*"),
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


    if ($this->input->post('proses') == 'table_simpanan_user_wajib') {
      $i = 1;
      $cek_data = $this->model->tampil_data_where('tb_user',array('nik_user' => $this->input->post('nik_user')))->result();
      
      if(count($cek_data) > 0){
        $ket = json_decode($cek_data[0]->simpanan_wajib,true);
        function date_simpanan($a,$b)
        {
          return strcmp($a['tanggal_simpanan'],$b['tanggal_simpanan']);
        }
        /// atur kembali array berdasarkan tanggal
        usort($ket , 'date_simpanan');
        foreach ($ket as $key => $value) {
          // $data[$i]['no'] = $i;
          $data[$i]['waktu'] = $value['tanggal_simpanan'];
          $data[$i]['ket'] = 'Rp. '. number_format($value['simpanan']);
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

    if ($this->input->post('proses') == 'table_simpanan_user_sukarela') {
      $i = 1;
      $cek_data = $this->model->tampil_data_where('tb_user',array('nik_user' => $this->input->post('nik_user')))->result();
      
      if(count($cek_data) > 0){
        $ket = json_decode($cek_data[0]->simpanan_sukarela,true);
        function date_simpanan($a,$b)
        {
          return strcmp($a['tanggal_simpanan'],$b['tanggal_simpanan']);
        }
        /// atur kembali array berdasarkan tanggal
        usort($ket , 'date_simpanan');
        foreach ($ket as $key => $value) {
          // $data[$i]['no'] = $i;
          $data[$i]['waktu'] = $value['tanggal_simpanan'];
          $data[$i]['ket'] = 'Rp. '. number_format($value['simpanan']);
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

  }


   
}
?>