<?php
/**
 * Sidebar Template
 */
?>

<div id="sidebar" class="sidebar">
  <?php if (is_active_sidebar('blog-sidebar')) : ?>
    <?php dynamic_sidebar('blog-sidebar'); ?>
  <?php else : ?>
    <div class="widget">
      <h3 class="widget-title">Categories</h3>
      <ul>
        <?php wp_list_categories(array('title_li' => '')); ?>
      </ul>
    </div>

    <div class="widget">
      <h3 class="widget-title">Recent Posts</h3>
      <ul>
        <?php
        $recent_posts = wp_get_recent_posts(array(
          'numberposts' => 5,
          'post_status' => 'publish'
        ));
        foreach ($recent_posts as $post) :
          echo '<li><a href="' . get_permalink($post['ID']) . '">' . $post['post_title'] . '</a></li>';
        endforeach;
        wp_reset_query();
        ?>
      </ul>
    </div>

    <div class="widget">
      <h3 class="widget-title">Archives</h3>
      <ul>
        <?php wp_get_archives(array('type' => 'monthly', 'limit' => 12)); ?>
      </ul>
    </div>
  <?php endif; ?>
</div>
