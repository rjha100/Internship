<?php
/**
 * Sidebar for Classes Archive
 * Displays taxonomies: Subjects, Instructors, and Levels
 */
?>

<div class="widget-area">

  <?php
  // Subject taxonomy
  $subjects = get_terms([
      'taxonomy'   => 'subject',
      'hide_empty' => true,
  ]);

if (! empty($subjects) && ! is_wp_error($subjects)) : ?>
    <aside class="widget widget_categories">
      <h2 class="widget-title">Subjects</h2>
      <ul>
        <?php foreach ($subjects as $subject) : ?>
          <li>
            <a href="<?php echo esc_url(get_term_link($subject)); ?>">
              <?php echo esc_html($subject->name); ?>
              <span class="count">(<?php echo $subject->count; ?>)</span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </aside>
  <?php endif; ?>

  <?php
// Instructor taxonomy
$instructors = get_terms([
    'taxonomy'   => 'instructor',
    'hide_empty' => true,
]);

if (! empty($instructors) && ! is_wp_error($instructors)) : ?>
    <aside class="widget widget_categories">
      <h2 class="widget-title">Instructors</h2>
      <ul>
        <?php foreach ($instructors as $instructor) : ?>
          <li>
            <a href="<?php echo esc_url(get_term_link($instructor)); ?>">
              <?php echo esc_html($instructor->name); ?>
              <span class="count">(<?php echo $instructor->count; ?>)</span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </aside>
  <?php endif; ?>

  <?php
// Level taxonomy
$levels = get_terms([
    'taxonomy'   => 'class_level',
    'hide_empty' => true,
]);

if (! empty($levels) && ! is_wp_error($levels)) : ?>
    <aside class="widget widget_categories">
      <h2 class="widget-title">Class Levels</h2>
      <ul>
        <?php foreach ($levels as $level) : ?>
          <li>
            <a href="<?php echo esc_url(get_term_link($level)); ?>">
              <?php echo esc_html($level->name); ?>
              <span class="count">(<?php echo $level->count; ?>)</span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </aside>
  <?php endif; ?>

</div>
