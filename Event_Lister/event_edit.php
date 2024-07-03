<?php
$item_id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

global $wpdb;
$table_name = $wpdb->prefix . 'event_lister';

$item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $item_id), ARRAY_A);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    
    $event_title = sanitize_text_field($_POST['event_title']);
    $event_date = sanitize_text_field($_POST['event_date']);
    $event_description = stripslashes(sanitize_textarea_field($_POST['event_description']));

    $data = array(
        'event_title' => $event_title,
        'event_description' => $event_description,
        'event_date' => $event_date,
    );

    $where = array('id' => $item_id);

    $data_format = array('%s', '%s', '%s');

    $where_format = array('%d');

    $result = $wpdb->update($table_name, $data, $where, $data_format, $where_format);
    if ($result !== false && $wpdb->rows_affected > 0) {
        echo '<script>window.location.href="' . admin_url('admin.php?page=events-list') . '";</script>';
        exit();
    } else {
        echo "Update failed or no rows were updated. Please try again.";
    }
}
?>



<div class="wrap">
    <h1>Edit Event</h1>
    <form method="post" action="">
        <table class="form-table">
            <tr>
                <th scope="row">Event Title</th>
                <td><input type="text" name="event_title" value="<?php echo esc_attr($item['event_title']); ?>"></td>
            </tr>
            <tr>
                <th scope="row">Event Date</th>
                <td><input type="date" name="event_date" value="<?php echo esc_attr($item['event_date']); ?>"></td>
            </tr>
            <tr>
                <th scope="row">Event Description</th>
                <td><textarea name="event_description"><?php echo esc_textarea($item['event_description']); ?></textarea></td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Update Event">
        </p>
    </form>
</div>
