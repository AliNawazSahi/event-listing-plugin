<?php
/*
	Plugin Name: Events Lister
	Description: This Plugin is for listing of different Events.
	Version: 0.1
	Author: Ali Nawaz Sahi
*/


function add_event_lister_menu_fun() {
    $capability = 'read';     
    add_menu_page('Import Events', 'Import Events', $capability, 'import-events-menu', 'import_events_fun');
    add_submenu_page('import-events-menu', 'Events List', 'Events List', $capability, 'events-list', 'events_list_fun');
}

add_action('admin_menu', 'add_event_lister_menu_fun');

function import_events_fun() {
    include_once plugin_dir_path(__FILE__) . 'import_events.php';
    $template = ob_get_contents();
    ob_end_clean();
    echo $template;
}

function events_list_fun() {
    $action = isset($_GET["action"]) ? trim($_GET["action"]) : "";
    if ($action == "edit") {
        $item_id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
        ob_start();
        include_once plugin_dir_path(__FILE__) . 'event_edit.php';
        $template = ob_get_contents();
        ob_end_clean();
        echo $template;
    }elseif ($action == "delete") {
            if (isset($_GET['id'])) {
                $id = intval($_GET['id']);
                global $wpdb;
                $table_name = $wpdb->prefix . 'event_lister';
                $wpdb->delete($table_name, array('id' => $id));
                echo '<script>window.location.href="' . admin_url('admin.php?page=events-list') . '";</script>';
                exit();
        }
    } else {
            include_once plugin_dir_path(__FILE__) . 'events_listing.php';
            $template = ob_get_contents();
            ob_end_clean();
            echo $template;
   }

}


function event_lister_table_shortcode() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'event_lister';

    $query    = "SELECT * FROM $table_name ORDER BY event_date ASC";
    $results  = $wpdb->get_results($query, ARRAY_A);
    $output   = '';

    if (empty($results)) {
        $output .= '<p style="text-align: center; font-size: 18px; color: #555;">No events found.</p>';
    } else {
        $grouped_events = [];

        // Group events by year
        foreach ($results as $event) {
            $year = date('Y', strtotime($event['event_date']));
            $grouped_events[$year][] = $event;
        }

        // Styles for the container div
        $output .= '<div  style="display: flex; width:full-vw;">';

      
        // Styles for the 25% width div with a parallelogram shape
        $output .= '<div style="width: 25%; height: 550px;display:flex;justify-content: center;align-items: center; margin-right: 20px; position: relative; overflow: hidden;">
        <div style="width: 100%; height: 100%; background-color:  #2661bac2; clip-path: polygon(0% 0%, 105% -2%, 104% 45%, 0% 100%); position: absolute; z-index: 1;"></div>
        <div style="position: absolute; z-index: 2; color: black; text-align: center; padding: 20px;">
            <strong><p style=" font-family:Fantasy; font-weight: 800;font-size: 50px;">EVENTS <br> CALENDAR</p></strong>
        </div>
        </div>';




        // Styles for the 75% width div containing events
        $output .= '<div class="event-list" style="width: 75%; font-family: \'Roboto\', sans-serif;">';

        foreach ($grouped_events as $year => $events) {
            $output .= '<div class="event-date">';
            $output .= '<h1 style="margin-bottom: 10px; font-size: 32px; font-family: \'Helvetica Neue\', sans-serif; text-align: center;"><strong>' . esc_html($year) . '</strong></h1> <hr style="margin-bottom: 40px;">';

            foreach ($events as $event) {
                $output .= '<div class="event-item" style="display:flex;">';
                $formattedDate_day = date('j', strtotime($event['event_date']));
                $formattedDate_month = date('M', strtotime($event['event_date']));

                $output .= '<div style="align-items: center;width: 15%; background-color:black; color:white;border-radius: 10px 0px 0px 10px;display:flex;flex-direction:column;justify-content:center; padding: 30px 0px; "><h1 style="font-size: 3rem;">' . esc_html($formattedDate_day) . '</h1><h5>' . esc_html($formattedDate_month) . '</h5></div>';

                $output .= '<div style="width: 85%; padding: 20px; color:#2661bac2; font-size:20px">';
                $output .= '<h1 style=" margin-bottom: 5px; font-family: \'Montserrat\', sans-serif;"><strong style="font-size: 30px; ">' . esc_html($event['event_title']) . '</strong></h1>';
                $output .= '<p style="color: #666; font-size: 18px; font-family: \'Open Sans\', sans-serif;">' . stripslashes(esc_html($event['event_description'])) . '</p>';
                $output .= '</div>';
                $output .= '</div>';
            }

            $output .= '</div>';
        }

        $output .= '</div>'; 

        $output .= '</div>'; 

        $output .= '<style>';
        $output .= '.event-date {margin-bottom: 20px; padding-bottom: 20px; }';
        $output .= '.event-item { background-color: #fff; border-radius: 10px; margin-bottom: 50px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); transition: transform 0.3s ease-in-out; }';
        $output .= '.event-item:hover { transform: translateY(-5px); }';
        $output .= '</style>';
    }

    return $output;
}

add_shortcode('event_lister_table', 'event_lister_table_shortcode');

?>