<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Users_m extends CI_Model
{
    public $table         = 'resto_users';
    public $column_order  = array(null, null, 'user_username', 'user_name', 'user_email', 'user_level', 'user_status');
    public $column_search = array('user_username', 'user_name', 'user_email', 'user_level', 'user_status');
    public $order         = array('user_name' => 'asc');

    public $table1         = 'v_akses';
    public $column_order1  = array(null, null, 'kategori_nama');
    public $column_search1 = array();
    public $order1         = array('kategori_nama' => 'asc');

    public function __construct()
    {
        parent::__construct();
    }

    private function _get_datatables_query()
    {
        if ($this->input->post('lstLevel', 'true')) {
            $this->db->where('user_level', $this->input->post('lstLevel', 'true'));
        }
        if ($this->input->post('lstStatus', 'true')) {
            $this->db->where('user_status', $this->input->post('lstStatus', 'true'));
        }

        $this->db->from($this->table);

        $i = 0;
        foreach ($this->column_search as $item) {
            if ($_POST['search']['value']) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i) {
                    $this->db->group_end();
                }
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }

        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function insert_data()
    {
        $data = array(
            'user_username'    => trim(stripHTMLtags($this->input->post('username', 'true'))),
            'user_password'    => sha1(trim(stripHTMLtags($this->input->post('password', 'true')))),
            'user_name'        => strtoupper(stripHTMLtags($this->input->post('name', 'true'))),
            'user_email'       => trim(stripHTMLtags($this->input->post('email', 'true'))),
            'user_level'       => trim($this->input->post('lstLevel', 'true')),
            'user_date_create' => date('Y-m-d H:i:s'),
            'user_date_update' => date('Y-m-d H:i:s'),
        );

        $this->db->insert('resto_users', $data);
    }

    public function select_by_id($user_username)
    {
        $this->db->select('*');
        $this->db->from('resto_users');
        $this->db->where('user_username', $user_username);

        return $this->db->get();
    }

    public function update_data()
    {
        $user_username = $this->input->post('id', 'true');
        $password      = trim($this->input->post('password', 'true'));

        if (!empty($password)) {
            $data = array(
                'user_password'    => sha1(trim(stripHTMLtags($this->input->post('password', 'true')))),
                'user_name'        => strtoupper(stripHTMLtags($this->input->post('name', 'true'))),
                'user_email'       => trim(stripHTMLtags($this->input->post('email', 'true'))),
                'user_level'       => trim($this->input->post('lstLevel', 'true')),
                'user_status'      => $this->input->post('lstStatus', 'true'),
                'user_date_update' => date('Y-m-d H:i:s'),
            );
        } else {
            $data = array(
                'user_name'        => strtoupper(stripHTMLtags($this->input->post('name', 'true'))),
                'user_email'       => trim(stripHTMLtags($this->input->post('email', 'true'))),
                'user_level'       => trim($this->input->post('lstLevel', 'true')),
                'user_status'      => $this->input->post('lstStatus', 'true'),
                'user_date_update' => date('Y-m-d H:i:s'),
            );
        }

        $this->db->where('user_username', $user_username);
        $this->db->update('resto_users', $data);
    }
}
/* Location: ./application/model/admin/Users_m.php */
