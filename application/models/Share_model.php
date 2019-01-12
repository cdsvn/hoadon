<?php

/**
 * Description of Admin_model
 *
 * @author Nguyen Tat Huy
 * @phone 0988656070
 * @email nguyentathuy1986@gmail.com
 * @skype tim_nguoi_xa_la
 */
class Share_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    function row_default($table) {
        $cols = $this->db->query("SHOW COLUMNS FROM $table")->result_array();
        $obj = new stdClass();
        foreach ($cols as $col) {
            $obj->{$col['Field']} = $col['Default'];
        }
        return $obj;
    }

    public function get_arr_cat_id_name() {
        $rs = $this->db->where('is_delete', 0)->get('product_categories')->result();
        $arr = array();
        foreach ($rs as $item) {
            $arr[$item->id] = $item->name;
        }
        return $arr;
    }

    public function get_category($other = "", $parent_id = 0, $insert_text = "", $level = 0) {
        $this->db->where('parent', $parent_id);
        $this->db->where('is_delete', 0);
        if ($other != "") {
            $this->db->where('id <>', $other);
        }
        $this->db->order_by('order', "ASC");
        $cat = $this->db->get('product_categories');
        static $menu = array();
        $temp_parent = 0;
        foreach ($cat->result_array() as $item) {
            if ($item["parent"] == 0) {
                $level = 0;
            }
            if ($temp_parent != $item["parent"]) {
                ++$level;
            }
            $temp_parent = $item["parent"];
            $temp = "";
            for ($i = 0; $i < $level; $i++) {
                $temp .= $insert_text;
            }
            $menu[] = array(
                'id' => $item["id"],
                'name' => $temp . $item["name"],
                'link' => $item["link"],
                'parent' => $item["parent"],
                'group' => $item["group"],
                'status' => $item["status"],
                'active' => $item["active"],
                'order' => $item["order"],
                'level' => $level
            );
            $this->get_category($other, $item["id"], $insert_text, $level);
        }
        return $menu;
    }

    function user_arr_id_name() {
        $rs = $this->db->select("id, fullname")->where('is_delete', 0)->get('users')->result();
        $arr = array();
        foreach ($rs as $item) {
            $arr[$item->id] = $item->fullname;
        }
        return $arr;
    }

    /*
     * Inverse value in column
     */

    function inverse_column($table, $column, $id) {
        $sql = "UPDATE " . $table . " SET " . $column . " = " . $column . " XOR 1 WHERE id = " . $id;
        $this->db->query($sql);
        return "success";
    }

    /*
     * Set order of colum to sort
     */

    function set_order($table, $column, $order_array) {
        if (count($order_array) > 0) {
            $this->db->trans_start();
            foreach ($order_array as $id => $order) {
                $sql = "UPDATE " . $table . " as cp SET cp." . $column . " = '" . $order . "' WHERE cp.id = " . $id . ";";
                $this->db->query($sql);
            }
            $this->db->trans_complete();
        }
        return array("status" => "success", "reason" => "pass");
    }

    /*
     * Get colum from categories table
     * @param string $col the string column name in table product_categories
     * @return array result
     */

    function categories($col = "") {
        if (!empty($col)) {
            $this->db->select($col);
        }
        $this->db->where('is_delete', 0);
        $this->db->order_by('order', "ASC");
        $data = $this->db->get('product_categories')->result_array();
        return $data;
    }

    /*
     * Get row with tree format
     * @param array $arrData array categories
     * @param int parent parent of record
     * @param int level level of record
     * @param string space the string show before name
     * @param array $result array result
     * @return array result
     */

    function recursive_categories($arrData = array(), $parent = 0, $level = 0, $space = "", &$result = null) {
        if (empty($arrData)) {
            $arrData = $this->categories();
        }
        if (count($arrData) > 0) {
            $temp = "";
            for ($i = 0; $i < $level; $i++) {
                $temp .= $space;
            }
            foreach ($arrData as $key => $val) {
                if ($parent == $val['parent']) {
                    $val['level'] = $level;
                    $val['sname'] = $temp . $val['name'];
                    $result[] = $val;
                    $_parent = $val['id'];
                    unset($arrData[$key]);
                    $this->recursive_categories($arrData, $_parent, $level + 1, $space, $result);
                }
            }
        }
    }

    /*
     * List color
     */

    function syscolor($color = "") {
        $colors = array();
        $colors["FFBF00"] = "Hổ phách";
        $colors["FFBF00"] = "Hổ phách";
        $colors["007FFF"] = "Xanh da trời";
        $colors["F0DC82"] = "Da bò";
        $colors["ACE1AF"] = "Men ngọc";
        $colors["FF7F50"] = "San hô";
        $colors["DC143C"] = "Đỏ thắm";
        $colors["00FF00"] = "Xanh lá cây";
        $colors["FFFF00"] = "Vàng";
        $colors["FFFFFF"] = "Trắng";
        $colors["BF00BF"] = "Tím";
        $colors["C0C0C0"] = "Bạc";
        $colors["FF0000"] = "Đỏ";
        $colors["FF0000"] = "Xám";
        if (!empty($color)) {
            return $colors[$color];
        }
        return $colors;
    }

    function country($isarr = false) {
        $rs = $this->db->select('id, name')->where('isdelete', 0)->get('public_country')->result();
        if ($isarr) {
            $arr = array();
            foreach ($rs as $item) {
                $arr[$item->id] = $item->name;
            }
            return $arr;
        }
        return $rs;
    }

}
