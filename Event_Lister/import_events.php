<?php
if (isset($_POST['submit_csv'])) {
    if (isset($_FILES['csv_file'])) {
        $file = wp_handle_upload($_FILES['csv_file'], array('test_form' => false));

        if ($file && !isset($file['error'])) {
            $csv_data = array_map('str_getcsv', file($file['file']));

            if (!empty($csv_data)) {
                global $wpdb;
                $table_name = $wpdb->prefix . 'event_lister'; 

                for ($i = 1; $i < count($csv_data); $i++) {
                    $data = $csv_data[$i];
                    $event_title = sanitize_text_field($data[0]);
                    $event_date = date('Y-m-d', strtotime($data[1])); 
                    $event_description = sanitize_text_field($data[2]);

                    $event_description = stripslashes($event_description);
    
                    $wpdb->insert(
                        $table_name,
                        array(
                            'event_title' => $event_title,
                            'event_date' => $event_date,
                            'event_description' => $event_description,
                        )
                    );
                }
    
                echo '<div class="updated"><p>' . esc_html('Data inserted successfully!') . '</p></div>';
            }
        } else {
            echo '<div class="error"><p>' . esc_html('File upload error: ' . $file['error']) . '</p></div>';
        }
    }
}



?>

<div class="wrap" style="background-color: #f7f7f7; border: 1px solid #ddd; padding: 20px; border-radius: 5px; box-shadow: 0 0 5px rgba(0, 0, 0, 0.2); text-align: center;">
    <h2 style="font-size: 24px; margin-bottom: 20px;">Import CSV File</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="csv_file" id="csv_file" accept=".csv" style="display: block; margin: 10px auto; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
        <input type="submit" name="submit_csv" value="Import" style="display: block; margin: 20px auto 0; padding: 10px 20px; background-color: #0073e6; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
    </form>
</div>
