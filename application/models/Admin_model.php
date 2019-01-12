<?php

/**
 * Description of Admin_model
 *
 * @author Nguyen Tat Huy
 * @phone 0988656070
 * @email nguyentathuy1986@gmail.com
 * @skype tim_nguoi_xa_la
 */
class Admin_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /*
     * Get array config
     */

    function get_config() {
        $rs = $this->db->select('name, value')->get('sys_config')->result();
        $rt = array();
        foreach ($rs as $item) {
            $rt[$item->name] = $item->value;
        }
        return $rt;
    }

    /*
     * Get array customer
     */

    function get_customer() {
        $query = $this->db->select('id, name')->where('is_delete', 0)->get('customers');
        $arr = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $item) {
                $arr[$item->id] = $item->name;
            }
        }
        return $arr;
    }

    /*
     * Get array group
     */

    function get_groups($customer = '') {
        if (empty($id)) {
            $query = $this->db->select('id, name')->where('is_delete', 0)->get('sys_groups');
        } else {
            $query = $this->db->select('id, name')->where('is_delete', 0)->where('customer', $customer)->get('sys_groups');
        }
        $arr = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $item) {
                $arr[$item->id] = $item->name;
            }
        }
        return $arr;
    }

    /*
     * Get array user status
     */

    function get_user_status() {
        $arr = array("1" => "active", "2" => "inactive", "3" => "expired");
        return $arr;
    }

    /*
     * Get permission by group
     * @param string $group_id user group login
     */

    function get_permission($group_id) {
        $rs = $this->db->where('id', $group_id)->where('is_delete', 0)->get('sys_groups')->result();
        $rt = array();
        if (isset($rs[0])) {
            $arr_params = json_decode($rs[0]->params, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $rt = array_keys($arr_params);
            }
        }
        return $rt;
    }

    /*
     * Get menu tree format
     * Tree menu not grant permissions
     */

    function tree_menu_pure() {
        $nodeList = array();
        $tree = array();
        $rs = $this->db->order_by('parent')->get('sys_menus')->result_array();
        foreach ($rs as $row) {
            $nodeList[$row['id']] = array_merge($row, array('children' => array()));
        }
        foreach ($nodeList as $nodeId => &$node) {
            if (!$node['parent'] || !array_key_exists($node['parent'], $nodeList)) {
                $tree[] = &$node;
            } else {
                $nodeList[$node['parent']]['children'][] = &$node;
            }
        }
        unset($node);
        unset($nodeList);
        return $tree;
    }

    /*
     * Tree menu have grant permissions
     * @param array &$tree
     * @param array $permission
     * @param array &$parent parent of element current
     * @param array &$path path to element
     * @param array &$route_path path by route format
     */

    function tree_menu_route_permission(&$tree, $permission, &$parent, &$path, &$route_path) {
        $status = 0;
        $rt = 0;
        foreach ($tree as &$item) {
            if ($status == 1) {
                $rt = 1;
            }
            if ($item['parent'] == 0) {
                $path = '';
            }
            if (!empty($item['children'])) {
                $path .= ',' . $item['id'];
                $status = $this->tree_menu_route_permission($item['children'], $permission, $item, $path, $route_path);
                $item['status'] = $status;
            } else {
                if (in_array($item['id'], $permission)) {
                    $item['status'] = 1;
                    $parent['status'] = 1;
                    $status = 1;
                } else {
                    $item['status'] = 0;
                    $parent['status'] = 0;
                }
                $path .= ',' . $item['id'];
                $route_path[$item['route']] = $path . ',';
                $temp = explode(",", $path);
                array_pop($temp);
                $path = implode(",", $temp);
            }
        }
        return ($rt == 1) ? 1 : $status;
    }

    /*
     * Full tree menu
     * @param array $tree tree menu have permission
     * @param string $cm menu active
     * @param string $rm route menu path
     * @param array &$html result html return
     */

    function create_menu_html($tree, $cm, $rm, &$html) {
        $ce = count($tree) - 1;
        $i = 0;
        $classicon = 'fa fa-folder-o';
        $active = '';
        $style = 'style="display: none;"';
        foreach ($tree as $item) {
            if ($item['status'] == 1) { // This element have permission
                if (empty($item['classicon'])) {
                    $classicon = 'fa fa-folder-o';
                } else {
                    $classicon = $item['classicon'];
                }
                if (!empty($item['children'])) {
                    if (strpos($rm, "," . $item['id'] . ",") !== FALSE) {
                        $active = ' active';
                        $style = '';
                    } else {
                        $active = '';
                        $style = 'style="display: none;"';
                    }
                    $html .= '<li class="treeview ' . $active . '">
                    <a href="#">
                    <span class = "ico ' . $classicon . '">&nbsp;</span> <span class="tit">' . $this->lang->line($item['route']) . '</span>
                    <span class = "arr pull-right"></span>
                    </a>
                    <ul class = "treeview-menu">';
                    $this->create_menu_html($item['children'], $cm, $rm, $html);
                } else {
                    if ($item['route'] == $cm) {
                        $active = ' active';
                    } else {
                        $active = '';
                    }
                    $html .= '<li class = "' . $active . '">
                    <a href = "' . admin_url($this->admin->lang . '/' . $item['route']) . '">
                    <span class = "ico ' . $classicon . '"></span> <span class="tit">' . $this->lang->line($item['route']) . '</span>
                    <!--<small class = "label pull-right bg-yellow">12</small>-->
                    </a>
                    </li>';
                }
            }
            if ($i == $ce) { // close ul li when element end
                $html .= '</ul></li>';
            }
            $i++;
        }
    }

    /*
     * Config pagination
     * @param array $url link to load
     * @param int $total_row sum row
     * @param int $limit limit record
     * @param int $cpage current page
     */

    public function config_pagination($url, $total_row, $limit, $cpage) {
        $config = array();
        $config["base_url"] = $url;
        $config["total_rows"] = $total_row;
        $config["per_page"] = $limit;
        $config["cur_page"] = $cpage; // using for ajax
        $config['use_page_numbers'] = TRUE;
        $config['num_links'] = 3;
        $config['cur_tag_open'] = '<a href="#" class="current">';
        $config['cur_tag_close'] = '</a>';
        $config['next_link'] = 'Next';
        $config['prev_link'] = 'Previous';
        $config['first_link'] = 'First';
        $config['last_link'] = 'Last';
        $config['page_query_string'] = FALSE; // using for ajax
        $config['query_string_segment'] = 'page';
        return $config;
    }

    /*
     * Delete record
     * @param string $table table to delete
     */

    function delete($table, $arr_id) {
        $this->db->trans_start();
        if ($table == 'sys_users' || $table == 'sys_groups' || $table == 'sys_menus') {
            $this->db->where_in("id", $arr_id)->update($table, array("is_delete" => 1));
        } else {
            $this->db->where_in("id", $arr_id)->update($table, array("isdelete" => 1));
        }
        $this->db->trans_complete();
        return $this->db->trans_status();
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

    public function country() {
        $country = $this->db->where('is_delete', 0)->get('country')->result();
        return $country;
    }

    function user_arr_id_name() {
        $rs = $this->db->select("id, fullname")->where('is_delete', 0)->get('sys_users')->result();
        $arr = array();
        foreach ($rs as $item) {
            $arr[$item->id] = $item->fullname;
        }
        return $arr;
    }

    function inverse_column($table, $column, $id) {
        $sql = "UPDATE " . $table . " SET " . $column . " = " . $column . " XOR 1 WHERE id = " . $id;
        $this->db->query($sql);
        $val = $this->db->select($column)->where('id', $id)->limit(1)->get($table)->row()->{$column};
        return $val;
    }

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

    function sub_menu($menu_route) {
        $sql = "SELECT m.id, m.name FROM menus m WHERE m.isdelete = 0 AND m.parent = (SELECT mp.id FROM menus mp WHERE mp.route = '$menu_route' AND mp.isdelete=0)";
        $rows = $this->db->query($sql)->result();
        $rs = array();
        foreach ($rows as $row) {
            $rs[$row->id] = $row->name;
        }
        return $rs;
    }

}
