<?php
require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');

class Events_WP_Table extends WP_List_Table {

    public function prepare_items() {
        global $wpdb; 
        $table_name = $wpdb->prefix . 'event_lister';
    
        $columns = $this->get_columns();
        $sortable = $this->get_sortable_columns();
    
        $search_term = isset($_POST['s']) ? trim($_POST['s']) : "";
    
        $orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'event_date';
        $order = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'asc';
    
        $query = "SELECT * FROM $table_name";
        if (!empty($search_term)) {
            $query .= $wpdb->prepare(" WHERE event_title LIKE '%%%s%%'", $search_term); 
        }
    
        if (array_key_exists($orderby, $sortable)) {
            $query .= " ORDER BY $orderby $order";
        } else {
            $query .= " ORDER BY event_date asc";
        }
    
        $data = $wpdb->get_results($query, ARRAY_A);
    
        $this->_column_headers = array($columns, array(), $sortable);
        
        $per_page = 5;
        $current_page = $this->get_pagenum();
        $total_items = count($data);
        
        $this->items = array_slice($data , (($current_page - 1) * $per_page),$per_page);
        
        $this->set_pagination_args(array(
            "total_items" => $total_items,
            "per_page" => $per_page
        ));
    }
    

    public function get_columns() {
        $columns = array(
            "event_title" => "Event Title",
            "event_date" => "Event Date",
            "event_description" => "Event Description",
            "actions" => "Actions" 
        );
        return $columns;
    }

    public function get_sortable_columns() {
        $sortable = array(
            "event_date" => array("event_date", false) 
        );
        return $sortable;  
    }
                     
   public function column_default($item, $column_name) {
    switch ($column_name) {
        case 'event_title':
        case 'event_date':
            return $item[$column_name];

        case 'event_description':
            return stripslashes($item[$column_name]);

        case 'actions':
            $edit_link = esc_url(admin_url("admin.php?page=events-list&action=edit&id=" . $item['id']));
            $delete_link = esc_url(admin_url("admin.php?page=events-list&action=delete&id=" . $item['id']));

            return "<a href='$edit_link' class='button button-primary'>Edit</a>
                    <a href='$delete_link' class='button button-secondary'>Delete</a>";

        default:
            return "no value";
    }
}

}

function Events_list_table_layout() {
    
    echo '<div class="wrap"><h1>Events Details</h1>';
    $Events_list_table = new Events_WP_Table();
    $Events_list_table->prepare_items();
    echo "<form method='post' name='frm_search_box'  action='" . $_SERVER['PHP_SELF'] . "?page=events-list'>";
    $Events_list_table->search_box("Search Events", "search_box_id");
    echo "</form>";
    $Events_list_table->display();
    echo '</div>';
}

Events_list_table_layout();


?>
