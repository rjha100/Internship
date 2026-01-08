<?php

/**
 * Instructor Profile Fields
 *
 * @package ClassManagement
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue scripts for instructor profile fields
 */
function cm_enqueue_instructor_profile_scripts($hook)
{
    if ($hook !== 'profile.php' && $hook !== 'user-edit.php' && strpos($hook, 'instructor-profile') === false) {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_script('cm-instructor-profile', plugin_dir_url(dirname(__FILE__)) . 'js/instructor-profile.js', array('jquery'), '1.0', true);
}
add_action('admin_enqueue_scripts', 'cm_enqueue_instructor_profile_scripts');

/**
 * Add Instructor menu to admin
 */
function cm_add_instructor_admin_menu()
{
    add_menu_page(
        __('Instructor Profile', 'class-management'),
        __('Instructor', 'class-management'),
        'read', // All logged-in users (including instructors) have read capability
        'instructor-profile',
        'cm_render_instructor_profile_page',
        'dashicons-id',
        6
    );
}
add_action('admin_menu', 'cm_add_instructor_admin_menu');

/**
 * Render Instructor Profile admin page
 */
function cm_render_instructor_profile_page()
{
    $current_user = wp_get_current_user();
    $is_admin = current_user_can('manage_options');
    $is_instructor = in_array('instructor', (array) $current_user->roles);

    if (!$is_admin && !$is_instructor) {
        wp_die(__('You do not have permission to access this page.', 'class-management'));
    }

    $user_id = null;
    if ($is_admin && isset($_GET['user_id'])) {
        $user_id = absint($_GET['user_id']);
    } elseif (!$is_admin) {
        $user_id = $current_user->ID;
    }

    $user = $user_id ? get_userdata($user_id) : null;

    if ($user_id && isset($_POST['cm_save_instructor_profile']) && check_admin_referer('cm_instructor_profile_' . $user_id, 'cm_instructor_profile_nonce')) {
        cm_save_instructor_profile_data($user_id);
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Profile updated successfully.', 'class-management') . '</p></div>';

        $user = get_userdata($user_id);
    }

    $degree = $user_id ? get_user_meta($user_id, 'instructor_degree', true) : '';
    $bio = $user_id ? get_user_meta($user_id, 'instructor_bio', true) : '';
    $testimonial_text = $user_id ? get_user_meta($user_id, 'instructor_testimonial_text', true) : '';
    $testimonial_author = $user_id ? get_user_meta($user_id, 'instructor_testimonial_author', true) : '';
    $photo = $user_id ? get_user_meta($user_id, 'instructor_photo', true) : '';

    ?>
  <div class="wrap">
    <h1><?php _e('Instructor Profile', 'class-management'); ?></h1>
    
    <?php if ($is_admin) : ?>
      <h2><?php _e('Select Instructor', 'class-management'); ?></h2>
      <form method="get" action="">
        <input type="hidden" name="page" value="instructor-profile" />
        <select name="user_id" onchange="this.form.submit()">
          <option value=""><?php _e('-- Select an Instructor --', 'class-management'); ?></option>
          <?php
            $instructors = get_users(array(
            'role' => 'instructor',
            'orderby' => 'display_name',
            'order' => 'ASC'
            ));
          foreach ($instructors as $instructor) {
              $selected = ($user_id && $instructor->ID == $user_id) ? 'selected' : '';
              echo '<option value="' . esc_attr($instructor->ID) . '" ' . $selected . '>' . esc_html($instructor->display_name) . '</option>';
          }
            ?>
        </select>
      </form>
      <hr />
    <?php endif; ?>
    
    <?php if ($user_id && $user) : ?>
    <h2><?php echo sprintf(__('Editing Profile: %s', 'class-management'), esc_html($user->display_name)); ?></h2>
    
    <form method="post" action="">
        <?php wp_nonce_field('cm_instructor_profile_' . $user_id, 'cm_instructor_profile_nonce'); ?>
      
      <table class="form-table">
        <tr>
          <th><label for="instructor_degree"><?php _e('Degree', 'class-management'); ?></label></th>
          <td>
            <input type="text" name="instructor_degree" id="instructor_degree" value="<?php echo esc_attr($degree); ?>" 
                   class="regular-text" placeholder="e.g., MFA in Creative Writing" />
            <p class="description"><?php _e('Your academic degree or credentials.', 'class-management'); ?></p>
          </td>
        </tr>
        <tr>
          <th><label for="instructor_bio"><?php _e('Bio', 'class-management'); ?> <span style="color:red;">*</span></label></th>
          <td>
            <?php
            wp_editor($bio, 'instructor_bio', array(
              'textarea_name' => 'instructor_bio',
              'media_buttons' => false,
              'textarea_rows' => 8,
              'teeny' => true,
              'quicktags' => false
            ));
            ?>
            <p class="description"><?php _e('Your professional biography. This will be displayed on class pages.', 'class-management'); ?></p>
          </td>
        </tr>
        <tr>
          <th><label for="instructor_testimonial_text"><?php _e('Testimonial Text', 'class-management'); ?></label></th>
          <td>
            <textarea name="instructor_testimonial_text" id="instructor_testimonial_text" rows="4" cols="50" 
                      class="large-text"><?php echo esc_textarea($testimonial_text); ?></textarea>
            <p class="description"><?php _e('A testimonial or quote about your teaching.', 'class-management'); ?></p>
          </td>
        </tr>
        <tr>
          <th><label for="instructor_testimonial_author"><?php _e('Testimonial Author', 'class-management'); ?></label></th>
          <td>
            <input type="text" name="instructor_testimonial_author" id="instructor_testimonial_author" 
                   value="<?php echo esc_attr($testimonial_author); ?>" class="regular-text" 
                   placeholder="e.g., John Doe, Former Student" />
            <p class="description"><?php _e('Who said this testimonial?', 'class-management'); ?></p>
          </td>
        </tr>
        <tr>
          <th><label for="instructor_photo"><?php _e('Display Image', 'class-management'); ?></label></th>
          <td>
            <input type="hidden" name="instructor_photo" id="instructor_photo" value="<?php echo esc_attr($photo); ?>" />
            <button type="button" class="button" id="instructor_photo_button">
              <?php _e('Upload/Select Photo', 'class-management'); ?>
            </button>
            <button type="button" class="button" id="instructor_photo_remove" <?php echo empty($photo) ? 'style="display:none;"' : ''; ?>>
              <?php _e('Remove Photo', 'class-management'); ?>
            </button>
            <div id="instructor_photo_preview" style="margin-top: 10px;">
              <?php if ($photo) :
                    $photo_url = wp_get_attachment_image_url($photo, 'medium');
                    if ($photo_url) :
                        ?>
                <img src="<?php echo esc_url($photo_url); ?>" style="max-width: 200px; height: auto; display: block;" />
                    <?php endif;
              endif; ?>
            </div>
            <p class="description"><?php _e('Your profile photo that will be displayed on class pages.', 'class-management'); ?></p>
          </td>
        </tr>
      </table>
      
      <p class="submit">
        <input type="submit" name="cm_save_instructor_profile" class="button button-primary" value="<?php _e('Save Profile', 'class-management'); ?>" />
      </p>
    </form>
    <?php else : ?>
      <p><?php _e('Please select an instructor from the dropdown above to edit their profile.', 'class-management'); ?></p>
    <?php endif; ?>
  </div>
    <?php
}

/**
 * Save instructor profile data from admin page
 */
function cm_save_instructor_profile_data($user_id)
{
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    if (isset($_POST['instructor_degree'])) {
        update_user_meta($user_id, 'instructor_degree', sanitize_text_field($_POST['instructor_degree']));
    }

    if (isset($_POST['instructor_bio'])) {
        update_user_meta($user_id, 'instructor_bio', wp_kses_post($_POST['instructor_bio']));
    }

    if (isset($_POST['instructor_testimonial_text'])) {
        update_user_meta($user_id, 'instructor_testimonial_text', sanitize_textarea_field($_POST['instructor_testimonial_text']));
    }

    if (isset($_POST['instructor_testimonial_author'])) {
        update_user_meta($user_id, 'instructor_testimonial_author', sanitize_text_field($_POST['instructor_testimonial_author']));
    }

    if (isset($_POST['instructor_photo'])) {
        update_user_meta($user_id, 'instructor_photo', absint($_POST['instructor_photo']));
    }
}

