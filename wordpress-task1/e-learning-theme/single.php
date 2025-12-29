<?php get_template_part('parts/header'); ?>

<style>
.single-post-page {
  max-width: var(--max-width);
  margin: 0 auto;
  padding: 40px 20px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.single-post-hero {
  text-align: center;
  margin-bottom: 40px;
}

.single-post-title {
  font-family: "Montserrat";
  font-size: 48px;
  color: var(--text-color);
  margin-bottom: 20px;
  line-height: 1.2;
}

.single-post-meta {
  color: #666;
  font-size: 16px;
  margin-bottom: 30px;
}

.single-post-meta span {
  margin-right: 20px;
}

.single-post-featured-image {
  width: 100%;
  max-width: 800px;
  height: auto;
  border-radius: 8px;
  margin: 0 auto 30px;
  display: block;
}

.single-post-content {
  font-size: 18px;
  line-height: 1.8;
  color: var(--text-color);
  margin-bottom: 40px;
  padding: 20px;
  background: #fafafa;
  border-radius: 8px;
}

.single-post-content h2,
.single-post-content h3,
.single-post-content h4 {
  font-family: "Montserrat";
  margin-top: 30px;
  margin-bottom: 15px;
  color: var(--text-color);
}

.single-post-content p {
  margin-bottom: 20px;
}

.single-post-content ul,
.single-post-content ol {
  margin-left: 20px;
  margin-bottom: 20px;
}

.single-post-content li {
  margin-bottom: 10px;
}

.single-post-content img {
  max-width: 100%;
  height: auto;
  border-radius: 4px;
  margin: 20px 0;
}

.single-post-tags {
  margin-top: 30px;
  padding-top: 20px;
  border-top: 1px solid #eee;
}

.single-post-tags .pill {
  display: inline-block;
  margin-right: 10px;
  margin-bottom: 10px;
}

.comments-section {
  margin-top: 50px;
  padding-top: 30px;
  border-top: 1px solid #eee;
}

.comments-section h3 {
  font-family: "Montserrat";
  font-size: 32px;
  color: var(--text-color);
  margin-bottom: 20px;
}

/* Comments List */
.comment-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.comment {
  margin-bottom: 30px;
  padding: 20px;
  background: #f9f9f9;
  border-radius: 8px;
  border-left: 4px solid #dc3636;
}

.comment .children {
  margin-left: 40px;
  margin-top: 20px;
}

.comment .children .comment {
  background: #f0f0f0;
  border-left-color: #b52828;
}

.comment-author {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

.comment-author .avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  margin-right: 15px;
}

.comment-author .fn {
  font-weight: 600;
  color: var(--text-color);
  font-size: 16px;
}

.comment-meta {
  font-size: 14px;
  color: #666;
  margin-bottom: 10px;
}

.comment-meta a {
  color: #666;
  text-decoration: none;
}

.comment-meta a:hover {
  color: #dc3636;
}

.comment-content {
  font-size: 16px;
  line-height: 1.6;
  color: var(--text-color);
}

.comment-content p {
  margin-bottom: 10px;
}

.reply {
  margin-top: 15px;
}

.comment-reply-link {
  background: #dc3636;
  color: white;
  padding: 6px 12px;
  border-radius: 4px;
  text-decoration: none;
  font-size: 14px;
  font-weight: 600;
  transition: background 0.3s ease;
}

.comment-reply-link:hover {
  background: #b52828;
}

/* Comment Form */
.comment-form {
  margin-top: 40px;
  padding: 30px;
  background: #f9f9f9;
  border-radius: 8px;
}

.comment-form h3 {
  font-family: "Montserrat";
  font-size: 24px;
  color: var(--text-color);
  margin-bottom: 20px;
}

.comment-form-comment,
.comment-form-author,
.comment-form-email,
.comment-form-url {
  margin-bottom: 20px;
}

.comment-form label {
  display: block;
  font-weight: 600;
  color: var(--text-color);
  margin-bottom: 5px;
}

.comment-form input[type="text"],
.comment-form input[type="email"],
.comment-form input[type="url"],
.comment-form textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 16px;
  font-family: inherit;
  box-sizing: border-box;
}

.comment-form textarea {
  min-height: 120px;
  resize: vertical;
}

.comment-form input:focus,
.comment-form textarea:focus {
  outline: none;
  border-color: #dc3636;
  box-shadow: 0 0 0 2px rgba(220, 54, 54, 0.1);
}

.form-submit {
  margin-top: 20px;
}

.comment-form input[type="submit"] {
  background: #dc3636;
  color: white;
  padding: 12px 24px;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s ease;
}

.comment-form input[type="submit"]:hover {
  background: #b52828;
}

/* No Comments */
.no-comments {
  text-align: center;
  color: #666;
  font-style: italic;
  padding: 40px 0;
}

@media (max-width: 768px) {
  .single-post-title {
    font-size: 36px;
  }
  
  .single-post-content {
    font-size: 16px;
  }
  
  .comment {
    padding: 15px;
  }
  
  .comment .children {
    margin-left: 20px;
  }
  
  .comment-author .avatar {
    width: 40px;
    height: 40px;
  }
  
  .comment-form {
    padding: 20px;
  }
}
</style>

<div class="content-wrapper">
  <div class="single-post-page">
    <?php if (have_posts()) : ?>
      <?php while (have_posts()) : the_post(); ?>
        <article class="single-post">
          <header class="single-post-hero">
            <h1 class="single-post-title"><?php the_title(); ?></h1>
            <div class="single-post-meta">
              <span>By <?php the_author(); ?></span>
              <span>On <?php the_date(); ?></span>
              <span>In <?php the_category(', '); ?></span>
            </div>
          </header>

          <?php if (has_post_thumbnail()) : ?>
            <img src="<?php the_post_thumbnail_url('large'); ?>" alt="<?php the_title(); ?>" class="single-post-featured-image" />
          <?php endif; ?>

          <div class="single-post-content">
            <?php the_content(); ?>
          </div>

          <?php if (has_tag()) : ?>
            <div class="single-post-tags">
              <?php the_tags('', ' ', ''); ?>
            </div>
          <?php endif; ?>
        </article>

        <?php if (comments_open() || get_comments_number()) : ?>
          <div class="comments-section">
            <?php comments_template(); ?>
          </div>
        <?php endif; ?>
      <?php endwhile; ?>
    <?php else : ?>
      <p>No post found.</p>
    <?php endif; ?>
  </div>
</div>

<?php get_template_part('parts/footer'); ?>
