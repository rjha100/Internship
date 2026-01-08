<?php get_template_part('parts/header'); ?>

<div class="content-wrapper">
  <div class="posts-grid-container">
    <div class="blog-layout">
      <aside class="blog-sidebar">
        <?php get_sidebar(); ?>
      </aside>
      
      <main class="blog-main">
        <div class="posts-grid">
          <?php if (have_posts()) : ?>
                <?php while (have_posts()) :
                    the_post(); ?>
              <article class="post-card">
                    <?php if (has_post_thumbnail()) : ?>
                  <a href="<?php the_permalink(); ?>" class="post-thumbnail">
                        <?php the_post_thumbnail('medium', array( 'class' => 'post-image' )); ?>
                  </a>
                    <?php endif; ?>
                
                <div class="post-content">
                  <h2 class="post-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                  </h2>
                  
                  <div class="post-meta">
                    <span class="post-category"><?php the_category(', '); ?></span>
                  </div>
                  
                  <div class="post-excerpt">
                    <?php the_excerpt(); ?>
                  </div>
                  
                  <a href="<?php the_permalink(); ?>" class="read-more">Read More</a>
                </div>
              </article>
                <?php endwhile; ?>
            
            <div class="pagination">
                <?php
                the_posts_pagination(array(
                'mid_size' => 2,
                'prev_text' => __('« Previous', 'e-learning-theme'),
                'next_text' => __('Next »', 'e-learning-theme'),
                ));
                ?>
            </div>
          <?php else : ?>
            <p class="no-posts">No posts found.</p>
          <?php endif; ?>
        </div>
      </main>
    </div>
  </div>
</div>

<?php get_template_part('parts/footer'); ?>
