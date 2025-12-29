<?php
/*
 * Archive template for `class` CPT
 */

get_template_part( 'parts/header' );
?>

<div class="content-wrapper">
  <div class="posts-grid-container">
    <div class="blog-layout">
      <aside class="blog-sidebar">
        <?php get_sidebar( 'classes' ); ?>
      </aside>

      <main class="blog-main">
        <div class="posts-grid">
          <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
              <article class="post-card">
                <?php if ( has_post_thumbnail() ) : ?>
                  <a href="<?php the_permalink(); ?>" class="post-thumbnail">
                    <?php the_post_thumbnail( 'medium', array( 'class' => 'post-image' ) ); ?>
                  </a>
                <?php endif; ?>

                <div class="post-content">
                  <h2 class="post-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                  </h2>

                  <div class="post-meta">
                    <?php
                    // Show taxonomy terms (subject, instructor, class_level)
                    $subjects = get_the_term_list( get_the_ID(), 'subject', '', ', ', '' );
                    $instructors = get_the_term_list( get_the_ID(), 'instructor', '', ', ', '' );
                    $levels = get_the_term_list( get_the_ID(), 'class_level', '', ', ', '' );

                    if ( $subjects ) {
                      echo '<span class="post-category">' . $subjects . '</span>';
                    }
                    if ( $instructors ) {
                      echo '<span class="post-instructor">' . $instructors . '</span>';
                    }
                    if ( $levels ) {
                      echo '<span class="post-level">' . $levels . '</span>';
                    }
                    ?>
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
              the_posts_pagination( array(
                'mid_size'  => 2,
                'prev_text' => __( '« Previous', 'e-learning-theme' ),
                'next_text' => __( 'Next »', 'e-learning-theme' ),
              ) );
              ?>
            </div>

          <?php else : ?>
            <p class="no-posts">No classes found.</p>
          <?php endif; ?>
        </div>
      </main>
    </div>
  </div>
</div>

<?php get_template_part( 'parts/footer' );
