<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label class="screen-reader-text" for="site-search">Search for:</label>
    <div class="search-input-wrap">
        <input type="search" id="site-search" class="search-field" placeholder="Search the site..." value="<?php echo esc_attr(get_search_query()); ?>" name="s" />
        <button type="submit" class="search-submit" aria-label="Search">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        </button>
    </div>
</form>
