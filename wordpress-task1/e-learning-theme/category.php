<?php get_template_part('parts/header'); ?>

<style>
.category-page {
  max-width: var(--max-width);
  margin: 0 auto;
  padding: 40px 20px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.category-header {
  text-align: center;
  margin-bottom: 50px;
}

.category-title {
  font-family: "Montserrat";
  font-size: 48px;
  color: var(--text-color);
  margin-bottom: 20px;
  line-height: 1.2;
}

.category-description {
  font-size: 18px;
  color: #666;
  max-width: 600px;
  margin: 0 auto;
}

.posts-grid-container {
  max-width: var(--max-width);
  margin: 0 auto;
  padding: 0 20px;
}

.posts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 30px;
  margin-bottom: 40px;
}

.post-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  overflow: hidden;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  display: flex;
  flex-direction: column;
}

.post-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
}

.post-thumbnail {
  display: block;
  overflow: hidden;
  background: #f5f5f5;
  aspect-ratio: 16 / 9;
}

.post-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.post-card:hover .post-image {
  transform: scale(1.05);
}

.post-content {
  padding: 24px;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.post-title {
  font-family: "Montserrat";
  font-size: 24px;
  font-weight: 700;
  line-height: 1.3;
  margin: 0 0 12px;
}

.post-title a {
  color: var(--text-color);
  text-decoration: none;
  transition: color 0.2s ease;
}

.post-title a:hover {
  color: #dc3636;
}

.post-meta {
  margin-bottom: 12px;
  font-size: 14px;
  color: #666;
}

.post-category a {
  color: #dc3636;
  text-decoration: none;
  font-weight: 600;
}

.post-category a:hover {
  text-decoration: underline;
}

.post-excerpt {
  color: #666;
  font-size: 16px;
  line-height: 1.6;
  margin-bottom: 16px;
  flex: 1;
}

.read-more {
  display: inline-block;
  color: #dc3636;
  font-weight: 700;
  text-decoration: none;
  font-size: 16px;
  transition: color 0.2s ease;
  align-self: flex-start;
}

.read-more:hover {
  color: #b52828;
  text-decoration: underline;
}

.no-posts {
  text-align: center;
  font-size: 18px;
  color: #666;
  padding: 60px 20px;
}

/* Pagination */
.pagination {
  grid-column: 1 / -1;
  display: flex;
  justify-content: center;
  margin-top: 20px;
}

.pagination .nav-links {
  display: flex;
  gap: 10px;
  align-items: center;
}

.pagination a,
.pagination .current {
  padding: 8px 16px;
  background: white;
  border: 1px solid #ddd;
  border-radius: 4px;
  color: var(--text-color);
  text-decoration: none;
  font-weight: 600;
  transition: all 0.2s ease;
}

.pagination a:hover {
  background: #dc3636;
  color: white;
  border-color: #dc3636;
}

.pagination .current {
  background: #dc3636;
  color: white;
  border-color: #dc3636;
}

@media (max-width: 768px) {
  .posts-grid {
    grid-template-columns: 1fr;
    gap: 20px;
  }

  .post-content {
    padding: 20px;
  }

  .post-title {
    font-size: 20px;
  }

  .category-title {
    font-size: 36px;
  }
}
</style>

<div class="content-wrapper">
  <div class="category-page">
    <header class="category-header">
      <h1 class="category-title"><?php single_cat_title(); ?></h1>
      <?php
      $category_description = category_description();
      if (!empty($category_description)) :
        echo '<p class="category-description">' . $category_description . '</p>';
      endif;
      ?>
    </header>

    <div class="posts-grid-container">
      <div class="posts-grid">
        <?php if (have_posts()) : ?>
          <?php while (have_posts()) : the_post(); ?>
            <article class="post-card">
              <?php if (has_post_thumbnail()) : ?>
                <a href="<?php the_permalink(); ?>" class="post-thumbnail">
                  <?php the_post_thumbnail('medium', array('class' => 'post-image')); ?>
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
          <p class="no-posts">No posts found in this category.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php get_template_part('parts/footer'); ?>
