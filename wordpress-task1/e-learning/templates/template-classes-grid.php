<?php

/**
 * Template Name: Classes Grid
 * Description: Displays all classes in a responsive grid layout
 */

get_template_part("parts/header");

// Get search and filter parameters
$search_query = isset($_GET['class_search']) ? sanitize_text_field($_GET['class_search']) : '';
$filter_subject = isset($_GET['subject']) ? sanitize_text_field($_GET['subject']) : '';
$filter_level = isset($_GET['level']) ? sanitize_text_field($_GET['level']) : '';
$filter_price_min = isset($_GET['price_min']) ? floatval($_GET['price_min']) : '';
$filter_price_max = isset($_GET['price_max']) ? floatval($_GET['price_max']) : '';
$filter_is_member = isset($_GET['is_member']) && $_GET['is_member'] === '1';
$filter_date_from = isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : '';
$filter_date_to = isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : '';
$filter_time_from = isset($_GET['time_from']) ? sanitize_text_field($_GET['time_from']) : '';
$filter_time_to = isset($_GET['time_to']) ? sanitize_text_field($_GET['time_to']) : '';
$filter_city = isset($_GET['city']) ? sanitize_text_field($_GET['city']) : '';

// Get all subjects and levels for filter dropdowns
$subjects = get_terms(array(
  'taxonomy' => 'class_subject',
  'hide_empty' => true,
));

$levels = get_terms(array(
  'taxonomy' => 'class_level',
  'hide_empty' => true,
));

