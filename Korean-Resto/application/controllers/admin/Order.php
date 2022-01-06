<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Order extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->cek_auth_admin();
        $this->load->library('template');
        $this->load->model('admin/order_m');
    }

    public function index()
    {
        $this->template->display('admin/order/view');
    }

    public function data_list()
    {
        $List = $this->order_m->get_datatables();
        $data = array();
        $no   = $_POST['start'];

        foreach ($List as $r) {
            $no++;
            $row      = array();
            $order_id = $r->order_id;
            if ($r->order_status == 1) {
                $konfirmasi = '<a onclick="konfirmData(' . $order_id . ')" title="Konfirmasi Order"><i class="icon-check"></i></a>';
                $print      = '';
            } elseif ($r->order_status == 2) {
                $konfirmasi = '';
                $print      = '<a onclick="printNota(' . $order_id . ')" title="Print Nota"><i class="icon-printer"></i></a>';
            } else {
                $konfirmasi = '';
                $print      = '';
            }
            $link  = site_url('admin/order/editdata/' . $r->order_id);
            $row[] = '<a href="' . $link . '" title="Detail Order"><i class="icon-eye"></i></a> ' . $konfirmasi . ' ' . $print;
            $row[] = $no;
            $row[] = $r->order_id;
            $row[] = date('d-m-Y', strtotime($r->order_tanggal));
            $row[] = $r->order_nama;
            $row[] = $r->meja_nama;
            $row[] = $r->order_waktu;
            $row[] = number_format($r->order_qty, 0, '', ',');
            $row[] = number_format($r->order_total, 0, '', ',');
            if ($r->order_status == 1) {
                $confirm = '<span class="label label-danger">Belum Konfirm</span>';
            } else {
                $confirm = '<span class="label label-success">Konfirm</span>';
            }
            $row[] = $confirm;
            if ($r->order_status == 1) {
                $status = '<span class="label label-danger">Belum Bayar</span>';
            } else {
                $status = '<span class="label label-success">Bayar</span>';
            }
            $row[]  = $status;
            $data[] = $row;
        }

        $output = array(
            "draw"            => $_POST['draw'],
            "recordsTotal"    => $this->order_m->count_all(),
            "recordsFiltered" => $this->order_m->count_filtered(),
            "data"            => $data,
        );

        echo json_encode($output);
    }

    public function adddata()
    {
        $username = $this->session->userdata('username');
        $NoOrder  = $this->order_m->getnoorder($username);
        // Insert ke Table Order
        $data = array(
            'order_no'      => $NoOrder,
            'user_username' => $username,
            'order_tanggal' => date('Y-m-d'),
            'order_update'  => date('Y-m-d H:i:s'),
        );

        $this->db->insert('resto_order', $data);
        $order_id = $this->db->insert_id();
        redirect(site_url('admin/order/editdata/' . $order_id . '/' . $NoOrder));
    }

    public function editdata($order_id)
    {
        $data['detail'] = $this->db->get_where('v_order', array('order_id' => $order_id))->row();
        $this->template->display('admin/order/edit', $data);
    }

    public function data_menu_list()
    {
        $List = $this->order_m->get_menu_datatables();
        $data = array();
        $no   = $_POST['start'];

        foreach ($List as $r) {
            $no++;
            $row     = array();
            $menu_id = $r->menu_id;
            $row[]   = '<a title="Pilih Menu" class="pilihData"  data-id="' . $menu_id . '" data-code="' . $r->menu_kode . '" data-name="' . trim($r->menu_nama) . '" data-harga="' . $r->menu_harga . '" data-waktu="' . $r->menu_waktu . '"><i class="icon-check"></i></a>';
            $row[]   = $no;
            $row[]   = $r->menu_kode;
            $row[]   = $r->menu_nama;
            $row[]   = $r->kategori_nama;
            $row[]   = $r->menu_waktu;
            $row[]   = number_format($r->menu_harga, 0, '', ',');
            $data[]  = $row;
        }

        $output = array(
            "draw"            => $_POST['draw'],
            "recordsTotal"    => $this->order_m->count_menu_all(),
            "recordsFiltered" => $this->order_m->count_menu_filtered(),
            "data"            => $data,
        );

        echo json_encode($output);
    }

    public function confirmdata($id)
    {
        $this->order_m->confirm_data($id);
    }

    public function data_order_list($order_id)
    {
        $List = $this->order_m->get_order_datatables($order_id);
        $data = array();
        $no   = $_POST['start'];

        foreach ($List as $r) {
            $no++;
            $row             = array();
            $order_detail_id = $r->order_detail_id;
            if ($r->order_status == 1) {
                $row[] = '<a onclick="hapusData(' . $order_detail_id . ')" title="Delete Data"><i class="icon-close"></i></a>';
            } else {
                $row[] = '';
            }
            $row[] = $no;
            $row[] = $r->menu_kode;
            $row[] = $r->menu_nama;
            $row[] = number_format($r->order_detail_qty, 0, '', ',');
            $row[] = number_format($r->order_detail_harga, 0, '', ',');
            $row[] = $r->order_detail_waktu;
            $row[] = number_format($r->order_detail_subtotal, 0, '', ',');
            if ($r->order_detail_status == 1) {
                $status = '<span class="label label-primary">Baru</span>';
            } else {
                $status = '<span class="label label-success">Selesai</span>';
            }
            $row[]  = $status;
            $data[] = $row;
        }

        $output = array(
            "draw"            => $_POST['draw'],
            "recordsTotal"    => $this->order_m->count_order_all($order_id),
            "recordsFiltered" => $this->order_m->count_order_filtered($order_id),
            "data"            => $data,
        );

        echo json_encode($output);
    }

    public function saveitem()
    {
        $order_id  = $this->input->post('order_id', 'true');
        $menu_id   = $this->input->post('menu_id', 'true');
        $checkMenu = $this->db->get_where('resto_order_detail', array('menu_id' => $menu_id, 'order_id' => $order_id))->row();
        if (count($checkMenu) > 0) {
            $response['status'] = 'info';
        } else {
            $this->order_m->insert_data_item();
            $response['status'] = 'success';
        }

        echo json_encode($response);
    }

    public function updateitem()
    {
        $this->order_m->update_data_item();
    }

    public function get_data_total($order_id)
    {
        $data = $this->db->select_sum('order_detail_waktu', 'waktu')->select_sum('order_detail_qty', 'qty')->select_sum('order_detail_subtotal', 'total')->get_where('resto_order_detail', array('order_id' => $order_id))->row();
        echo json_encode($data);
    }

    public function get_data_detail($order_detail_id)
    {
        $data = $this->db->get_where('v_order_detail', array('order_detail_id' => $order_detail_id))->row();
        echo json_encode($data);
    }

    public function get_data($order_id)
    {
        $data = $this->db->get_where('v_order', array('order_id' => $order_id))->row();
        echo json_encode($data);
    }

    public function deletedataitem($id)
    {
        $this->order_m->delete_data_item($id);
    }

    public function bayar()
    {
        $order_id   = $this->input->post('id', 'true');
        $dataJumlah = $this->db->select_sum('order_detail_qty', 'qty')->get_where('resto_order_detail', array('order_id' => $order_id))->row();
        $qty        = $dataJumlah->qty;
        $data       = array(
            'order_diskon'    => intval(str_replace(",", "", $this->input->post('diskon', 'true'))),
            'order_bayar'     => intval(str_replace(",", "", $this->input->post('bayar', 'true'))),
            'order_kembali'   => intval(str_replace(",", "", $this->input->post('kembali', 'true'))),
            'order_tgl_bayar' => date('Y-m-d'),
            'user_username'   => $this->session->userdata('username'),
            'order_status'    => 2, // Bayar
            'order_update'    => date('Y-m-d H:i:s'),
        );

        $this->db->where('order_id', $order_id);
        $this->db->update('resto_order', $data);

        // Update Detail Order jadi Selesai
        $dataDetail = array(
            'order_detail_status' => 2,
            'order_detail_update' => date('Y-m-d H:i:s'),
        );

        $this->db->where('order_id', $order_id);
        $this->db->update('resto_order_detail', $dataDetail);

        // Item Order
        $listItem = $this->db->get_where('resto_order_detail', array('order_id' => $order_id))->result();
        foreach ($listItem as $i) {
            $menu_id  = $i->menu_id;
            $Jual     = $this->db->get_where('resto_menu', array('menu_id' => $menu_id))->row();
            $dataMenu = array(
                'menu_jual' => ($Jual->menu_jual + $i->order_detail_qty),
            );

            $this->db->where('menu_id', $menu_id);
            $this->db->update('resto_menu', $dataMenu);
        }

        $this->cetaknotabayar($order_id);
    }

    public function cetaknotabayar($order_id)
    {
        $data['detail'] = $this->db->get_where('v_order', array('order_id' => $order_id))->row();
        $this->load->view('admin/order/cetak', $data);
    }
}
/* Location: ./application/controller/admin/Order.php */