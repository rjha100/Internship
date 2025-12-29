<?php
get_template_part('parts/header');
?>

<style>
/* 404 page styles */
.error-404-page {
	display: flex;
	align-items: center;
	justify-content: center;
	padding: 40px 20px;
}

.error-404-inner {
	max-width: 920px;
	width: 100%;
	text-align: center;
	background: white;
	border-radius: 12px;
	padding: 48px;
	box-shadow: 0 8px 30px rgba(0,0,0,0.06);
}

.error-404-title {
	font-family: "Montserrat";
	font-size: 64px;
	margin: 0 0 12px;
	color: var(--text-color);
	font-weight: 900;
}

.error-404-subtitle {
	font-size: 20px;
	color: #666;
	margin-bottom: 18px;
}

.error-404-msg {
	font-size: 18px;
	color: var(--text-color);
	margin-bottom: 24px;
}

.error-actions {
	display: flex;
	gap: 12px;
 	justify-content: center;
 	align-items: center;
 	margin-bottom: 28px;
 	flex-wrap: wrap;
}

/* Improved search/form layout */
.error-actions .search-form {
 	display: inline-block;
 	min-width: 280px;
}

.error-actions .search-input-wrap {
 	display: inline-flex;
 	align-items: center;
 	background: #fff;
 	border-radius: 8px;
 	box-shadow: 0 2px 10px rgba(0,0,0,0.04);
 	overflow: hidden;
}

.error-actions .search-field {
 	border: none;
 	padding: 10px 12px;
 	outline: none;
 	width: 260px;
 	font-size: 15px;
 	color: var(--text-color);
}

.error-actions .search-field::placeholder { color: #999; }

.error-actions .search-submit {
 	background: #dc3636;
 	color: #fff;
 	border: none;
 	padding: 10px 12px;
 	display: inline-flex;
 	align-items: center;
 	justify-content: center;
 	cursor: pointer;
}

.error-actions .search-submit svg { width: 16px; height: 16px; display: block; }

/* Return Home button styling */
.error-actions .btn-home {
 	background: #dc3636;
 	color: #fff;
 	text-decoration: none;
 	padding: 10px 18px;
 	border-radius: 8px;
 	font-weight: 700;
 	box-shadow: 0 4px 14px rgba(220,54,54,0.18);
}

.error-actions .btn-home:hover,
.error-actions .search-submit:hover { opacity: 0.95; }

.error-widgets {
	display: flex;
	gap: 24px;
	justify-content: center;
	margin-top: 20px;
	flex-wrap: wrap;
}

.error-widgets .widget {
	background: #fafafa;
	padding: 18px;
	border-radius: 8px;
	min-width: 220px;
	max-width: 320px;
	box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}

.error-widgets .widget-title {
	font-weight: 700;
	margin-bottom: 12px;
}

.error-widgets ul { list-style: none; padding: 0; margin: 0; }
.error-widgets li { margin-bottom: 8px; }
.error-widgets a { color: var(--text-color); text-decoration: none; }
.error-widgets a:hover { color: #dc3636; }

@media (max-width: 780px) {
	.error-404-inner { padding: 28px; }
	.error-404-title { font-size: 44px; }
	.error-actions input[type="search"] { width: 160px; }
	.error-widgets { flex-direction: column; align-items: stretch; }
}
</style>

<div class="content-wrapper">
	<div class="error-404-page">
		<div class="error-404-inner">
			<h1 class="error-404-title">404</h1>
			<div class="error-404-subtitle">Page not found</div>
			<p class="error-404-msg">Sorry — we couldn't find the page you were looking for. Try searching below or explore recent posts and popular categories.</p>

			<div class="error-actions">
				<?php get_template_part('parts/searchform'); ?>
				<a class="btn-home" href="<?php echo esc_url( home_url('/') ); ?>" aria-label="Return to homepage">Return Home</a>
			</div>

			<div class="error-widgets">
				<div class="widget">
					<h3 class="widget-title">Recent Posts</h3>
					<ul>
						<?php
						$recent = wp_get_recent_posts(array('numberposts'=>5,'post_status'=>'publish'));
						foreach ($recent as $p) {
							echo '<li><a href="' . esc_url( get_permalink($p['ID']) ) . '">' . esc_html( $p['post_title'] ) . '</a></li>';
						}
						wp_reset_query();
						?>
					</ul>
				</div>

				<div class="widget">
					<h3 class="widget-title">Popular Categories</h3>
					<ul>
						<?php wp_list_categories(array('orderby'=>'count','order'=>'DESC','number'=>8,'title_li'=>'')); ?>
					</ul>
				</div>

				<div class="widget">
					<h3 class="widget-title">Helpful Links</h3>
					<ul>
						<li><a href="<?php echo esc_url( home_url('/') ); ?>">Home</a></li>
						<li><a href="<?php echo esc_url( get_permalink( get_option('page_for_posts') ) ); ?>">Blog</a></li>
						<li><a href="<?php echo esc_url( site_url('/contact') ); ?>">Contact Us</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
get_template_part('parts/footer');
?>
