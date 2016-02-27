<?php global $smof_data; ?>
<div class="header-v1">
	<header id="header">
		<div class="avada-row" style="padding-top:<?php echo $smof_data['margin_header_top']; ?>;padding-bottom:<?php echo $smof_data['margin_header_bottom']; ?>;" data-padding-top="<?php echo $smof_data['margin_header_top']; ?>" data-padding-bottom="<?php echo $smof_data['margin_header_bottom']; ?>">
			<div class="logo" data-margin-right="<?php echo $smof_data['margin_logo_right']; ?>" data-margin-left="<?php echo $smof_data['margin_logo_left']; ?>" data-margin-top="<?php echo $smof_data['margin_logo_top']; ?>" data-margin-bottom="<?php echo $smof_data['margin_logo_bottom']; ?>" style="margin-right:<?php echo $smof_data['margin_logo_right']; ?>;margin-top:<?php echo $smof_data['margin_logo_top']; ?>;margin-left:<?php echo $smof_data['margin_logo_left']; ?>;margin-bottom:<?php echo $smof_data['margin_logo_bottom']; ?>;">
				<a href="<?php echo home_url(); ?>">
					<img src="<?php echo $smof_data['logo']; ?>" alt="<?php bloginfo('name'); ?>" class="normal_logo" />
					<?php if($smof_data['logo_retina'] && $smof_data['retina_logo_width'] && $smof_data['retina_logo_height']): ?>
					<?php
					$pixels ="";
					if(is_numeric($smof_data['retina_logo_width']) && is_numeric($smof_data['retina_logo_height'])):
					$pixels ="px";
					endif; ?>
					<img src="<?php echo $smof_data["logo_retina"]; ?>" alt="<?php bloginfo('name'); ?>" style="width:<?php echo $smof_data["retina_logo_width"].$pixels; ?>;max-height:<?php echo $smof_data["retina_logo_height"].$pixels; ?>; height: auto !important" class="retina_logo" />
					<?php endif; ?>
				</a>
			</div>		
			<!-- Start Info & Search Box -->
			<div class="info-box-wrap">
				<div class="info-box">
			    <div class="header-phone-icon"><img src="http://meetingtomorrow.com/wp-content/uploads/2015/03/icon-phone.png" alt="Call Now"></div>
			    <div class="header-call-box">
			      <div class="header-call-now">Call Now (businesses only)</div>
			      <div class="header-phone-number"><a href="tel:1.877.633.8866" class="phone-number-link">1.877.633.8866</a></div>
			    </div>
			  </div>
			  <div class="main-nav-search"> 			    
			    <form role="search" id="searchform" class="searchform" method="get" action="/search/">
			      <div class="search-table">
			        <div class="search-field">
			          <input type="text" value="" name="q" id="q" autocomplete="off" class="s field"  />
			        </div>
			        <div class="search-button">
			          <input type="submit" class="searchsubmit" value="&#xf002;" />
			        </div>
			      </div>
			    </form>
			    <!-- Google Custom Search form code from: https://cse.google.com/ -->			    
			    <script>
					  function() {
					    var cx = '007152895395390622885:8lcdi7qbvek';
					    var gcse = document.createElement('script');
					    gcse.type = 'text/javascript';
					    gcse.async = true;
					    gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
					        '//cse.google.com/cse.js?cx=' + cx;
					    var s = document.getElementsByTagName('script')[0];
					    s.parentNode.insertBefore(gcse, s);
					  }();
					</script>					
					<!-- This will display Google's proprietary search box. Not currently used on the site.
					<gcse:searchbox-only></gcse:searchbox-only>-->							   
			  </div>	
		  </div>
		  <!-- End Info & Search Box -->
		  
			<?php if($smof_data['ubermenu']): ?>
			<nav id="nav-uber" class="clearfix">
			<?php else: ?>
			<nav id="nav" class="nav-holder" data-height="<?php echo $smof_data['nav_height']; ?>px">
				<a href="<?php get_home_url(); ?>/careers" class="hiring-bubble"><span class="hiring-bubble-first-letter">W</span>e're hiring!</a>
				<div class="hiring-bubble-arrow"></div>
			<?php endif; ?>
				<?php get_template_part('framework/headers/header-main-menu-2'); ?>
			</nav>
			<?php if($smof_data['mobile_menu_design'] == 'modern' && ! $smof_data['ubermenu']): ?>
			<div class="mobile-menu-icons">
				<a href="#" class="fusionicon fusionicon-bars"></a>
				<?php if( class_exists('Woocommerce') && $smof_data['woocommerce_cart_link_main_nav'] ): ?>
				<a href="<?php echo get_permalink(get_option('woocommerce_cart_page_id')); ?>" class="fusionicon fusionicon-shopping-cart"></a>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<?php if(tf_checkIfMenuIsSetByLocation('main_navigation') && $smof_data['mobile_menu_design'] == 'classic' && ! $smof_data['ubermenu']): ?>
			<div class="mobile-nav-holder main-menu"></div>
			<?php endif; ?>
		</div>
		<div class="mobile-info-box">
		  <div class="mobile-phone-box">
		  	<img src="http://meetingtomorrow.com/wp-content/uploads/2015/03/icon-phone.png" alt="" class="mobile-icon-phone">
		  	<a class="header-phone-link" href="tel:1.877.633.8866" class="phone-number-link">1.877.633.8866</a>
		  </div>
		  <div class="mobile-chat-box">		    
		  	<img src="http://meetingtomorrow.com/wp-content/uploads/2015/03/icon-chat.png" alt="" class="mobile-icon-chat">
		  	<div data-id="e1f6108b40" class="livechat_button"><a class="white" href="http://www.livechatinc.com/?partner=lc_3484832&amp;utm_source=chat_button">Chat Live</a></div>
		  </div>
		</div>
	</header>
	<?php if(tf_checkIfMenuIsSetByLocation('main_navigation') && $smof_data['mobile_menu_design'] == 'modern'  && ! $smof_data['ubermenu']): ?>
	<div class="mobile-nav-holder main-menu"></div>
	<?php endif; ?>
</div>