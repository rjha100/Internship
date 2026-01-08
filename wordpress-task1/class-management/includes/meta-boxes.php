<?php

/**
 * Meta Boxes for Class Post Type
 *
 * @package ClassManagement
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Generate schedule dates from start date to end date
 * Returns formatted string like "Tue, 6/15\nWed, 6/16\nThu, 6/17"
 *
 * @param string $start_date_str Start date (Y-m-d format)
 * @param string $end_date_str End date (Y-m-d format)
 * @return string Formatted schedule dates
 */
function cm_generate_schedule_dates($start_date_str, $end_date_str)
{
    $tz = wp_timezone();
    $start = new DateTime($start_date_str, $tz);
    $end = new DateTime($end_date_str, $tz);

    $diff = $start->diff($end);
    $days_between = $diff->days + 1; // +1 to include both start and end dates

    $num_dates = min($days_between, 5);

    $dates = array();
    for ($i = 0; $i < $num_dates; $i++) {
        $dates[] = $start->format('D, n/j');
        $start->modify('+1 day');
    }

    return implode("\n", $dates);
}

/**
 * Add Meta Boxes for Classes
 */
function cm_add_class_meta_boxes()
{
    add_meta_box(
        'cm_class_information',
        __('Class Information', 'class-management'),
        'cm_class_information_callback',
        'class',
        'normal',
        'high'
    );

    add_meta_box(
        'cm_class_extras',
        __('Similar Classes', 'class-management'),
        'cm_class_extras_callback',
        'class',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'cm_add_class_meta_boxes');

/**
 * Class Information Meta Box Callback
 */
function cm_class_information_callback($post)
{
    wp_nonce_field('cm_save_class_meta', 'cm_class_meta_nonce');

    $instructor = get_post_meta($post->ID, 'instructor', true);
    $start_date = get_post_meta($post->ID, 'start_date', true);
    $end_date = get_post_meta($post->ID, 'end_date', true);
    $start_time = get_post_meta($post->ID, 'start_time', true);
    $end_time = get_post_meta($post->ID, 'end_time', true);
    $location_address = get_post_meta($post->ID, 'location_address', true);
    $location_city = get_post_meta($post->ID, 'location_city', true);
    $location_state = get_post_meta($post->ID, 'location_state', true);
    $location_zip = get_post_meta($post->ID, 'location_zip', true);
    $primary_price = get_post_meta($post->ID, 'primary_price', true);
    $member_price = get_post_meta($post->ID, 'member_price', true);
    $registration_deadline = get_post_meta($post->ID, 'registration_deadline', true);

    $current_user = wp_get_current_user();
    $is_instructor = in_array('instructor', (array) $current_user->roles);
    $is_admin = current_user_can('manage_options');
    ?>
  <table class="form-table">
    <?php if ($is_instructor) : ?>
    <input type="hidden" name="instructor" value="<?php echo esc_attr($current_user->ID); ?>" />
    <?php endif; ?>
    <tr>
      <th><label for="start_date"><?php _e('Start Date', 'class-management'); ?> <span style="color:red;">*</span></label></th>
      <td>
        <input type="date" name="start_date" id="start_date" value="<?php echo esc_attr($start_date); ?>" 
               required style="width: 100%; max-width: 400px;" />
      </td>
    </tr>
    <tr>
      <th><label for="end_date"><?php _e('End Date', 'class-management'); ?> <span style="color:red;">*</span></label></th>
      <td>
        <input type="date" name="end_date" id="end_date" value="<?php echo esc_attr($end_date); ?>" 
               required style="width: 100%; max-width: 400px;" />
      </td>
    </tr>
    <tr>
      <th><label for="start_time"><?php _e('Start Time', 'class-management'); ?> <span style="color:red;">*</span></label></th>
      <td>
        <input type="time" name="start_time" id="start_time" value="<?php echo esc_attr($start_time); ?>" 
               required style="width: 100%; max-width: 200px;" />
        <p class="description"><?php _e('Time format: HH:MM (12-hour)', 'class-management'); ?></p>
      </td>
    </tr>
    <tr>
      <th><label for="end_time"><?php _e('End Time', 'class-management'); ?> <span style="color:red;">*</span></label></th>
      <td>
        <input type="time" name="end_time" id="end_time" value="<?php echo esc_attr($end_time); ?>" 
               required style="width: 100%; max-width: 200px;" />
        <p class="description"><?php _e('Time format: HH:MM (12-hour)', 'class-management'); ?></p>
      </td>
    </tr>
    <tr>
      <th><label for="location_address"><?php _e('Location Address', 'class-management'); ?> <span style="color:red;">*</span></label></th>
      <td>
        <input type="text" name="location_address" id="location_address" value="<?php echo esc_attr($location_address); ?>" 
               placeholder="e.g., 2042 Balboa St." required style="width: 100%; max-width: 400px;" />
      </td>
    </tr>
    <tr>
      <th><label for="location_city"><?php _e('Location City', 'class-management'); ?> <span style="color:red;">*</span></label></th>
      <td>
        <input type="text" name="location_city" id="location_city" value="<?php echo esc_attr($location_city); ?>" 
               placeholder="e.g., San Francisco" required style="width: 100%; max-width: 400px;" />
      </td>
    </tr>
    <tr>
      <th><label for="location_state"><?php _e('Location State', 'class-management'); ?></label></th>
      <td>
        <input type="text" name="location_state" id="location_state" value="<?php echo esc_attr($location_state); ?>" 
               placeholder="e.g., California" style="width: 100%; max-width: 300px;" />
        <p class="description"><?php _e('State or province (optional)', 'class-management'); ?></p>
      </td>
    </tr>
    <tr>
      <th><label for="location_zip"><?php _e('Location Zip Code', 'class-management'); ?></label></th>
      <td>
        <input type="text" name="location_zip" id="location_zip" value="<?php echo esc_attr($location_zip); ?>" 
               placeholder="e.g., 94121" style="width: 100%; max-width: 150px;" />
        <p class="description"><?php _e('Zip code (optional)', 'class-management'); ?></p>
      </td>
    </tr>
    <tr>
      <th><label for="primary_price"><?php _e('Non-Member Price', 'class-management'); ?> <span style="color:red;">*</span></label></th>
      <td>
        <span style="font-size: 16px; margin-right: 5px;">$</span>
        <input type="number" name="primary_price" id="primary_price" value="<?php echo esc_attr($primary_price); ?>" 
               step="0.01" min="0" required style="width: 150px;" />
      </td>
    </tr>
    <tr>

      <th><label for="member_price"><?php _e('Member Price', 'class-management'); ?> <span style="color:red;">*</span></label></th>
      <td>
        <span style="font-size: 16px; margin-right: 5px;">$</span>
        <input type="number" name="member_price" id="member_price" value="<?php echo esc_attr($member_price); ?>" 
               step="0.01" min="0" required style="width: 150px;" />
      </td>
    </tr>
    <tr>
      <th><label for="registration_deadline"><?php _e('Registration Deadline', 'class-management'); ?> <span style="color:red;">*</span></label></th>
      <td>
        <input type="datetime-local" name="registration_deadline" id="registration_deadline" 
               value="<?php echo esc_attr($registration_deadline ? date('Y-m-d\TH:i', strtotime($registration_deadline)) : ''); ?>" 
               required style="width: 100%; max-width: 400px;" />
        <p class="description"><?php _e('Class schedule dates will be auto-generated from start to end date.', 'class-management'); ?></p>
      </td>
    </tr>
  </table>
    <?php
}

/**
 * Class Extras Meta Box Callback
 */
function cm_class_extras_callback($post)
{
    $similar_classes = get_post_meta($post->ID, 'similar_classes', true);

    $classes = get_posts(array(
    'post_type' => 'class',
    'posts_per_page' => -1,
    'post__not_in' => array($post->ID),
    'orderby' => 'title',
    'order' => 'ASC'
    ));
    ?>
  <table class="form-table">
    <tr>
      <th><label for="similar_classes"><?php _e('Similar Classes', 'class-management'); ?></label></th>
      <td>
        <select name="similar_classes[]" id="similar_classes" multiple style="width: 100%; max-width: 400px; height: 150px;">
          <?php foreach ($classes as $class) : ?>
            <option value="<?php echo esc_attr($class->ID); ?>" <?php echo is_array($similar_classes) && in_array($class->ID, $similar_classes) ? 'selected' : ''; ?>>
                <?php echo esc_html($class->post_title); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <p class="description"><?php _e('Hold Ctrl (Cmd on Mac) to select multiple classes (max 3).', 'class-management'); ?></p>
      </td>
    </tr>
  </table>
    <?php
}

/**
 * Save Class Meta Box Data
 */
function cm_save_class_meta($post_id)
{
    if (!isset($_POST['cm_class_meta_nonce']) || !wp_verify_nonce($_POST['cm_class_meta_nonce'], 'cm_save_class_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $post_status = get_post_status($post_id);
    if ($post_status === 'publish' || (isset($_POST['post_status']) && $_POST['post_status'] === 'publish')) {
        $errors = array();

        $required_fields = array(
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'start_time' => 'Start Time',
        'end_time' => 'End Time',
        'location_address' => 'Location Address',
        'location_city' => 'Location City',
        'primary_price' => 'Primary Price',
        'member_price' => 'Member Price',
        'registration_deadline' => 'Registration Deadline'
        );

        foreach ($required_fields as $field => $label) {
            if (empty($_POST[$field])) {
                $errors[] = $label . ' is required';
            }
        }

        if (!empty($errors)) {
            remove_action('save_post_class', 'cm_save_class_meta');

            wp_update_post(array(
            'ID' => $post_id,
            'post_status' => 'draft'
            ));

            add_action('save_post_class', 'cm_save_class_meta');

            set_transient('cm_validation_errors_' . $post_id, $errors, 45);

            return;
        }
    }

    $current_user = wp_get_current_user();
    $is_instructor = in_array('instructor', (array) $current_user->roles);

    if ($is_instructor) {
        update_post_meta($post_id, 'instructor', $current_user->ID);
    }

    $fields = array(
    'start_date',
    'end_date',
    'start_time',
    'end_time',
    'location_address',
    'location_city',
    'location_state',
    'location_zip',
    'primary_price',
    'member_price',
    'registration_deadline'
    );

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            if ($field === 'registration_deadline' && !empty($_POST[$field])) {
                $datetime = str_replace('T', ' ', sanitize_text_field($_POST[$field]));
                update_post_meta($post_id, $field, $datetime . ':00');
            } else {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
    }

    if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
        $schedule_dates = cm_generate_schedule_dates(
            sanitize_text_field($_POST['start_date']),
            sanitize_text_field($_POST['end_date'])
        );
        update_post_meta($post_id, 'class_schedule_dates', $schedule_dates);
    }

    if (isset($_POST['similar_classes']) && is_array($_POST['similar_classes'])) {
        $similar_classes = array_slice(array_map('intval', $_POST['similar_classes']), 0, 3);
        update_post_meta($post_id, 'similar_classes', $similar_classes);
    } else {
        delete_post_meta($post_id, 'similar_classes');
    }
}
add_action('save_post_class', 'cm_save_class_meta');

/**
 * Display validation error messages
 */
function cm_display_validation_errors()
{
    global $post;

    if (!$post || $post->post_type !== 'class') {
        return;
    }

    $errors = get_transient('cm_validation_errors_' . $post->ID);

    if ($errors) {
        delete_transient('cm_validation_errors_' . $post->ID);
        ?>
    <div class="notice notice-error is-dismissible">
      <p><strong><?php _e('Cannot publish class. Please fix the following errors:', 'class-management'); ?></strong></p>
      <ul style="list-style: disc; margin-left: 20px;">
        <?php foreach ($errors as $error) : ?>
          <li><?php echo esc_html($error); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
        <?php
    }
}
add_action('admin_notices', 'cm_display_validation_errors');

