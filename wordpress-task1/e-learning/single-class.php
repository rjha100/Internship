<?php
//Template Name: Home
get_template_part('parts/header');

// Get custom fields
$instructor_id = get_post_meta(get_the_ID(), 'instructor', true);
$instructor = $instructor_id ? get_userdata($instructor_id) : null;
$instructor_name = $instructor ? $instructor->display_name : '';

$instructor_degree = '';
$instructor_bio = '';
$instructor_testimonial_text = '';
$instructor_testimonial_author = '';
$instructor_photo = '';
if ($instructor) {
    $instructor_degree = get_user_meta($instructor->ID, 'instructor_degree', true);
    $instructor_bio = get_user_meta($instructor->ID, 'instructor_bio', true);
    $instructor_testimonial_text = get_user_meta($instructor->ID, 'instructor_testimonial_text', true);
    $instructor_testimonial_author = get_user_meta($instructor->ID, 'instructor_testimonial_author', true);
    $instructor_photo_id = get_user_meta($instructor->ID, 'instructor_photo', true);
    $instructor_photo = $instructor_photo_id ? wp_get_attachment_image_url($instructor_photo_id, 'full') : '';
}

// Get taxonomy terms
$class_subjects = get_the_terms(get_the_ID(), 'class_subject');
$category_tag = ($class_subjects && !is_wp_error($class_subjects)) ? $class_subjects[0]->name : '';

$class_levels = get_the_terms(get_the_ID(), 'class_level');
$level_tag = ($class_levels && !is_wp_error($class_levels)) ? $class_levels[0]->name : '';

$start_date = get_post_meta(get_the_ID(), 'start_date', true);
$end_date = get_post_meta(get_the_ID(), 'end_date', true);
$start_date_formatted = $start_date ? date('M d', strtotime($start_date)) : '';
$end_date_formatted = $end_date ? date('M d', strtotime($end_date)) : '';

$start_time = get_post_meta(get_the_ID(), 'start_time', true);
$end_time = get_post_meta(get_the_ID(), 'end_time', true);

$class_time = '';
if ($start_time && $end_time) {
    $start_time_formatted = date('g:ia', strtotime($start_time));
    $end_time_formatted = date('g:ia', strtotime($end_time));
    $class_time = $start_time_formatted . ' - ' . $end_time_formatted;
}


$location_address = get_post_meta(get_the_ID(), 'location_address', true);
$location_city = get_post_meta(get_the_ID(), 'location_city', true);
$location_state = get_post_meta(get_the_ID(), 'location_state', true);
$location_zip = get_post_meta(get_the_ID(), 'location_zip', true);

$primary_price = get_post_meta(get_the_ID(), 'primary_price', true);
$member_price = get_post_meta(get_the_ID(), 'member_price', true);

$registration_deadline = get_post_meta(get_the_ID(), 'registration_deadline', true);
$registration_deadline_formatted = $registration_deadline ? date('M d \a\t g:ia (T)', strtotime($registration_deadline)) : '';

// Get testimonial from instructor user profile
$testimonial_text = $instructor ? get_user_meta($instructor->ID, 'instructor_testimonial_text', true) : '';
$testimonial_author = $instructor ? get_user_meta($instructor->ID, 'instructor_testimonial_author', true) : '';
$testimonial_bg = get_template_directory_uri() . '/assets/background.jpg';

$similar_classes_ids = get_post_meta(get_the_ID(), 'similar_classes', true);
$similar_classes = array();
if (!empty($similar_classes_ids) && is_array($similar_classes_ids)) {
    foreach ($similar_classes_ids as $class_id) {
        $class_post = get_post($class_id);
        if ($class_post) {
            $similar_classes[] = $class_post;
        }
    }
}
if (empty($similar_classes)) {
    // Get classes from the same subject
    $class_subjects = get_the_terms(get_the_ID(), 'class_subject');
    if ($class_subjects && !is_wp_error($class_subjects)) {
        $subject_ids = wp_list_pluck($class_subjects, 'term_id');
        $similar_classes_query = new WP_Query(array(
            'post_type' => 'class',
            'posts_per_page' => 3,
            'post__not_in' => array(get_the_ID()),
            'tax_query' => array(
                array(
                    'taxonomy' => 'class_subject',
                    'field' => 'term_id',
                    'terms' => $subject_ids,
                ),
            ),
        ));
        $similar_classes = $similar_classes_query->posts;
        wp_reset_postdata();
    }
}