// Get distinct cities from post meta
global $wpdb;
$cities = $wpdb->get_col("
  SELECT DISTINCT meta_value 
  FROM {$wpdb->postmeta} 
  WHERE meta_key = 'location_city' 
  AND meta_value != '' 
  ORDER BY meta_value ASC
");

// Check if any filters are active
$has_active_filters = !empty($search_query) || !empty($filter_subject) || !empty($filter_level) ||
                      !empty($filter_price_min) || !empty($filter_price_max) ||
                      !empty($filter_date_from) || !empty($filter_date_to) ||
                      !empty($filter_time_from) || !empty($filter_time_to) || !empty($filter_city);
?>

<div class="classes-grid-outer">
  <div class="classes-grid-wrapper">
    <div class="container">
    <?php if (have_posts()) :
        while (have_posts()) :
            the_post(); ?>
      <div class="page-header">
        <h1 class="page-title"><?php the_title(); ?></h1>
            <?php if (has_excerpt()) : ?>
          <div class="page-excerpt">
                <?php the_excerpt(); ?>
          </div>
            <?php endif; ?>
      </div>
      
      <div class="page-content">
            <?php the_content(); ?>
      </div>
        <?php endwhile;
    endif; ?>

    <!-- Sidebar Layout Container -->
    <div class="classes-content-wrapper">
      <!-- Filter Sidebar -->
      <aside class="filter-sidebar">
        <h2 class="sidebar-title">Filters</h2>
        <form class="filter-sidebar-form" method="get" action="">
          <!-- Hidden search value to preserve search query -->
          <input type="hidden" name="class_search" value="<?php echo esc_attr($search_query); ?>">
          
          <!-- Subject Filter -->
          <div class="filter-section">
            <h3 class="filter-section-title">Subject</h3>
            <select name="subject" class="sidebar-filter-select">
              <option value="">All Subjects</option>
              <?php if (!is_wp_error($subjects) && !empty($subjects)) : ?>
                    <?php foreach ($subjects as $subject) : ?>
                  <option value="<?php echo esc_attr($subject->slug); ?>" <?php selected($filter_subject, $subject->slug); ?>>
                        <?php echo esc_html($subject->name); ?>
                  </option>
                    <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <!-- Level Filter -->
          <div class="filter-section">
            <h3 class="filter-section-title">Difficulty Level</h3>
            <select name="level" class="sidebar-filter-select">
              <option value="">All Levels</option>
              <?php if (!is_wp_error($levels) && !empty($levels)) : ?>
                    <?php foreach ($levels as $level) : ?>
                  <option value="<?php echo esc_attr($level->slug); ?>" <?php selected($filter_level, $level->slug); ?>>
                        <?php echo esc_html($level->name); ?>
                  </option>
                    <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <!-- Location Filters -->
          <div class="filter-section">
            <h3 class="filter-section-title">Location</h3>
            <div class="location-inputs-wrapper">
              <div class="location-input-group">
                <input 
                  type="text" 
                  name="city" 
                  class="sidebar-location-input" 
                  placeholder="City" 
                  value="<?php echo esc_attr($filter_city); ?>"
                >
              </div>
            </div>
          </div>

          <!-- Price Filter -->
          <div class="filter-section">
            <h3 class="filter-section-title">Price</h3>
            <div class="price-inputs-wrapper">
              <div class="price-input-group">
                <span class="currency-symbol">$</span>
                <input 
                  type="number" 
                  name="price_min" 
                  class="sidebar-price-input" 
                  placeholder="min" 
                  value="<?php echo esc_attr($filter_price_min); ?>"
                  min="0"
                  step="1"
                >
              </div>
              <span class="price-separator">—</span>
              <div class="price-input-group">
                <span class="currency-symbol">$</span>
                <input 
                  type="number" 
                  name="price_max" 
                  class="sidebar-price-input" 
                  placeholder="max" 
                  value="<?php echo esc_attr($filter_price_max); ?>"
                  min="0"
                  step="1"
                >
              </div>
            </div>
            <div class="member-checkbox-wrapper">
              <label class="sidebar-checkbox-label">
                <input 
                  type="checkbox" 
                  name="is_member" 
                  value="1" 
                  class="sidebar-checkbox"
                  <?php checked($filter_is_member, true); ?>
                >
                <span class="sidebar-checkbox-custom"></span>
                <span class="sidebar-checkbox-text">I'm a member</span>
              </label>
            </div>
          </div>

          <!-- Date Range Filter -->
          <div class="filter-section">
            <h3 class="filter-section-title">Class Dates</h3>
            <div class="date-inputs-wrapper">
              <input 
                type="date" 
                name="date_from" 
                class="sidebar-date-input" 
                value="<?php echo esc_attr($filter_date_from); ?>"
                placeholder="From"
              >
              <span class="date-separator">to</span>
              <input 
                type="date" 
                name="date_to" 
                class="sidebar-date-input" 
                value="<?php echo esc_attr($filter_date_to); ?>"
                placeholder="To"
              >
            </div>
          </div>

          <!-- Time Filter -->
          <div class="filter-section">
            <h3 class="filter-section-title">Time of Day</h3>
            <div class="time-inputs-wrapper">
              <input 
                type="time" 
                name="time_from" 
                class="sidebar-time-input" 
                value="<?php echo esc_attr($filter_time_from); ?>"
              >
              <span class="time-separator">to</span>
              <input 
                type="time" 
                name="time_to" 
                class="sidebar-time-input" 
                value="<?php echo esc_attr($filter_time_to); ?>"
              >
            </div>
          </div>

          <!-- Filter Actions -->
          <div class="filter-section filter-actions">
            <button type="submit" class="sidebar-apply-btn">Apply Filters</button>
            <?php if ($has_active_filters) : ?>
              <a href="<?php echo esc_url(get_permalink()); ?>" class="sidebar-clear-btn">Clear Filters</a>
            <?php endif; ?>
          </div>
        </form>
      </aside>

      <!-- Main Content Area -->
      <div class="classes-main-content">
        <!-- Search Bar Above Grid -->
        <div class="search-bar-wrapper">
          <form class="search-bar-form" method="get" action="">
            <!-- Preserve filter parameters -->
            <input type="hidden" name="subject" value="<?php echo esc_attr($filter_subject); ?>">
            <input type="hidden" name="level" value="<?php echo esc_attr($filter_level); ?>">
            <input type="hidden" name="price_min" value="<?php echo esc_attr($filter_price_min); ?>">
            <input type="hidden" name="price_max" value="<?php echo esc_attr($filter_price_max); ?>">
            <input type="hidden" name="is_member" value="<?php echo $filter_is_member ? '1' : ''; ?>">
            <input type="hidden" name="date_from" value="<?php echo esc_attr($filter_date_from); ?>">
            <input type="hidden" name="date_to" value="<?php echo esc_attr($filter_date_to); ?>">
            <input type="hidden" name="time_from" value="<?php echo esc_attr($filter_time_from); ?>">
            <input type="hidden" name="time_to" value="<?php echo esc_attr($filter_time_to); ?>">
            <input type="hidden" name="city" value="<?php echo esc_attr($filter_city); ?>">
            
            <div class="search-bar-container">
              <div class="search-input-group">
                <svg class="search-bar-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M14.2939 12.5786H13.3905L13.0703 12.2699C14.191 10.9663 14.8656 9.27387 14.8656 7.43282C14.8656 3.32762 11.538 0 7.43282 0C3.32762 0 0 3.32762 0 7.43282C0 11.538 3.32762 14.8656 7.43282 14.8656C9.27387 14.8656 10.9663 14.191 12.2699 13.0703L12.5786 13.3905V14.2939L18.2962 20L20 18.2962L14.2939 12.5786ZM7.43282 12.5786C4.58548 12.5786 2.28702 10.2802 2.28702 7.43282C2.28702 4.58548 4.58548 2.28702 7.43282 2.28702C10.2802 2.28702 12.5786 4.58548 12.5786 7.43282C12.5786 10.2802 10.2802 12.5786 7.43282 12.5786Z" fill="#999"/>
                </svg>
                <input 
                  type="text" 
                  name="class_search" 
                  class="search-bar-input" 
                  placeholder="Search for classes, instructors, or topics..." 
                  value="<?php echo esc_attr($search_query); ?>"
                >
                <?php if ($search_query) : ?>
                  <button type="button" class="search-clear-btn" onclick="this.previousElementSibling.value=''; this.closest('form').submit();" title="Clear search">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" fill="currentColor"/>
                    </svg>
                  </button>
                <?php endif; ?>
              </div>
              <button type="submit" class="search-bar-submit">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M14.2939 12.5786H13.3905L13.0703 12.2699C14.191 10.9663 14.8656 9.27387 14.8656 7.43282C14.8656 3.32762 11.538 0 7.43282 0C3.32762 0 0 3.32762 0 7.43282C0 11.538 3.32762 14.8656 7.43282 14.8656C9.27387 14.8656 10.9663 14.191 12.2699 13.0703L12.5786 13.3905V14.2939L18.2962 20L20 18.2962L14.2939 12.5786ZM7.43282 12.5786C4.58548 12.5786 2.28702 10.2802 2.28702 7.43282C2.28702 4.58548 4.58548 2.28702 7.43282 2.28702C10.2802 2.28702 12.5786 4.58548 12.5786 7.43282C12.5786 10.2802 10.2802 12.5786 7.43282 12.5786Z" fill="#fff"/>
                </svg>
                Search
              </button>
            </div>
          </form>
          
          <?php if ($search_query) : ?>
            <div class="search-results-info">
              <span>Showing results for: <strong>"<?php echo esc_html($search_query); ?>"</strong></span>
            </div>
          <?php endif; ?>
        </div>


    <?php
    // Query classes with advanced filters
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $args = array(
      'post_type'      => 'class',
      'posts_per_page' => 12,
      'paged'          => $paged,
      'orderby'        => 'date',
      'order'          => 'DESC',
      'post_status'    => 'publish',
    );

    // Build tax_query for taxonomy filters
    $tax_query = array();

    if (!empty($filter_subject)) {
        $tax_query[] = array(
        'taxonomy' => 'class_subject',
        'field'    => 'slug',
        'terms'    => $filter_subject,
        );
    }

    if (!empty($filter_level)) {
        $tax_query[] = array(
        'taxonomy' => 'class_level',
        'field'    => 'slug',
        'terms'    => $filter_level,
        );
    }

    if (!empty($tax_query)) {
        $tax_query['relation'] = 'AND';
        $args['tax_query'] = $tax_query;
    }

    // Build meta_query for meta filters
    $meta_query = array();

    // Price filter (using member or primary price based on checkbox)
    $has_price_min = ($filter_price_min !== '' && $filter_price_min > 0);
    $has_price_max = ($filter_price_max !== '' && $filter_price_max > 0);

    if ($has_price_min || $has_price_max) {
        $price_meta_key = $filter_is_member ? 'member_price' : 'primary_price';
        $price_query = array(
        'key'     => $price_meta_key,
        'type'    => 'NUMERIC',
        );

        if ($has_price_min && $has_price_max) {
          // Both min and max specified: show prices between min and max
            $price_query['value'] = array($filter_price_min, $filter_price_max);
            $price_query['compare'] = 'BETWEEN';
        } elseif ($has_price_min) {
          // Only min specified: show prices >= min
            $price_query['value'] = $filter_price_min;
            $price_query['compare'] = '>=';
        } elseif ($has_price_max) {
          // Only max specified: show prices <= max
            $price_query['value'] = $filter_price_max;
            $price_query['compare'] = '<=';
        }

        $meta_query[] = $price_query;
    }

    // Date filter
    if (!empty($filter_date_from)) {
        $meta_query[] = array(
        'key'     => 'start_date',
        'value'   => $filter_date_from,
        'compare' => '>=',
        'type'    => 'DATE',
        );
    }

    if (!empty($filter_date_to)) {
        $meta_query[] = array(
        'key'     => 'end_date',
        'value'   => $filter_date_to,
        'compare' => '<=',
        'type'    => 'DATE',
        );
    }

    // Time filter
    $has_time_from = !empty($filter_time_from);
    $has_time_to = !empty($filter_time_to);

    if ($has_time_from && $has_time_to) {
      // Both times given: class should fit within the time window
      // start_time >= from AND end_time <= to
        $meta_query[] = array(
        'key'     => 'start_time',
        'value'   => $filter_time_from,
        'compare' => '>=',
        'type'    => 'CHAR',
        );
        $meta_query[] = array(
        'key'     => 'end_time',
        'value'   => $filter_time_to,
        'compare' => '<=',
        'type'    => 'CHAR',
        );
    } elseif ($has_time_from) {
      // Only start time given: class start_time should be >= from
        $meta_query[] = array(
        'key'     => 'start_time',
        'value'   => $filter_time_from,
        'compare' => '>=',
        'type'    => 'CHAR',
        );
    } elseif ($has_time_to) {
      // Only end time given: class end_time should be <= to
        $meta_query[] = array(
        'key'     => 'end_time',
        'value'   => $filter_time_to,
        'compare' => '<=',
        'type'    => 'CHAR',
        );
    }

    // Location filters
    if (!empty($filter_city)) {
        $meta_query[] = array(
        'key'     => 'location_city',
        'value'   => $filter_city,
        'compare' => 'LIKE',
        );
    }

    if (!empty($meta_query)) {
        $meta_query['relation'] = 'AND';
        $args['meta_query'] = $meta_query;
    }

    // Full-text search (title, content, instructor name)
    if (!empty($search_query)) {
        global $wpdb;
        $like_term = '%' . $wpdb->esc_like($search_query) . '%';

      // Search in title, content, and instructor user display name
        $matching_ids = $wpdb->get_col($wpdb->prepare("
        SELECT DISTINCT p.ID FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm_instructor ON (p.ID = pm_instructor.post_id AND pm_instructor.meta_key = 'instructor')
        LEFT JOIN {$wpdb->users} u ON pm_instructor.meta_value = u.ID
        WHERE p.post_type = 'class' 
        AND p.post_status = 'publish'
        AND (
          p.post_title LIKE %s
          OR p.post_content LIKE %s
          OR u.display_name LIKE %s
        )
      ", $like_term, $like_term, $like_term));

        if (!empty($matching_ids)) {
          // If we already have meta/tax queries, we need to intersect
            if (isset($args['post__in'])) {
                $args['post__in'] = array_intersect($args['post__in'], $matching_ids);
            } else {
                $args['post__in'] = $matching_ids;
            }

            if (empty($args['post__in'])) {
                $args['post__in'] = array(0); // Force no results
            }
        } else {
          // No matches found - force empty result
            $args['post__in'] = array(0);
        }
    }

    $classes_query = new WP_Query($args);

    if ($classes_query->have_posts()) : ?>
      <div class="classes-grid">
        <?php while ($classes_query->have_posts()) :
            $classes_query->the_post(); ?>
          <div class="class-card">
            <div class="class-card-image <?php echo !has_post_thumbnail() ? 'no-image' : ''; ?>">
              <?php if (has_post_thumbnail()) : ?>
                <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail('medium', array('class' => 'class-thumbnail')); ?>
                </a>
              <?php else : ?>
                <a href="<?php the_permalink(); ?>" class="placeholder-link">
                  <div class="image-placeholder"></div>
                </a>
              <?php endif; ?>
              <?php
              // Display category/tag if available
                $categories = get_the_terms(get_the_ID(), 'category');
                if ($categories && !is_wp_error($categories)) :
                    $first_category = array_shift($categories);
                    ?>
                <span class="class-category"><?php echo esc_html($first_category->name); ?></span>
                <?php endif; ?>
            </div>

            <div class="class-card-content">
              <h2 class="class-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
              </h2>

              <?php if (has_excerpt()) : ?>
                <div class="class-excerpt">
                    <?php
                    $excerpt = get_the_excerpt();
                    $trimmed = strlen($excerpt) > 100 ? substr($excerpt, 0, 100) . '...' : $excerpt;
                    echo esc_html($trimmed);
                    ?>
                </div>
              <?php else : ?>
                <div class="class-excerpt">
                  <?php
                    $content = apply_filters('the_content', get_the_content());
                    $content = wp_strip_all_tags($content);
                    $trimmed = strlen($content) > 100 ? substr($content, 0, 100) . '...' : $content;
                    echo esc_html($trimmed);
                    ?>
                </div>
              <?php endif; ?>

              <?php
              // Display instructor using WordPress built-in meta
                $instructor_id = get_post_meta(get_the_ID(), 'instructor', true);
                if ($instructor_id) :
                    $instructor_user = get_userdata($instructor_id);
                    if ($instructor_user) :
                        ?>
                  <div class="class-instructor">
                    <i class="instructor-icon"></i>
                    <span>Instructor: <?php echo esc_html($instructor_user->display_name); ?></span>
                  </div>
                        <?php
                    endif;
                endif;

              // Display class level from taxonomy
                $class_levels = get_the_terms(get_the_ID(), 'class_level');
                if ($class_levels && !is_wp_error($class_levels)) :
                    $level = $class_levels[0];
                    ?>
                  <div class="class-level">
                    <span class="level-badge level-<?php echo esc_attr(strtolower($level->name)); ?>">
                      <?php echo esc_html($level->name); ?>
                    </span>
                  </div>
                <?php endif; ?>

              <?php
              // Display class dates using WordPress built-in meta
                $start_date = get_post_meta(get_the_ID(), 'start_date', true);
                $end_date = get_post_meta(get_the_ID(), 'end_date', true);
                if ($start_date && $end_date) :
                    $start_formatted = date('M j', strtotime($start_date));
                    $end_formatted = date('M j, Y', strtotime($end_date));
                    ?>
                  <div class="class-dates">
                    <i class="date-icon"></i>
                    <span><?php echo esc_html($start_formatted . ' - ' . $end_formatted); ?></span>
                  </div>
                <?php endif; ?>

              <div class="class-card-footer">
                <a href="<?php the_permalink(); ?>" class="class-link">
                  Learn More <span class="arrow">&rarr;</span>
                </a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>

        <?php
      // Pagination
        if ($classes_query->max_num_pages > 1) :
            $pagination_args = array(
            'total'        => $classes_query->max_num_pages,
            'current'      => $paged,
            'prev_text'    => '&laquo; Previous',
            'next_text'    => 'Next &raquo;',
            'type'         => 'list',
            );
            ?>
        <div class="classes-pagination">
            <?php echo paginate_links($pagination_args); ?>
        </div>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>

    <?php else : ?>
      <div class="no-classes-found">
        <?php if ($search_query) : ?>
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15.5 14H14.71L14.43 13.73C15.41 12.59 16 11.11 16 9.5C16 5.91 13.09 3 9.5 3C5.91 3 3 5.91 3 9.5C3 13.09 5.91 16 9.5 16C11.11 16 12.59 15.41 13.73 14.43L14 14.71V15.5L19 20.49L20.49 19L15.5 14ZM9.5 14C7.01 14 5 11.99 5 9.5C5 7.01 7.01 5 9.5 5C11.99 5 14 7.01 14 9.5C14 11.99 11.99 14 9.5 14Z" fill="#ccc"/>
          </svg>
          <h2>No Classes Found</h2>
          <p>We couldn't find any classes matching "<strong><?php echo esc_html($search_query); ?></strong>".</p>
          <p class="no-classes-hint">Try searching for something else or browse all available classes.</p>
          <a href="<?php echo esc_url(get_permalink()); ?>" class="btn-view-all">View All Classes</a>
        <?php else : ?>
          <h2>No Classes Available</h2>
          <p>There are currently no classes available. Please check back later.</p>
        <?php endif; ?>
      </div>
    <?php endif; ?>
      </div><!-- .classes-main-content -->
    </div><!-- .classes-content-wrapper -->
    </div>
  </div>
</div>

<?php get_template_part("parts/footer"); ?>
