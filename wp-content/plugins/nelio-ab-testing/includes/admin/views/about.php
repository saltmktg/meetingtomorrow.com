<?php
// ------------------------------------------------
// FILL THIS VARS FOR AN UPDATE
// ------------------------------------------------

$welcome_message = __( '%s Nelio A/B Testing %s is now faster and more compatible than ever. We hope you enjoy using it.', 'nelioab' );


/** @var string|boolean $minor_update */
$minor_update = false;


$main_update_title = __( 'ResponseTap Support', 'nelioab' );
$main_update_summary = __( 'Nelio A/B Testing is now compatible with <a href="https://www.responsetap.com/">ResponseTap</a>, a call-tracking service. From now on, you can use your customers\' actual calls in a split test and count them as conversions!', 'nelioab' );
$main_update_details = array(
	array(
		'title' => __( 'Speed Improvements', 'nelioab' ),
		'text'  => __( 'This new version uses a higher priority for enqueuing our tracking scripts, offering a faster response to all your visitors.', 'nelioab' )
	),
	array(
		'title' => __( 'Editors, get ready!', 'nelioab' ),
		'text'  => __( 'The plugin is now available to Editors and Administrators, so there\'s no need to log in as an Administrator to manage it.', 'nelioab' )
	),
	array(
		'title' => __( 'Disqus Support', 'nelioab' ),
		'text'  => __( 'We added <a href="https://help.disqus.com/customer/portal/articles/472098">Disqus config script</a> for loading the appropriate comment thread.', 'nelioab' )
	),
);


$secondary_update_details = array();
//	array(
//		'title' => __( 'New Results Page', 'nelioab' ),
//		'text'  => __( 'We\'ve completely redesigned the results page. Now, the results are organized in three different sections: (a) general information about the experiment and its status, (b) details of the alternatives, and (c) a more visual list of conversion actions.', 'nelioab' )
//	),
//	array(
//		'title' => __( 'New UI for Conversion Actions', 'nelioab' ),
//		'text'  => __( 'The set of conversion actions are no longer presented using texts. Now, when you define the goals of your experiments, conversion actions use descriptive icons.', 'nelioab' )
//	),
//	array(
//		'title' => __( 'Drag and Drop Conversion Actions', 'nelioab' ),
//		'text'  => __( 'Conversion Actions are one of the key pieces of an A/B Testing platform. You can now sort the conversion actions within a goal just by dragging and dropping them.', 'nelioab' )
//	)
//);


$tweets = array(
	__( 'Are you a WordPress publisher? #Nelio A/B Testing makes it super easy to split test your headlines!', 'nelioab' ),
	__( '#Nelio A/B Testing is the best #abtest service for #WordPress. Check it out (it\'s free to test).', 'nelioab' ),
	__( 'Collecting #heatmaps and #clickmaps in #WordPress, and then running split tests, is easy with Nelio.', 'nelioab' ),
	__( 'Want more income? More subscribers? #Nelio A/B Testing is the best plugin for doing so in #WordPress.', 'nelioab' )
);


?>
<div class="wrap about-wrap">
	<h1><?php printf( __( 'Welcome to Nelio A/B Testing %s', 'nelioab' ), NELIOAB_PLUGIN_VERSION ); ?></h1>

	<div class="about-text nelioab-about-text">
		<?php
			if ( ! empty( $_GET['nelioab-installed'] ) ) {
				$message = __( 'Thanks, all done!', 'nelioab' );
			} elseif ( ! empty( $_GET['nelioab-updated'] ) ) {
				$message = __( 'Thank you for updating to the latest version!', 'nelioab' );
			} else {
				$message = __( 'Thanks for installing!', 'nelioab' );
			}

			printf( $welcome_message, $message, NELIOAB_PLUGIN_VERSION );
		?>
	</div>

	<div class="nelioab-badge"><div class="logo"></div><?php printf( __( 'Version %s', 'nelioab' ), NELIOAB_PLUGIN_VERSION ); ?></div>

	<?php
		// Random tweet - must be kept to 102 chars to "fit"
		shuffle( $tweets );
	?>

	<p class="nelioab-first-actions">
		<a href="https://twitter.com/share" class="twitter-share-button" data-url="https://nelioabtesting.com" data-text="<?php echo esc_attr( $tweets[0] ); ?>" data-via="NelioSoft" data-size="large"><?php _e( 'Tweet', 'nelioab' ); ?></a>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
	</p>

	<?php
	if ( $minor_update ) {
		echo '<h2>' . __( 'Minor Update Notice', 'nelioab' ). '</h2>';
		echo '<p>' . $minor_update . '</p>';
	}
	?>

	<h2><?php _e( 'What\'s New', 'nelioab' ); ?></h2>

	<div class="changelog">
		<h4><?php echo $main_update_title; ?></h4>
		<p><?php echo $main_update_summary; ?></p>

		<div class="changelog about-integrations">
			<div class="nelioab-feature feature-section col three-col">
				<div>
					<h4><?php echo $main_update_details[0]['title']; ?></h4>
					<p><?php echo $main_update_details[0]['text']; ?></p>
				</div>
				<div>
					<h4><?php echo $main_update_details[1]['title']; ?></h4>
					<p><?php echo $main_update_details[1]['text']; ?></p>
				</div>
				<div class="last-feature">
					<h4><?php echo $main_update_details[2]['title']; ?></h4>
					<p><?php echo $main_update_details[2]['text']; ?></p>
				</div>
			</div>
		</div>
	</div>

	<?php
		$size = count( $secondary_update_details );
		if ( $size > 0 ) {
			echo "\n";
			echo '<div class="changelog">' . "\n";

			for ( $i = 0; $i < count( $secondary_update_details ); ++$i ) {
				$is_first_in_block = $i % 3 == 0;
				$is_last_in_block = $i % 3 == 2 || $i == $size - 1;

				if ( $is_first_in_block ) {
					echo '  <div class="feature-section col three-col">' . "\n";
				}

				if ( $is_last_in_block ) {
					echo '    <div class="last-feature">' . "\n";
				} else {
					echo '    <div>' . "\n";
				}

				$details = $secondary_update_details[$i];
				echo '      <h4>' . $details['title'] . '</h4>' . "\n";
				echo '      <p>' . $details['text'] . '</p>' . "\n";

				echo '    </div>' . "\n";

				if ( $is_last_in_block ) {
					echo '  </div>' . "\n";
				}
			}
		}

		echo '</div>' . "\n";
	?>

	<p class="nelioab-last-actions">
		<a href="<?php echo admin_url('admin.php?page=nelioab-dashboard'); ?>" class="button button-primary"><?php _e( 'Dashboard', 'nelioab' ); ?></a>
		<a href="<?php echo esc_url( apply_filters( 'nelioab', 'http://support.nelioabtesting.com/support/home', 'nelioab' ) ); ?>" class="button"><?php _e( 'Docs', 'nelioab' ); ?></a>
	</p>

</div>