?>
    <main class="content-wrapper">
      <section class="class-card">
        <div class="class-card-top">
          <div class="class-card-left">
            <div class="class-title">
              <?php echo get_the_title(); ?>
            </div>

            <div class="class-tags">
              <button class="pill pill-outline">
                <svg
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 5C13.66 5 15 6.34 15 8C15 9.66 13.66 11 12 11C10.34 11 9 9.66 9 8C9 6.34 10.34 5 12 5ZM12 19.2C9.5 19.2 7.29 17.92 6 15.98C6.03 13.99 10 12.9 12 12.9C13.99 12.9 17.97 13.99 18 15.98C16.71 17.92 14.5 19.2 12 19.2Z"
                    fill="#00ACB4"
                  />
                </svg>
                <?php echo esc_html($instructor_name); ?>
              </button>
              <?php if ($category_tag) : ?>
                <button class="pill pill-pink"><?php echo esc_html($category_tag); ?></button>
              <?php endif; ?>
              <?php if ($level_tag) : ?>
                <button class="pill pill-gray"><?php echo esc_html(ucfirst($level_tag)); ?></button>
              <?php endif; ?>
            </div>
          </div>

          <aside class="class-card-right">
            <div class="price-stack">
              <div class="price-box">
                <div class="price-col">
                  <div class="price-amount">$<?php echo esc_html($member_price); ?></div>
                  <div class="price-label">Member</div>
                </div>
                <div class="price-col">
                  <div class="price-amount">$<?php echo esc_html($primary_price); ?></div>
                  <div class="price-label">Non-Member</div>
                </div>
              </div>

              <button class="btn-primary-wide" id="signup-button">Sign Up</button>
            </div>

            <button class="btn-outline-wide" id="open-reduced-rate-modal">Apply for Reduced-Rate Spot</button>
          </aside>
        </div>

        <div class="class-card-bottom">
          <div class="class-meta">
            <div class="meta-item">
              <span class="meta-icon">
                <svg
                  width="19"
                  height="21"
                  viewBox="0 0 19 21"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M6.23438 2.625H12.1406V0.984375C12.1406 0.451172 12.5508 0 13.125 0C13.6582 0 14.1094 0.451172 14.1094 0.984375V2.625H15.75C17.1855 2.625 18.375 3.81445 18.375 5.25V18.375C18.375 19.8516 17.1855 21 15.75 21H2.625C1.14844 21 0 19.8516 0 18.375V5.25C0 3.81445 1.14844 2.625 2.625 2.625H4.26562V0.984375C4.26562 0.451172 4.67578 0 5.25 0C5.7832 0 6.23438 0.451172 6.23438 0.984375V2.625ZM1.96875 10.1719H5.25V7.875H1.96875V10.1719ZM1.96875 12.1406V14.7656H5.25V12.1406H1.96875ZM7.21875 12.1406V14.7656H11.1562V12.1406H7.21875ZM13.125 12.1406V14.7656H16.4062V12.1406H13.125ZM16.4062 7.875H13.125V10.1719H16.4062V7.875ZM16.4062 16.7344H13.125V19.0312H15.75C16.0781 19.0312 16.4062 18.7441 16.4062 18.375V16.7344ZM11.1562 16.7344H7.21875V19.0312H11.1562V16.7344ZM5.25 16.7344H1.96875V18.375C1.96875 18.7441 2.25586 19.0312 2.625 19.0312H5.25V16.7344ZM11.1562 7.875H7.21875V10.1719H11.1562V7.875Z"
                    fill="#DC3636"
                  />
                </svg>
              </span>
              <div class="meta-text">
                <div class="meta-primary">
                  <?php echo esc_html($start_date_formatted); ?> – <?php echo esc_html($end_date_formatted); ?>
                </div>
                <div class="meta-secondary"><?php echo esc_html($class_time); ?></div>
              </div>
            </div>

            <div class="meta-divider"></div>

            <div class="meta-item">
              <span class="meta-icon">
                <svg
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M12 2.16125C7.8 2.16125 4 5.38125 4 10.3613C4 13.5413 6.45 17.2813 11.34 21.5913C11.72 21.9213 12.29 21.9213 12.67 21.5913C17.55 17.2813 20 13.5413 20 10.3613C20 5.38125 16.2 2.16125 12 2.16125ZM12 12.1613C10.9 12.1613 10 11.2613 10 10.1613C10 9.06125 10.9 8.16125 12 8.16125C13.1 8.16125 14 9.06125 14 10.1613C14 11.2613 13.1 12.1613 12 12.1613Z"
                    fill="#DC3636"
                  />
                </svg>
              </span>
              <div class="meta-text">
                <div><?php echo esc_html($location_address); ?></div>
                <div>
                  <?php
                    $location_parts = array();
                    if ($location_city) {
                        $location_parts[] = $location_city;
                    }
                    if ($location_state) {
                        $location_parts[] = $location_state;
                    }
                    if ($location_zip) {
                        $location_parts[] = $location_zip;
                    }
                    echo esc_html(implode(', ', $location_parts));
                    ?>
                </div>
              </div>
            </div>

            <?php if ($registration_deadline_formatted) : ?>
            <div class="meta-divider"></div>

            <div class="meta-item">
              <span class="meta-icon">
                <svg
                  width="20"
                  height="20"
                  viewBox="0 0 20 20"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M10 0C4.5 0 0 4.5 0 10C0 15.5 4.5 20 10 20C15.5 20 20 15.5 20 10C20 4.5 15.5 0 10 0ZM14.2 14.2L9 11V5H10.5V10.2L15 12.9L14.2 14.2Z"
                    fill="#DC3636"
                  />
                </svg>
              </span>
              <div class="meta-text">
                <div class="meta-primary">Registration deadline:</div>
                <div class="meta-secondary"><?php echo esc_html($registration_deadline_formatted); ?></div>
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </section>

      <section class="class-details-card">
        <div class="class-details-inner">
          <div class="details-left">
            <div class="details-description">
              <?php
              // Display the class content/description
                if (have_posts()) :
                    while (have_posts()) :
                        the_post();
                        the_content();
                    endwhile;
                endif;
                wp_reset_postdata();
                ?>
            </div>

            <div class="details-share">
              <span class="share-label">Share</span>
              <div class="share-icon">
                <svg
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M12 0C5.37258 0 0 5.37258 0 12C0 18.6274 5.37258 24 12 24C18.6274 24 24 18.6274 24 12C24 5.37258 18.6274 0 12 0ZM13.2508 12.5271V19.0557H10.5495V12.5274H9.2V10.2776H10.5495V8.92678C10.5495 7.0914 11.3116 6 13.4766 6H15.279V8.25006H14.1524C13.3096 8.25006 13.2538 8.56447 13.2538 9.15125L13.2508 10.2773H15.2918L15.053 12.5271H13.2508Z"
                    fill="#434343"
                  />
                  <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M12 0C5.37258 0 0 5.37258 0 12C0 18.6274 5.37258 24 12 24C18.6274 24 24 18.6274 24 12C24 5.37258 18.6274 0 12 0ZM13.2508 12.5271V19.0557H10.5495V12.5274H9.2V10.2776H10.5495V8.92678C10.5495 7.0914 11.3116 6 13.4766 6H15.279V8.25006H14.1524C13.3096 8.25006 13.2538 8.56447 13.2538 9.15125L13.2508 10.2773H15.2918L15.053 12.5271H13.2508Z"
                    fill="white"
                    fill-opacity="0.5"
                  />
                </svg>
              </div>
              <div class="share-icon">
                <svg
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M12 0C5.37258 0 0 5.37258 0 12C0 18.6274 5.37258 24 12 24C18.6274 24 24 18.6274 24 12C24 5.37258 18.6274 0 12 0ZM11.6658 10.169L11.6406 9.75375C11.5651 8.67755 12.2282 7.69456 13.2774 7.31323C13.6635 7.17765 14.3182 7.1607 14.7463 7.27934C14.9142 7.33018 15.2331 7.49966 15.4598 7.65219L15.8711 7.93184L16.3243 7.78778C16.5761 7.71151 16.9119 7.5844 17.063 7.49966C17.2057 7.42339 17.3316 7.38102 17.3316 7.40645C17.3316 7.5505 17.021 8.042 16.7608 8.31317C16.4083 8.6945 16.509 8.72839 17.2224 8.47417C17.6505 8.33011 17.6589 8.33011 17.575 8.49112C17.5246 8.57586 17.2644 8.87245 16.9874 9.14362C16.5174 9.60969 16.4922 9.66054 16.4922 10.0503C16.4922 10.652 16.2068 11.9062 15.9214 12.5925C15.3926 13.8806 14.2595 15.211 13.1263 15.8805C11.5315 16.8211 9.40786 17.0584 7.61999 16.5075C7.02404 16.3211 6 15.8466 6 15.7618C6 15.7364 6.31057 15.7025 6.68829 15.694C7.4773 15.6771 8.26631 15.4568 8.93781 15.067L9.39108 14.7958L8.87066 14.6178C8.13201 14.3636 7.46891 13.7789 7.30103 13.2281C7.25067 13.0501 7.26746 13.0417 7.73751 13.0417L8.22434 13.0332L7.81305 12.8383C7.32621 12.5925 6.88134 12.1773 6.66311 11.7536C6.50362 11.4486 6.30218 10.6774 6.36093 10.6181C6.37772 10.5927 6.55399 10.6435 6.75544 10.7113C7.33461 10.9232 7.41015 10.8723 7.0744 10.5164C6.44487 9.87239 6.25181 8.91482 6.55399 8.0081L6.69668 7.60135L7.25067 8.15216C8.38383 9.26226 9.71843 9.92323 11.2461 10.1181L11.6658 10.169Z"
                    fill="#434343"
                  />
                  <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M12 0C5.37258 0 0 5.37258 0 12C0 18.6274 5.37258 24 12 24C18.6274 24 24 18.6274 24 12C24 5.37258 18.6274 0 12 0ZM11.6658 10.169L11.6406 9.75375C11.5651 8.67755 12.2282 7.69456 13.2774 7.31323C13.6635 7.17765 14.3182 7.1607 14.7463 7.27934C14.9142 7.33018 15.2331 7.49966 15.4598 7.65219L15.8711 7.93184L16.3243 7.78778C16.5761 7.71151 16.9119 7.5844 17.063 7.49966C17.2057 7.42339 17.3316 7.38102 17.3316 7.40645C17.3316 7.5505 17.021 8.042 16.7608 8.31317C16.4083 8.6945 16.509 8.72839 17.2224 8.47417C17.6505 8.33011 17.6589 8.33011 17.575 8.49112C17.5246 8.57586 17.2644 8.87245 16.9874 9.14362C16.5174 9.60969 16.4922 9.66054 16.4922 10.0503C16.4922 10.652 16.2068 11.9062 15.9214 12.5925C15.3926 13.8806 14.2595 15.211 13.1263 15.8805C11.5315 16.8211 9.40786 17.0584 7.61999 16.5075C7.02404 16.3211 6 15.8466 6 15.7618C6 15.7364 6.31057 15.7025 6.68829 15.694C7.4773 15.6771 8.26631 15.4568 8.93781 15.067L9.39108 14.7958L8.87066 14.6178C8.13201 14.3636 7.46891 13.7789 7.30103 13.2281C7.25067 13.0501 7.26746 13.0417 7.73751 13.0417L8.22434 13.0332L7.81305 12.8383C7.32621 12.5925 6.88134 12.1773 6.66311 11.7536C6.50362 11.4486 6.30218 10.6774 6.36093 10.6181C6.37772 10.5927 6.55399 10.6435 6.75544 10.7113C7.33461 10.9232 7.41015 10.8723 7.0744 10.5164C6.44487 9.87239 6.25181 8.91482 6.55399 8.0081L6.69668 7.60135L7.25067 8.15216C8.38383 9.26226 9.71843 9.92323 11.2461 10.1181L11.6658 10.169Z"
                    fill="white"
                    fill-opacity="0.5"
                  />
                </svg>
              </div>
              <div class="share-icon">
                <svg
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M12 0C5.37258 0 0 5.37258 0 12C0 18.6274 5.37258 24 12 24C18.6274 24 24 18.6274 24 12C24 5.37258 18.6274 0 12 0ZM5.76084 9.93892H8.4803V18.1098H5.76084V9.93892ZM8.65938 7.41135C8.64173 6.6102 8.06883 6 7.13852 6C6.20821 6 5.6 6.6102 5.6 7.41135C5.6 8.1959 6.19023 8.82367 7.10322 8.82367H7.12059C8.06883 8.82367 8.65938 8.1959 8.65938 7.41135ZM15.1566 9.74707C16.9461 9.74707 18.2877 10.9151 18.2877 13.4249L18.2876 18.1098H15.5682V13.7384C15.5682 12.6404 15.1747 11.8911 14.1902 11.8911C13.4389 11.8911 12.9914 12.3962 12.7949 12.8841C12.723 13.0589 12.7053 13.3025 12.7053 13.5467V18.11H9.98555C9.98555 18.11 10.0214 10.7059 9.98555 9.93915H12.7053V11.0965C13.0663 10.5401 13.7127 9.74707 15.1566 9.74707Z"
                    fill="#434343"
                  />
                  <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M12 0C5.37258 0 0 5.37258 0 12C0 18.6274 5.37258 24 12 24C18.6274 24 24 18.6274 24 12C24 5.37258 18.6274 0 12 0ZM5.76084 9.93892H8.4803V18.1098H5.76084V9.93892ZM8.65938 7.41135C8.64173 6.6102 8.06883 6 7.13852 6C6.20821 6 5.6 6.6102 5.6 7.41135C5.6 8.1959 6.19023 8.82367 7.10322 8.82367H7.12059C8.06883 8.82367 8.65938 8.1959 8.65938 7.41135ZM15.1566 9.74707C16.9461 9.74707 18.2877 10.9151 18.2877 13.4249L18.2876 18.1098H15.5682V13.7384C15.5682 12.6404 15.1747 11.8911 14.1902 11.8911C13.4389 11.8911 12.9914 12.3962 12.7949 12.8841C12.723 13.0589 12.7053 13.3025 12.7053 13.5467V18.11H9.98555C9.98555 18.11 10.0214 10.7059 9.98555 9.93915H12.7053V11.0965C13.0663 10.5401 13.7127 9.74707 15.1566 9.74707Z"
                    fill="white"
                    fill-opacity="0.5"
                  />
                </svg>
              </div>
              <div class="share-icon">
                <svg
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <circle cx="12" cy="12" r="12" fill="#434343" />
                  <circle cx="12" cy="12" r="12" fill="white" fill-opacity="0.5" />
                  <path
                    d="M16.1666 7.83334H13.6666C13.2083 7.83334 12.8333 8.20834 12.8333 8.66667C12.8333 9.125 13.2083 9.5 13.6666 9.5H16.1666C17.5416 9.5 18.6666 10.625 18.6666 12C18.6666 13.375 17.5416 14.5 16.1666 14.5H13.6666C13.2083 14.5 12.8333 14.875 12.8333 15.3333C12.8333 15.7917 13.2083 16.1667 13.6666 16.1667H16.1666C18.4666 16.1667 20.3333 14.3 20.3333 12C20.3333 9.7 18.4666 7.83334 16.1666 7.83334ZM8.66663 12C8.66663 12.4583 9.04163 12.8333 9.49996 12.8333H14.5C14.9583 12.8333 15.3333 12.4583 15.3333 12C15.3333 11.5417 14.9583 11.1667 14.5 11.1667H9.49996C9.04163 11.1667 8.66663 11.5417 8.66663 12ZM10.3333 14.5H7.83329C6.45829 14.5 5.33329 13.375 5.33329 12C5.33329 10.625 6.45829 9.5 7.83329 9.5H10.3333C10.7916 9.5 11.1666 9.125 11.1666 8.66667C11.1666 8.20834 10.7916 7.83334 10.3333 7.83334H7.83329C5.53329 7.83334 3.66663 9.7 3.66663 12C3.66663 14.3 5.53329 16.1667 7.83329 16.1667H10.3333C10.7916 16.1667 11.1666 15.7917 11.1666 15.3333C11.1666 14.875 10.7916 14.5 10.3333 14.5Z"
                    fill="white"
                  />
                </svg>
              </div>
            </div>
            <span class="share-divider"></span>

            <div class="details-schedule">
              <h2 class="schedule-heading">Class Schedule</h2>
              <div class="schedule-row">
                <div class="schedule-label">Time:</div>
                <div class="schedule-value"><?php echo esc_html($class_time); ?></div>
              </div>
              <?php
                $schedule_dates = get_post_meta(get_the_ID(), 'class_schedule_dates', true);
                if ($schedule_dates) :
                  // Use regex to match pattern "Day, Date" (e.g., "Tue, 6/15")
                    preg_match_all('/([A-Za-z]+),\s*([0-9\/]+)/', $schedule_dates, $matches, PREG_SET_ORDER);
                    if (!empty($matches)) :
                        ?>
              <div class="schedule-row">
                <div class="schedule-label">Dates:</div>
                <div class="schedule-dates">
                        <?php foreach ($matches as $match) :
                            $day = isset($match[1]) ? $match[1] : '';
                            $date = isset($match[2]) ? $match[2] : '';
                            ?>
                  <div class="schedule-date">
                    <div class="date-day"><?php echo esc_html($day); ?></div>
                    <div class="date-num"><?php echo esc_html($date); ?></div>
                  </div>
                        <?php endforeach; ?>
                </div>
              </div>
                        <?php
                    endif;
                endif;
                ?>
            </div>
          </div>

          <aside class="details-right">
            <?php if ($instructor) : ?>
            <div class="instructor-card">
                <?php if ($instructor_photo) : ?>
                <div class="instructor-photo" style="background-image: url('<?php echo esc_url($instructor_photo); ?>'); background-size: cover; background-position: center;"></div>
                <?php else : ?>
                <div class="instructor-photo"></div>
                <?php endif; ?>
              <div class="instructor-text">
                <p class="instructor-details">
                  <span class="instructor-name"><?php echo esc_html($instructor->first_name ?: explode(' ', $instructor_name)[0]); ?></span><?php if ($instructor_degree) :
                        ?>, <span class="instructor-degree"><?php echo esc_html($instructor_degree); ?></span><?php
                                                endif; ?>
                </p>
                <?php if ($instructor_bio) : ?>
                  <p class="instructor-bio-text"><?php echo esc_html(wp_strip_all_tags($instructor_bio)); ?></p>
                <?php endif; ?>
              </div>
              <button class="btn-instructor">See <?php echo esc_html($instructor_name); ?>'s Reviews</button>
            </div>
            <?php endif; ?>
          </aside>
        </div>
      </section>

      <?php if ($testimonial_text) :
            $bg_style = $testimonial_bg ? 'background-image: url(' . esc_url($testimonial_bg) . '); background-size: cover; background-position: center;' : '';
            ?>
      <section class="testimonial-section">
        <div class="testimonial-bg" style="<?php echo esc_attr($bg_style); ?>">
          <button class="testimonial-arrow testimonial-arrow--left">
            <svg
              width="32"
              height="31"
              viewBox="0 0 32 31"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M20.9401 20.8993L14.8334 15.1171L20.9401 9.33479L19.0601 7.55853L11.0601 15.1171L19.0601 22.6756L20.9401 20.8993Z"
                fill="white"
              />
            </svg>
          </button>

          <div class="testimonial-card">
            <div class="testimonial-quote-row">
              <span class="quote-mark quote-mark--left">
                <svg
                  width="47"
                  height="33"
                  viewBox="0 0 47 33"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M46.3 3.4C46.3 1.6 45.2 -4.29153e-06 42.5 -4.29153e-06C38.8 -4.29153e-06 34.3 1.8 31.3 4.8C27.6 8.5 25.4 13.9 25.4 21.2V21.6C25.4 28.5 28.7 32.8 34.3 32.8C39.3 32.8 42.6 29.3 42.6 24.6V24.4C42.6 20.1 39.1 17.1 35.6 16.2C35.4 11.4 38.2 7.9 43.3 6.7C45 6.3 46.3 5.19999 46.3 3.4ZM20.9 3.4C20.9 1.6 19.8 -4.29153e-06 17.1 -4.29153e-06C13.4 -4.29153e-06 8.89995 1.8 5.89995 4.8C2.19995 8.5 -4.85182e-05 13.9 -4.85182e-05 21.2V21.6C-4.85182e-05 28.5 3.29995 32.8 8.89995 32.8C13.9 32.8 17.2 29.3 17.2 24.6V24.4C17.2 20.1 13.7 17.1 10.2 16.2C9.99995 11.4 12.8 7.9 17.9 6.7C19.6 6.3 20.9 5.19999 20.9 3.4Z"
                    fill="#00ACB4"
                  />
                </svg>
              </span>
              <p class="testimonial-text">
                <?php echo esc_html($testimonial_text); ?>
                <?php if ($testimonial_author) : ?>
                  <span class="testimonial-author"><?php echo esc_html($testimonial_author); ?></span>
                <?php endif; ?>
              </p>
              <span class="quote-mark quote-mark--right">
                <svg
                  width="47"
                  height="33"
                  viewBox="0 0 47 33"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M6.12438e-06 29.4C6.12438e-06 31.2 1.10001 32.8 3.80001 32.8C7.50001 32.8 12 31 15 28C18.7 24.3 20.9 18.9 20.9 11.6V11.2C20.9 4.3 17.6 3.09944e-06 12 3.09944e-06C7.00001 3.09944e-06 3.70001 3.50001 3.70001 8.2V8.4C3.70001 12.7 7.20001 15.7 10.7 16.6C10.9 21.4 8.10001 24.9 3.00001 26.1C1.30001 26.5 6.12438e-06 27.6 6.12438e-06 29.4ZM25.4 29.4C25.4 31.2 26.5 32.8 29.2 32.8C32.9 32.8 37.4 31 40.4 28C44.1 24.3 46.3 18.9 46.3 11.6V11.2C46.3 4.3 43 3.09944e-06 37.4 3.09944e-06C32.4 3.09944e-06 29.1 3.50001 29.1 8.2V8.4C29.1 12.7 32.6 15.7 36.1 16.6C36.3 21.4 33.5 24.9 28.4 26.1C26.7 26.5 25.4 27.6 25.4 29.4Z"
                    fill="#00ACB4"
                  />
                </svg>
              </span>
            </div>
          </div>

          <button class="testimonial-arrow testimonial-arrow--right">
            <svg
              width="32"
              height="31"
              viewBox="0 0 32 31"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M11.06 20.8993L17.1667 15.1171L11.06 9.33479L12.94 7.55853L20.94 15.1171L12.94 22.6756L11.06 20.8993Z"
                fill="white"
              />
            </svg>
          </button>
        </div>
      </section>
      <?php endif; ?>

      <?php if (!empty($similar_classes)) : ?>
      <section class="similar-classes-section">
        <div class="similar-classes-card">
          <h2 class="similar-classes-title">Similar Classes</h2>

          <div class="similar-classes-grid">
            <?php foreach ($similar_classes as $similar_class) :
                $excerpt = get_the_excerpt($similar_class->ID);
                if (empty($excerpt)) {
                    $excerpt = wp_trim_words($similar_class->post_content, 20, '...');
                } else {
                    $excerpt = wp_trim_words($excerpt, 20, '...');
                }
                ?>
            <article class="similar-class">
              <h3 class="similar-class-name">
                <?php echo esc_html($similar_class->post_title); ?>
              </h3>
              <p class="similar-class-excerpt"><?php echo esc_html($excerpt); ?></p>
              <a href="<?php echo esc_url(get_permalink($similar_class->ID)); ?>" class="similar-class-link">Learn More &gt;</a>
            </article>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
      <?php endif; ?>

      <section class="info-section">
        <div class="info-inner">
          <section class="info-block">
            <h2 class="info-title">About the Remote Classes</h2>
            <p class="info-text">
              You will be able to participate in live class meetings via Zoom
              videoconference. To attend classes, you'll need a phone, tablet or
              computer and access to the internet. You can participate in the class
              from wherever you'd like, whether on your living room couch or in your
              office. Before your class meets, you'll receive an email from The
              Proprietary with more information about Zoom and your remote class. If
              you have any questions about remote learning, please don't hesitate to
              reach out to us at
              <a href="mailto:hello@wisdmlabs.com" class="info-link"
                >hello@wisdmlabs.com</a
              >.
            </p>

            <div class="info-links-row">
              <a href="#" class="info-cta-link">
                Learn About Our Classes
                <span class="info-arrow">
                  <svg
                    width="6"
                    height="10"
                    viewBox="0 0 6 10"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      d="M0.24375 7.88542L3.47708 4.65208L0.24375 1.41875C-0.08125 1.09375 -0.08125 0.56875 0.24375 0.24375C0.56875 -0.08125 1.09375 -0.08125 1.41875 0.24375L5.24375 4.06875C5.56875 4.39375 5.56875 4.91875 5.24375 5.24375L1.41875 9.06875C1.09375 9.39375 0.56875 9.39375 0.24375 9.06875C-0.0729167 8.74375 -0.08125 8.21042 0.24375 7.88542Z"
                      fill="#DC3636"
                    />
                  </svg>
                </span>
              </a>
              <a href="#" class="info-cta-link">
                Questions? See FAQ&ensp;
                <svg
                  width="6"
                  height="10"
                  viewBox="0 0 6 10"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M0.24375 7.88542L3.47708 4.65208L0.24375 1.41875C-0.08125 1.09375 -0.08125 0.56875 0.24375 0.24375C0.56875 -0.08125 1.09375 -0.08125 1.41875 0.24375L5.24375 4.06875C5.56875 4.39375 5.56875 4.91875 5.24375 5.24375L1.41875 9.06875C1.09375 9.39375 0.56875 9.39375 0.24375 9.06875C-0.0729167 8.74375 -0.08125 8.21042 0.24375 7.88542Z"
                    fill="#DC3636"
                  />
                </svg>
              </a>
            </div>
          </section>

          <hr class="info-divider" />

          <section class="info-block">
            <h2 class="info-title">Cancelation Policy</h2>

            <p class="info-text">
              In the event of an emergency, we may consider a refund or credit,
              whether partial or full. We review these requests on a case-by-case
              basis, and we ask that you notify us as near as possible to the start
              date for the class. Please read our policy details below before
              requesting a refund.
            </p>

            <div class="policy-grid">
              <div class="policy-item">
                <div class="policy-heading policy-heading--red">
                  10 days or more
                </div>
                <p class="policy-body">
                  before the start date for a class, the registrant will receive a
                  credit minus a 10% fee OR a refund minus a 20% fee.
                </p>
              </div>

              <div class="policy-item">
                <div class="policy-heading policy-heading--red">3–9 days</div>
                <p class="policy-body">
                  before the start date for a class, the registrant will receive a
                  credit minus a 20% fee OR a refund minus a 30% fee.
                </p>
              </div>

              <div class="policy-item">
                <div class="policy-heading policy-heading--red">2 days or less</div>
                <p class="policy-body">
                  before the start date for a class, the registrant will not receive
                  a credit or a refund.
                </p>
              </div>

              <div class="policy-item">
                <div class="policy-heading policy-heading--red">
                  On the day or after
                </div>
                <p class="policy-body">
                  The Proprietary
                  <strong>cannot offer refunds, credits, or makeup sessions</strong>
                  for classes a student might miss.
                </p>
              </div>
            </div>
          </section>
        </div>
      </section>
    </main>

    <!-- Reduced Rate Modal -->
    <div id="reduced-rate-modal" class="modal-overlay">
      <div class="modal-container">
        <button class="modal-close" id="close-reduced-rate-modal">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" fill="currentColor"/>
          </svg>
        </button>
        <div class="modal-header">
          <h2 class="modal-title">Apply for Reduced-Rate Spot</h2>
          <p class="modal-subtitle">for <?php echo esc_html(get_the_title()); ?></p>
        </div>
        <form id="reduced-rate-form" class="modal-form">
          <input type="hidden" name="class_id" value="<?php echo get_the_ID(); ?>">
          <input type="hidden" name="class_title" value="<?php echo esc_attr(get_the_title()); ?>">
          <div class="form-group">
            <label for="applicant-name" class="form-label">Your Name</label>
            <input type="text" id="applicant-name" name="applicant_name" class="form-input" required placeholder="Enter your full name">
          </div>
          <div class="form-group">
            <label for="applicant-email" class="form-label">Email Address</label>
            <input type="email" id="applicant-email" name="applicant_email" class="form-input" required placeholder="Enter your email">
          </div>
          <div class="form-group">
            <label for="reduced-rate-reason" class="form-label">Why are you applying for a reduced-rate spot?</label>
            <textarea 
              id="reduced-rate-reason" 
              name="reason" 
              class="form-textarea" 
              rows="5" 
              required
              placeholder="Please share your reason for applying for a reduced-rate spot..."
            ></textarea>
          </div>
          <div class="form-actions">
            <button type="button" class="btn-modal-cancel" id="cancel-reduced-rate">Cancel</button>
            <button type="submit" class="btn-modal-submit">Submit Application</button>
          </div>
        </form>
        <div id="form-success-message" class="form-success" style="display: none;">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM10 17L5 12L6.41 10.59L10 14.17L17.59 6.58L19 8L10 17Z" fill="#00ACB4"/>
          </svg>
          <h3>Application Submitted!</h3>
          <p>Thank you for your application. We'll review it and get back to you soon.</p>
          <button type="button" class="btn-modal-submit" id="close-success-modal">Close</button>
        </div>
      </div>
    </div>

<?php get_template_part('parts/footer')?>
