		</div>
	</div>
	<?php global $smof_data, $social_icons; ?>
	<?php
	$object_id = get_queried_object_id();
	if((get_option('show_on_front') && get_option('page_for_posts') && is_home()) ||
		(get_option('page_for_posts') && is_archive() && !is_post_type_archive()) && !(is_tax('product_cat') || is_tax('product_tag')) || (get_option('page_for_posts') && is_search())) {
		$c_pageID = get_option('page_for_posts');
	} else {
		if(isset($object_id)) {
			$c_pageID = $object_id;
		}

		if(class_exists('Woocommerce')) {
			if(is_shop() || is_tax('product_cat') || is_tax('product_tag')) {
				$c_pageID = get_option('woocommerce_shop_page_id');
			}
		}
	}
	?>
	<?php if(!is_page_template('blank.php')): ?>
	<?php if( ($smof_data['footer_widgets'] && get_post_meta($c_pageID, 'pyre_display_footer', true) != 'no') ||
			  ( ! $smof_data['footer_widgets'] && get_post_meta($c_pageID, 'pyre_display_footer', true) == 'yes') ): ?>
	<footer class="footer-area">
		<div class="avada-row">
			<div class="fusion-columns row fusion-columns-<?php echo $smof_data['footer_widgets_columns']; ?> columns columns-<?php echo $smof_data['footer_widgets_columns']; ?>">
				<?php 
				$column_width = 12 / $smof_data['footer_widgets_columns']; 
				if( $smof_data['footer_widgets_columns'] == '5' ) {
					$column_width = 2;
				}
				?>
			
				<?php if( $smof_data['footer_widgets_columns'] >= 1 ): ?>
				<div class="fusion-column col <?php echo sprintf( 'col-lg-%s col-md-%s col-sm-%s', $column_width, $column_width, $column_width ); ?> ">
				<?php
				if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer Widget 1')):
				endif;
				?>
				</div>
				<?php endif; ?>
				
				<?php if( $smof_data['footer_widgets_columns'] >= 2 ): ?>
				<div class="fusion-column col <?php echo sprintf( 'col-lg-%s col-md-%s col-sm-%s', $column_width, $column_width, $column_width ); ?>">
				<?php
				if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer Widget 2')):
				endif;
				?>
				</div>
				<?php endif; ?>
				
				<?php if( $smof_data['footer_widgets_columns'] >= 3 ): ?>
				<div class="fusion-column col <?php echo sprintf( 'col-lg-%s col-md-%s col-sm-%s', $column_width, $column_width, $column_width ); ?>">
				<?php
				if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer Widget 3')):
				endif;
				?>
				</div>
				<?php endif; ?>
				
				<?php if( $smof_data['footer_widgets_columns'] >= 4 ): ?>
				<div class="fusion-column col last <?php echo sprintf( 'col-lg-%s col-md-%s col-sm-%s', $column_width, $column_width, $column_width ); ?>">
				<?php
				if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer Widget 4')):
				endif;
				?>
				</div>
				<?php endif; ?>

				<?php if( $smof_data['footer_widgets_columns'] >= 5 ): ?>
				<div class="fusion-column col last <?php echo sprintf( 'col-lg-%s col-md-%s col-sm-%s', $column_width, $column_width, $column_width ); ?>">
				<?php
				if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer Widget 5')):
				endif;
				?>
				</div>
				<?php endif; ?>

				<?php if( $smof_data['footer_widgets_columns'] >= 6 ): ?>
				<div class="fusion-column col last <?php echo sprintf( 'col-lg-%s col-md-%s col-sm-%s', $column_width, $column_width, $column_width ); ?>">
				<?php
				if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer Widget 6')):
				endif;
				?>
				</div>
				<?php endif; ?>
				<div class="fusion-clearfix"></div>
			</div>
		</div>
	</footer>
	<?php endif; ?>
	<?php if( ($smof_data['footer_copyright'] && get_post_meta($c_pageID, 'pyre_display_copyright', true) != 'no') ||
			  ( ! $smof_data['footer_copyright'] && get_post_meta($c_pageID, 'pyre_display_copyright', true) == 'yes') ): ?>
	<footer id="footer">
		<div class="avada-row">
			<div class="copyright-area-content">
				<div class="copyright">
					<div><?php echo do_shortcode( $smof_data['footer_text'] ); ?></div>
				</div>
				<?php if($smof_data['icons_footer']) : ?>				
				<div class="fusion-social-links-footer">
					<?php 
					$footer_soical_icon_options = array (
						'position'			=> 'footer',
						'icon_colors' 		=> $smof_data['footer_social_links_icon_color'],
						'box_colors' 		=> $smof_data['footer_social_links_box_color'],
						'icon_boxed' 		=> $smof_data['footer_social_links_boxed'],
						'icon_boxed_radius' => $smof_data['footer_social_links_boxed_radius'],
						'tooltip_placement'	=> $smof_data['footer_social_links_tooltip_placement'],
						'linktarget'		=> $smof_data['social_icons_new']
					);

					
					echo $social_icons->render_social_icons($footer_soical_icon_options); ?>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</footer>
	<?php endif; ?>
	<?php endif; ?>
	</div><!-- wrapper -->
	
	<?php if( ( ( $smof_data['layout'] == 'Boxed' && get_post_meta( $c_pageID, 'pyre_page_bg_layout', true ) == 'default' ) || get_post_meta( $c_pageID, 'pyre_page_bg_layout', true ) == 'boxed' ) && $smof_data['header_position'] != 'Top' ): ?>
	</div>
	<?php endif; ?>	

	<?php //include_once('style_selector.php'); ?>
	
	<!-- W3TC-include-js-head -->

	<?php wp_footer(); ?>

	<?php echo $smof_data['space_body']; ?> 

	<!-- Quote Form Popup -->
	<!-- Quote Tab -->	
	<a class="fusion-modal-text-link quote-tab-content" data-toggle="modal" data-target=".quote_tab_popup" href="#">
		<div class="quote-tab">
			<div class="quote-tab-img-wrap"><img src="https://meetingtomorrow.com/wp-content/uploads/2015/07/quote-tab.png" alt="Get a Free Quote!"></div> 
		</div>
		<div class="quote-tab-mobile">
			<div class="quote-tab-img-wrap"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/tab-quote-mobile.png" alt="Get a Free Quote!"></div> 
		</div>
	</a>
  <!-- Quote Form -->
	<div class="fusion-modal modal fade modal-2 quote_tab_popup" tabindex="-1" role="dialog" aria-labelledby="modal-heading-2" aria-hidden="true">
		<style type="text/css">.modal-2 .modal-header, .modal-2 .modal-footer{border-color:#ebebeb;}</style>
		<div class="modal-dialog modal-sm">
			<div class="modal-content fusion-modal-content" style="background-color:#f6f6f6">
				<div class="modal-header">
					<button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title" id="modal-heading-2" data-dismiss="modal" aria-hidden="true">Get a Quick Quote</h3>
				</div>
				<div class="modal-body">
					<iframe id="nsIframeSidetab" src="<?php echo 'https://forms.na2.netsuite.com/app/site/crm/externalleadpage.nl?compid=3373305&formid=65&h=AACffht_oVPvzBS2Q5xRFMm3YXYganqAV-g%3D&url=' . rawurlencode( get_permalink()); ?>" width="100%" height="350" frameborder="0" scrolling="no"></iframe>

					<!-- Form with hidden fields for GA reporting -->
					<form id="ga-tracking-form" method="POST" name='contactform' onSubmit=""> 
					    <input type='hidden' name='source' /> 
					    <input type='hidden' name='medium' /> 
					    <input type='hidden' name='term' /> 
					    <input type='hidden' name='content' /> 
					    <input type='hidden' name='campaign' /> 
					    <input type='hidden' name='gclid' />
					    <input type='hidden' name='segment' />
					    <input type='hidden' name='numVisits' />
					</form>
				</div>
				<div class="modal-footer">
					<a class="fusion-button button-default button-small button default small" data-dismiss="modal">Close</a>
				</div>
			</div>
		</div>

	</div>
	<!-- End Quote Form Popup -->

  <!-- Pricelist Popup Form -->
  <!-- Pricelist Tab -->	
  <a class="fusion-modal-text-link" data-toggle="modal" data-target=".pricelist_tab_popup" href="#">
		<div class="pricelist-tab">
			<div class="pricelist-tab-img-wrap"><img src="https://meetingtomorrow.com/wp-content/uploads/2015/07/pricelist-tab.png" alt="Download a Pricelist"></div> 
		</div>
	</a>
	<!-- Pricelist Form -->
	<div class="fusion-modal modal fade modal-2 pricelist_tab_popup" tabindex="-1" role="dialog" aria-labelledby="modal-heading-2" aria-hidden="true">
	  <style type="text/css">.modal-2 .modal-header, .modal-2 .modal-footer{border-color:#ebebeb;}</style>
		<div class="modal-dialog modal-sm">
			<div class="modal-content fusion-modal-content" style="background-color:#f6f6f6">
				<div class="modal-header">
					<button class="close" type="button" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title" id="modal-heading-2" data-dismiss="modal" aria-hidden="true">Just need pricing? <span style="font-size: 17px; display: block;">Download our Price List PDF</span></h3>
				</div>
				<div class="modal-body">
					<div id="wufoo-r1pq4xf3135ngw7">
					</div>
					<script type="text/javascript">var r1pq4xf3135ngw7;(function(d, t) {
					var s = d.createElement(t), options = {
					'userName':'meetingtomorrow',
					'formHash':'r1pq4xf3135ngw7',
					'autoResize':true,
					'height':'380',
					'async':true,
					'host':'wufoo.com',
					'header':'hide',
					'ssl':true};
					s.src = ('https:' == d.location.protocol ? 'https://' : 'http://') + 'www.wufoo.com/scripts/embed/form.js';
					s.onload = s.onreadystatechange = function() {
					var rs = this.readyState; if (rs) if (rs != 'complete') if (rs != 'loaded') return;
					try { r1pq4xf3135ngw7 = new WufooForm();r1pq4xf3135ngw7.initialize(options);r1pq4xf3135ngw7.display(); } catch (e) {}};
					var scr = d.getElementsByTagName(t)[0], par = scr.parentNode; par.insertBefore(s, scr);
					})(document, 'script');</script>

				</div>
				<div class="modal-footer">
					<a class="fusion-button button-default button-small button default small" data-dismiss="modal">Close</a>
				</div>
			</div>
		</div>
	</div>

	<!-- Exit Intent Form -->
	<!-- Ouibounce Modal -->
	  <div id="ouibounce-modal">
	    <div class="underlay"></div>
	    <div class="ob-modal">
	      <div class="ob-modal-title">
	        <button class="close ob-close" type="button" aria-hidden="true">&times;</button>
	        Get a Quick Quote
	      </div>
	      <div class="ob-modal-body">
	        <iframe id="nsIframeExitIntent" src="<?php echo 'https://forms.na2.netsuite.com/app/site/crm/externalleadpage.nl?compid=3373305&formid=66&h=AACffht_JbRmDTPrYhOS7xCYeyKnSLM3M4w%3D&url=' . rawurlencode( get_permalink()); ?>" width="100%" height="350" frameborder="0" scrolling="no"></iframe>

	        <!-- Form with hidden fields for GA reporting -->
					<form id="ga-tracking-form" method="POST" name='contactform' onSubmit=""> 
					    <input type='hidden' name='source' /> 
					    <input type='hidden' name='medium' /> 
					    <input type='hidden' name='term' /> 
					    <input type='hidden' name='content' /> 
					    <input type='hidden' name='campaign' /> 
					    <input type='hidden' name='gclid' />
					    <input type='hidden' name='segment' />
					    <input type='hidden' name='numVisits' />
					</form>
	      </div>
	      <div class="ob-modal-footer">
	        <a class="fusion-button button-default button-small button default small">Close</a>
	      </div>
	    </div>
	  </div>
  <!-- End Ouibounce Modal --> 

  <script>
    // if you want to use the 'fire' or 'disable' fn,
    // you need to save OuiBounce to an object
    // More info here: https://github.com/carlsednaoui/ouibounce
    var _ouibounce = ouibounce(document.getElementById('ouibounce-modal'), {
      timer: 0,
      delay: 300
    });

    jQuery('body').on('click', function() {
      jQuery('#ouibounce-modal').hide();
    });

    jQuery('#ouibounce-modal .ob-modal-footer').on('click', function() {
      jQuery('#ouibounce-modal').hide();
    });

    jQuery('#ouibounce-modal .ob-close').on('click', function() {
      jQuery('#ouibounce-modal').hide();
    });

    jQuery('#ouibounce-modal .ob-modal').on('click', function(e) {
      e.stopPropagation();
    });
  </script>

	<!-- Fitvids.js code - more info here: http://fitvidsjs.com -->
	<script>
	  jQuery(document).ready(function(){
	    jQuery(".fitvids-wrapper").fitVids({ customSelector: "iframe[src^='//fast.wistia.net/embed/iframe/zfnh8g142a']"});
	  });
	</script>

	<script>
	  jQuery(document).ready(function(){
	    jQuery(".fitvids-video-wrap").fitVids();
	  });
	</script>

	<!-- Testimonial slider -->
	<script>
	  jQuery(function() {
	    jQuery(".rslides").responsiveSlides({
        auto: true,
        nav: true,
        random: true,
        speed: 600,
        timeout: 10000
      });
	  });
	</script>

	<!-- Placeholder Script - displays placeholder even in IE8/9. More info: https://github.com/mathiasbynens/jquery-placeholder -->
	<script>
		jQuery('input, textarea').placeholder();
	</script>

	<!--[if lte IE 8]>
	<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/respond.js"></script>
	<![endif]-->

	<!-- Download Pricelist Form Tab 
	<script type="text/javascript" src="//cdn.contactus.com/cdn/forms/OWQyOTlhMTQ4MTU,/contactus.js"></script>
	<a class="cu_trigger" href="http://contactus.com">ContactUs.com</a>
	-->

	<!-- Contact Us Form Tab 
	<script type="text/javascript" src="//cdn.contactus.com/cdn/forms/YzNmOWIzMTI3ZDc,/contactus.js"></script>
	<a class="cu_trigger" href="http://contactus.com">ContactUs.com</a>
	-->

	<!-- Exit Intent Form 
	<script type="text/javascript" src="//cdn.contactus.com/cdn/forms/ODYzZjk1MjZjMTc,/contactus.js"></script>
	<a class="cu_trigger" href="http://contactus.com">ContactUs.com</a>
	-->

	<!-- Start of LiveChat (www.livechatinc.com) code -->
	<script type="text/javascript">
	var __lc = {};
	__lc.license = 3484832;
	__lc.ga_version = "ga";
	(function() {
	  var lc = document.createElement('script'); lc.type = 'text/javascript'; lc.async = true;
	  lc.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.livechatinc.com/tracking.js';
	  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(lc, s);
	})();
	</script>
	<!-- End of LiveChat code -->

  <!-- Pass Google Analytics info to NetSuite -->
	<script type="text/javascript"> 
	   var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www."); 
	   document.write("<script src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'>" + "</sc" + "ript>"); 
	</script> 

	<script type='text/javascript'>
	var pageTracker = _gat._getTracker("UA-1-1");
	pageTracker._trackPageview();

	//
	// This is a function that I "borrowed" from the urchin.js file.
	// It parses a string and returns a value.  I used it to get
	// data from the __utmz cookie
	//
	function _uGC(l,n,s) {
	 if (!l || l=="" || !n || n=="" || !s || s=="") return "-";
	 var i,i2,i3,c="-";
	 i=l.indexOf(n);
	 i3=n.indexOf("=")+1;
	 if (i > -1) {
	  i2=l.indexOf(s,i); if (i2 < 0) { i2=l.length; }
	  c=l.substring((i+i3),i2);
	 }
	 return c;
	}

	// 
	// Get the __utmz cookie value. This is the cookies that 
	// stores all campaign information. 
	// 
	var z = _uGC(document.cookie, '__utmz=', ';'); 
	// 
	// The cookie has a number of name-value pairs. 
	// Each identifies an aspect of the campaign. 
	// 
	// utmcsr  = campaign source 
	// utmcmd  = campaign medium 
	// utmctr  = campaign term (keyword) 
	// utmcct  = campaign content  
	// utmccn  = campaign name 
	// utmgclid = unique identifier used when AdWords auto tagging is enabled 
	// 
	// This is very basic code. It separates the campaign-tracking cookie 
	// and populates a variable with each piece of campaign info. 
	// 
	var source  = _uGC(z, 'utmcsr=', '|'); 
	var medium  = _uGC(z, 'utmcmd=', '|'); 
	var term    = _uGC(z, 'utmctr=', '|'); 
	var content = _uGC(z, 'utmcct=', '|'); 
	var campaign = _uGC(z, 'utmccn=', '|'); 
	var gclid   = _uGC(z, 'utmgclid=', '|');        

	// 
	// The gclid is ONLY present when auto tagging has been enabled. 
	// All other variables, except the term variable, will be '(not set)'. 
	// Because the gclid is only present for Google AdWords we can 
	// populate some other variables that would normally 
	// be left blank. 
	// 
	if (gclid !="-") { 
	      source = 'google'; 
	      medium = 'cpc'; 
	} 
	// Data from the custom segmentation cookie can also be passed 
	// back to your server via a hidden form field 
	var csegment = _uGC(document.cookie, '__utmv=', ';'); 
	if (csegment != '-') { 
	      var csegmentex = /[1-9]*?\.(.*)/;
	      csegment    = csegment.match(csegmentex); 
	      csegment    = csegment[1]; 
	} else { 
	      csegment = '(not set)'; 
	} 

	//
	// One more bonus piece of information.  
	// We're going to extract the number of visits that the visitor
	// has generated.  It's also stored in a cookie, the __utma cookie
	// 
	var a = _uGC(document.cookie, '__utma=', ';');
	var aParts = a.split(".");
	var nVisits = aParts[5];
	                             

	function populateHiddenFields() { 
		jQuery('form#ga-tracking-form').each(function (index, element) {
		
			var f = this;
			
			f.source.value  = source; 
			f.medium.value  = medium; 
			f.term.value    = term; 
			f.content.value = content; 
			f.campaign.value = campaign;
			f.gclid.value = gclid; 
			f.segment.value = csegment;
			f.numVisits.value = nVisits;  

		});
		
		// Important to keep things in this order: 1. nsIframeSidetab 2. nsIframeExitIntent 3. nsIframe
		// Script will check to see if the element exists. If it doesn't, the parameters won't be
		// added for that element AND any of the following elements below it.
		// Since nsIframeSidetab and nsExitIntent will be on every page, keep those first. nsIframe goes last
		// because it's only on certain pages.      

		var loc = window.location.toString();
    params = loc.split('?')[1];     

		document.getElementById("nsIframeSidetab").src = document.getElementById("nsIframeSidetab").src + '&custentity_web_lead_nab_parameter=' + encodeURIComponent(params) + '&custentity_ga_search_campaign=' + encodeURIComponent(campaign) + '&custentity_ga_search_source=' + encodeURIComponent(source) + '&custentity_ga_search_medium=' + encodeURIComponent(medium) + '&custentity_ga_search_keyword=' + encodeURIComponent(term) + '&custentity_ga_search_content=' + encodeURIComponent(content) + '&custentity_ga_search_gclid=' + encodeURIComponent(gclid);

		document.getElementById("nsIframeExitIntent").src = document.getElementById("nsIframeExitIntent").src + '&custentity_web_lead_nab_parameter=' + encodeURIComponent(params) + '&custentity_ga_search_campaign=' + encodeURIComponent(campaign) + '&custentity_ga_search_source=' + encodeURIComponent(source) + '&custentity_ga_search_medium=' + encodeURIComponent(medium) + '&custentity_ga_search_keyword=' + encodeURIComponent(term) + '&custentity_ga_search_content=' + encodeURIComponent(content) + '&custentity_ga_search_gclid=' + encodeURIComponent(gclid);

		document.getElementById("nsIframe").src = document.getElementById("nsIframe").src + '&custentity_web_lead_nab_parameter=' + encodeURIComponent(params) + '&custentity_ga_search_campaign=' + encodeURIComponent(campaign) + '&custentity_ga_search_source=' + encodeURIComponent(source) + '&custentity_ga_search_medium=' + encodeURIComponent(medium) + '&custentity_ga_search_keyword=' + encodeURIComponent(term) + '&custentity_ga_search_content=' + encodeURIComponent(content) + '&custentity_ga_search_gclid=' + encodeURIComponent(gclid);
                                  
        return false; 
  } 
	                                                               
	</script> 

	<!-- Google Analytics Tracking -->
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-228106-1', 'auto');
	  ga('send', 'pageview');
	</script>	

	<script type="text/javascript">
		// Track 404 errors with Universal Analytics
		if(jQuery('body').hasClass('error404')) {
			ga('send', 'pageview', '404.html?page='+ document.location.pathname + document.location.search +'&from=' + document.referrer, {'nonInteraction': 1});		}
	</script>

  <!-- Isotope Grid on Reviews Page -->
	<script>
    var $grid = jQuery('.reviews-wrap').isotope({
		  // set itemSelector so .grid-sizer is not used in layout
		  itemSelector: '.reviews-col',
		  percentPosition: true,
		  masonry: {
		    // use element for option
		    columnWidth: '.grid-sizer'
		  }
		});
		$grid.imagesLoaded().progress( function() {
		  $grid.isotope('layout');
		});
  </script>  

</body>
</html>