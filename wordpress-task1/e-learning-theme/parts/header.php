<!doctype html>
<html <?php language_attributes() ?>>
  <head>
    <meta charset="<?php bloginfo("charset")?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
      rel="stylesheet"
    />
    <title><?php bloginfo('name') ?><?php wp_title(); ?> <?php if (is_front_page()) {echo '| '; bloginfo('description');} ?></title>
    <link rel="stylesheet" href=<?=get_template_directory_uri()?>/style.css />
    <?php wp_head()?>
  </head>
  <body <?php body_class() ?>>
    <?php wp_body_open() ?>
    <header>
      <div class="header">
        <div class="header-left">
          <a href="<?php echo esc_url(home_url('/')); ?>">
            <img
              src="<?php header_image()?>"
              alt="<?php bloginfo('name'); ?>"
              width="125px"
              height="125px"
            >
          </a>
        </div>

        <input type="checkbox" id="nav-toggle" class="menu-toggle" />
        <label for="nav-toggle" class="hamburger">
          <span></span>
          <span></span>
          <span></span>
        </label>

        <div class="header-right">
          <div class="header-top">
            <button class="util-link" id="search-toggle" aria-label="Open search">
              <span class="util-icon util-icon--search">
                <svg viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M12.5 11H11.71L11.43 10.73C12.41 9.59 13 8.11 13 6.5C13 2.91 10.09 0 6.5 0C2.91 0 0 2.91 0 6.5C0 10.09 2.91 13 6.5 13C8.11 13 9.59 12.41 10.73 11.43L11 11.71V12.5L16 17.49L17.49 16L12.5 11ZM6.5 11C4.01 11 2 8.99 2 6.5C2 4.01 4.01 2 6.5 2C8.99 2 11 4.01 11 6.5C11 8.99 8.99 11 6.5 11Z"
                    fill="#434343"
                  />
                </svg>
              </span>
              <span>Search</span>
            </button>

            <button class="util-link">
              <span class="util-icon util-icon--user">
                <svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M10 0C4.48 0 0 4.48 0 10C0 15.52 4.48 20 10 20C15.52 20 20 15.52 20 10C20 4.48 15.52 0 10 0ZM10 3C11.66 3 13 4.34 13 6C13 7.66 11.66 9 10 9C8.34 9 7 7.66 7 6C7 4.34 8.34 3 10 3ZM10 17.2C7.5 17.2 5.29 15.92 4 13.98C4.03 11.99 8 10.9 10 10.9C11.99 10.9 15.97 11.99 16 13.98C14.71 15.92 12.5 17.2 10 17.2Z"
                    fill="black"
                  />
                </svg>
              </span>
              <span>Sign in</span>
            </button>

            <button class="util-link">
              <span class="util-icon util-icon--cart">
                <svg viewBox="0 0 16 20" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M14 4H12C12 1.79 10.21 0 8 0C5.79 0 4 1.79 4 4H2C0.9 4 0 4.9 0 6V18C0 19.1 0.9 20 2 20H14C15.1 20 16 19.1 16 18V6C16 4.9 15.1 4 14 4ZM6 8C6 8.55 5.55 9 5 9C4.45 9 4 8.55 4 8V6H6V8ZM8 2C9.1 2 10 2.9 10 4H6C6 2.9 6.9 2 8 2ZM12 8C12 8.55 11.55 9 11 9C10.45 9 10 8.55 10 8V6H12V8Z"
                    fill="#434343"
                  />
                </svg>
              </span>
              <span>(0)</span>
            </button>
          </div>

          <div class="header-bottom">
            <?php wp_nav_menu(
            array(
              'theme_location'=> 'primary_menu',
              'menu_class' => 'nav-links')
            )?>
            <div class="nav-cta">
              <a href="<?php echo esc_url(home_url('/classes')); ?>" class="cta">Find a Class</a>
            </div>
          </div>
        </div>

      </div>
    </header>

    <!-- Search Modal -->
    <div id="search-modal" class="search-modal" aria-hidden="true">
      <div class="search-modal-overlay" id="search-modal-overlay"></div>
      <div class="search-modal-content">
        <button class="search-modal-close" id="search-modal-close" aria-label="Close search">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
        <div class="search-modal-inner">
          <h2 class="search-modal-title">Search</h2>
          <?php get_template_part('parts/searchform'); ?>
        </div>
      </div>
    </div>

    <script>
    // Search modal functionality
    (function() {
      const searchToggle = document.getElementById('search-toggle');
      const searchModal = document.getElementById('search-modal');
      const searchModalClose = document.getElementById('search-modal-close');
      const searchModalOverlay = document.getElementById('search-modal-overlay');
      const searchInput = document.getElementById('site-search');

      function openSearchModal() {
        searchModal.classList.add('active');
        searchModal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        // Focus on search input after modal opens
        setTimeout(() => searchInput?.focus(), 100);
      }

      function closeSearchModal() {
        searchModal.classList.remove('active');
        searchModal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
      }

      searchToggle?.addEventListener('click', openSearchModal);
      searchModalClose?.addEventListener('click', closeSearchModal);
      searchModalOverlay?.addEventListener('click', closeSearchModal);

      // Close on Escape key
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && searchModal.classList.contains('active')) {
          closeSearchModal();
        }
      });
    })();
    </script>
