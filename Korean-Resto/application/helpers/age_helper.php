<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

function ageCalculator($dob)
{
    if (!empty($dob)) {
        $birthdate = new DateTime($dob);
        $today     = new DateTime('today');
        $age       = $birthdate->diff($today)->y;
        return $age;
    } else {
        return 0;
    }
}
if ( ! function_exists('order_detail')) {
    function order_detail($id) {
        $CI = &get_instance();
        return $CI->db->select('x1.menu_nama,x.order_detail_harga,x.order_detail_qty,x.order_detail_subtotal')
        ->join('resto_menu x1', 'x.menu_id = x1.menu_id', 'left')
        ->where(['x.order_id'=>$id])
        ->get('resto_order_detail x')->result();
    }
}

/* End of file welcome.php */
/* Location: ./application/helpers/age_helper.php */
