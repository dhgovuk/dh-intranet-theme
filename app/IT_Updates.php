<?php

namespace DHIntranet;

class IT_Updates
{
    /**
     * Get all the current IT Updates available.
     */
    public static function get_updates()
    {
        // Get Updates
        $updates_list = null;

        global $wpdb;

        $table_name = $wpdb->prefix.'it_updates';

        $raw_data = $wpdb->get_results("SELECT * FROM $table_name");

        // If we have DB records, use them.
        // If not, add some dummy data.
        if (!empty($raw_data)) {
            foreach ($raw_data as $data_piece) {
                if ($data_piece->data_key == 'overall_status') {
                    $status_data['overall_status'] = stripslashes_deep(json_decode($data_piece->data_value, true));
                } elseif ($data_piece->data_key == 'status') {
                    $status_data['statuses'][] = stripslashes_deep(json_decode($data_piece->data_value, true));
                }
            }
        } else {
            $status_data = array(
                'overall_status' => array('message' => 'Intranet is in good health', 'status' => 'green'),
                'statuses' => array(
                    array('system_name' => 'IT System 1', 'status' => 'amber'),
                    array('system_name' => 'IT System 2', 'status' => 'green'),
                    array('system_name' => 'IT System 3', 'status' => 'green'),
                ),
            );
        }

        $overall_status = $status_data['overall_status'];
        $statuses = $status_data['statuses'];

        return array('overall_status' => $overall_status, 'statuses' => $statuses);
    }

    // Each time updates are saved, we wipe all the date in the table
    // and repopulate it with new data from the freshly submitted form.
    public static function save_updates($data = '')
    {
        if (!$data) {
            return false;
        }

        global $wpdb;

        $overall_status = $data['overall'];
        $statuses = $data['statuses'];

        $table_name = $wpdb->prefix.'it_updates';

        // Delete all current records from database.
        $wpdb->query("
        TRUNCATE TABLE $table_name
        ");

        // Insert overall status.
        $data = json_encode($overall_status);
        $wpdb->insert($table_name, array(
            'data_key' => 'overall_status',
            'data_value' => $data,
        ));

        // Insert individual statuses.
        foreach ($statuses as $status) {
            $data = json_encode($status);

            $wpdb->insert($table_name, array(
                'data_key' => 'status',
                'data_value' => $data,
            ));
        }
    }

    public static function display_all_statuses()
    {
        global $wpdb;

        $output = '';

        $table_name = $wpdb->prefix.'it_updates';

        $all_statuses = $wpdb->get_results("
        SELECT *
        FROM $table_name
        ");

        if (!empty($all_statuses)) {
            foreach ($all_statuses as $data_piece) {
                if ($data_piece->data_key == 'overall_status') {
                    $status_data['overall_status'] = stripslashes_deep(json_decode($data_piece->data_value, true));
                } elseif ($data_piece->data_key == 'status') {
                    $status_data['statuses'][] = stripslashes_deep(json_decode($data_piece->data_value, true));
                }
            }
        } else {
            return false;
        }

        $output = '<table class="it-status-table">';
        $output .= '<tbody>';
        $output .= '<tr>';
        $output .= '<th>Our Environment</th>';
        $output .= '<th>Status</th>';
        $output .= '</tr>';

        if (isset($status_data['statuses']) && is_array($status_data['statuses'])) {
            foreach ($status_data['statuses'] as $status) {
                $output .= '<tr>';
                $output .= "<td>$status[system_name]</td>";
                $output .= "<td class='it-status-table-light'>
                <img class=\"status_circle\" src=\"".esc_url(\h()->getAssetPath("img/status-light-$status[status].png"))."\">
                </td>";
                $output .= '</tr>';
            }
        }

        $output .= '<tr>';
        $output .= '<td>Overall Status</td>';
        $output .= "<td class='it-status-table-light'>
        <img class=\"status-circle\" src=\"".esc_url(\h()->getAssetPath("img/status-light-{$status_data['overall_status']['status']}.png"))."\"></td>";
        $output .= '</tr>';

        $output .= '</tbody>';
        $output .= '</table>';

        // Convert new lines into <br>.
        $status_data['overall_status']['message'] = nl2br($status_data['overall_status']['message']);

        $output .= '<p>'.$status_data['overall_status']['message'].'</p>';

        return $output;
    }

    public static function display_overall_status()
    {
        global $wpdb;

        $table_name = $wpdb->prefix.'it_updates';

        $overall_status = $wpdb->get_results("
        SELECT *
        FROM $table_name
        WHERE data_key = 'overall_status'
        ");

        $decoded_status = stripslashes_deep(json_decode($overall_status[0]->data_value, true));

        if (!is_null($decoded_status)) {
            echo '<div class="it-updates-widget status-'.esc_attr($decoded_status['status']).'">';
            echo '<a href="/it-updates">Workplace status <span>'.esc_attr($decoded_status['status']).'</span></a>';
            echo '</div>';
        }
    }
}
