<?php get_header(); ?>
	<div id="content" class="full-width">
		<div id="post-404page">
			<div class="post-content">
				<div class="fusion-title title">
					<h2 class="title-heading-left"><?php echo __('Sorry, this page could not be found', 'Avada'); ?></h2><div class="title-sep-container"><div class="title-sep sep-double"></div></div>
				</div>
				<div class="fusion-clearfix"></div>
				<div class="error_page">
					<div class="fusion-one-third one_third fusion-column spacing-yes">						
						<p class="top-mar-10">This page appears to be deleted, moved, or missing. We'll send out a search squad to find it immediately, but in the meantime, feel free to use the links or search bar to find what you need.</p>
						<div class="error-message top-mar-80">404</div>
					</div>
					<div class="fusion-one-third one_third fusion-column spacing-yes useful_links">
						<h3 class="top-mar-10"><?php echo __('Here are some useful links:', 'Avada'); ?></h3>

						<?php
						if( $smof_data['checklist_circle'] ) {
							$circle_class = 'circle-yes';
						} else {
							$circle_class = 'circle-no';
						}
						wp_nav_menu(array('theme_location' => 'main_navigation', 'depth' => 1, 'container' => false, 'menu_id' => 'mavin_navigation', 'menu_class' => 'error-menu list-icon list-icon-arrow ' . $circle_class )); ?>
					</div>
					<div class="fusion-one-third one_third fusion-column spacing-yes last">
						<h3 class="top-mar-10"><?php echo __('Search Our Website', 'Avada'); ?></a></h3>
						<p><?php echo __('Can\'t find what you need? Take a moment and do a search below:', 'Avada'); ?></p>
						<div class="search-page-search-form">
							<form role="search" id="searchform" class="searchform" method="get" action="/search/">
								<div class="search-table">
									<div class="search-field">
										<input type="text" value="" name="q" id="q" autocomplete="off" class="s field" placeholder="Search" />
									</div>
									<div class="search-button">
										<input type="submit" class="searchsubmit" value="&#xf002;" />
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Custom Google Analytics Tracking Code -->
	<script type="text/javascript">
		_gaq.push(["_trackEvent", "404", location.pathname + location.search, document.referrer, 0, true]);
	</script>
<?php get_footer(); ?>