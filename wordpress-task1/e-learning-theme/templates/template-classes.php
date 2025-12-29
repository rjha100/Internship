<?php
/*
 * Template Name: Classes
 * Description: Page template that lists `class` CPT entries. Assign this template to a Page with slug `classes`.
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
        <header class="page-header">
          <h1 class="page-title"><?php the_title(); ?></h1>
        </header>

        <div class="posts-grid">
          <?php
          $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
          $args = array(
            'post_type'      => 'class',
            'posts_per_page' => 10,
            'paged'          => $paged,
          );

          $classes_query = new WP_Query( $args );

          if ( $classes_query->have_posts() ) :
            while ( $classes_query->have_posts() ) : $classes_query->the_post(); ?>

              <article class="post-card">
                <?php if ( has_post_thumbnail() ) : ?>
                  <a href="<?php the_permalink(); ?>" class="post-thumbnail">
                    <?php the_post_thumbnail( 'medium', array( 'class' => 'post-image' ) ); ?>
                  </a>
                <?php endif; ?>

                <div class="post-content">
                  <h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

                  <div class="post-meta">
                    <?php
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

                  <div class="post-excerpt"><?php the_excerpt(); ?></div>
                  <a href="<?php the_permalink(); ?>" class="read-more">Read More</a>
                </div>
              </article>

            <?php endwhile; ?>

            <div class="pagination">
              <?php
              echo paginate_links( array(
                'total'   => $classes_query->max_num_pages,
                'current' => $paged,
              ) );
              ?>
            </div>

          <?php else : ?>
            <p class="no-posts">No classes found.</p>
          <?php endif; wp_reset_postdata(); ?>
        </div>
      </main>
    </div>
  </div>
</div>

<?php get_template_part( 'parts/footer' );
