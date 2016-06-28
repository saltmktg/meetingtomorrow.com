<?php
/**
 * Copyright 2013 Nelio Software S.L.
 * This script is distributed under the terms of the GNU General Public License.
 *
 * This script is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License.
 * This script is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see <http://www.gnu.org/licenses/>.
 */


if ( !class_exists( 'NelioABDashboardPage' ) ) {

	require_once( NELIOAB_UTILS_DIR . '/admin-ajax-page.php' );
	class NelioABDashboardPage extends NelioABAdminAjaxPage {

		private $graphic_delay;
		private $experiments;
		private $rss;

		public function __construct( $title ) {

			$loader = sprintf(
				'<span class="nelioab-results-loader spinner" title="%s" style="margin-top:-4px;"></span>',
				esc_attr( __( 'Checking if there are new results available...', 'nelioab' ) )
			);

			parent::__construct( $title . $loader );

			$this->set_icon( 'icon-nelioab' );
			$this->add_title_action( __( 'New Experiment', 'nelioab' ), '?page=nelioab-add-experiment' );
			$this->experiments = array();
			$this->graphic_delay = 500;
			$this->rss = fetch_feed( 'https://nelioabtesting.com/feed/' );

		}

		public function set_summary( $summary ) {
			$this->experiments = $summary['exps'];
		}

		public function do_render() {
			echo '<div id="post-body" class="metabox-holder columns-2">';
			echo '<div id="post-body-content">';
			if ( count( $this->experiments ) == 0 ) {
				echo "<div class='nelio-message'>";
				echo sprintf( '<img class="animated flipInY" src="%s" alt="%s" />',
					nelioab_admin_asset_link( '/images/dashboard.png' ),
					__( 'Dashboard Icon', 'nelioab' )
				);
				echo '<h2 style="max-width:750px;">';
				printf( '%1$s<br><br><a class="button button-primary" href="%3$s">%2$s</a>',
					__( 'Here you\'ll find relevant information about your running experiments.', 'nelioab' ),
					__( 'Create One Now!', 'nelioab', 'create-experiment' ),
					'admin.php?page=nelioab-add-experiment' );
				echo '</h2>';
				echo '</div>';
			}
			else {
				echo '<h2>' . __( 'Running Experiments', 'nelioab' ) . '</h2>';
				$this->print_cards();
			}
			echo '</div>'; ?>
			<div id="postbox-container-1" class="postbox-container" style="overflow:hidden;">
				<h2>&nbsp;</h2>
				<?php
				require_once( NELIOAB_UTILS_DIR . '/wp-helper.php' );

				?>

				<div class="numbers" style="height:40px;">
					<div class="left" style="float:left; width:58%;">
						<span style="font-weight:bold;"><?php _e( 'AVAILABLE QUOTA', 'nelioab' ); ?></span><br>
						<span class="available-quota current" style="font-size:10px;"><?php
							esc_html_e( 'Loading...' );
						?></span>
						<span class="available-quota monthly" style="font-size:10px;"></span>
					</div>
					<div class="right quota-percentage" style="font-size:32px; text-align:right; float:right; width:36%; padding-right:5%; margin-top:8px; opacity:0.7;">&mdash; %</div>
				</div>

				<div class="progress-bar-container" style="background:none;border:2px solid rgba(0,0,0,0.1); width:95%; margin:0px; height:20px;">
					<div class="progress-bar normal" style="margin:0;padding:0;display:inline-block;height:20px;width:0;"></div><div class="progress-bar extended" style="margin:0;padding:0;display:inline-block;height:20px;width:0;"></div>
				</div>

				<script type="text/javascript">
				(function( $ ) {
					function onError() {
						$( '.available-quota.current' ).html( <?php
							echo json_encode( __( 'Error retrieving quota', 'nelioab' ) );
						?> );
					}
					$.ajax({
						url: ajaxurl,
						data: {
							action: 'nelioab_get_quota'
						},
						success: function( data ) {
							if ( typeof data.available !== 'undefined' ) {

								$( '.available-quota.current' ).html( data.available );
								$( '.available-quota.current' ).css( 'color', data.darkColor );
								$( '.available-quota.monthly' ).html( data.monthly );

								$( '.quota-percentage' ).html( data.quotaPercentage );
								$( '.quota-percentage' ).css( 'color', data.darkColor );

								$( '.progress-bar.normal' ).css( 'background', data.lightColor );
								$( '.progress-bar.normal' ).css( 'width', data.normalWidth );
								$( '.progress-bar.extended' ).css( 'background', data.darkColor );
								$( '.progress-bar.extended' ).css( 'width', data.extraWidth );

							} else {
								onError();
							}
						},
						error: function() {
							onError();
						}
					});
				})( jQuery );
				</script>

			<?php
			$this->print_rss();
			echo '</div>'; // #post-body
			?>


			<script type="text/javascript">
			(function( $ ) {
				var completedChecks = 0;
				var areThereNewResults = false;

				var expIds = <?php
					$ids = array();
					foreach ( $this->experiments as $exp ) {
						array_push( $ids, $exp->get_id() );
					}//end foreach
					echo json_encode( $ids );
				?>;

				var $loader = $( 'span.nelioab-results-loader' );
				$loader.addClass( 'is-active' );

				function maybeDone() {
					if ( completedChecks === expIds.length ) {
						$loader.removeClass( 'is-active' );
						if ( areThereNewResults ) {
							var $div = $( '#message-div' );
							$div.html( <?php
								$message = sprintf(
									_x( 'Hey! It looks like there are <strong>new results available. <a href="%s">Refresh!</a></strong>', 'HTML formatted string', 'nelioab' ),
									'javascript:window.location.reload()'
								);
								echo json_encode( '<p style="font-size:1.3em;">' . $message . '</p>' );
							?> );
							$div.show();
						}//end if
					}//end if
				}//end maybeDone()

				for ( var i = 0; i < expIds.length; ++i ) {
					var expId = expIds[i];
					$.ajax({
						url: ajaxurl,
						async: true,
						data: {
							action: "nelioab_update_results",
							exp: expId
						},
						success: function( result ) {
							++completedChecks;
							if ( result.indexOf( 'nelioab-new-results-available' ) >= 0 ) {
								areThereNewResults = true;
							}//end if
							maybeDone();
						},//end success()
						error: function() {
							++completedChecks;
							maybeDone();
						}//end error()
					});
				}//end for

				maybeDone();

			})( jQuery );
			</script>
			<?php
		}

		public function print_rss() {
			$maxitems = 0;

			if ( ! is_wp_error( $this->rss ) ) : // Checks that the object is created correctly

				// Figure out how many total items there are, but limit it to 5.
				$maxitems = $this->rss->get_item_quantity( 5 );

				// Build an array of all the items, starting with element 0 (first element).
				$rss_items = $this->rss->get_items( 0, $maxitems ); ?>

				<?php if ( $maxitems == 0 ) return; ?>

				<div id="nelio-rss" class="postbox-container" style="overflow:hidden;">
					<h2><?php _e( 'Latest News', 'nelioab' ); ?></h2>
				<?php // Loop through each feed item and display each item as a hyperlink.
				foreach ( $rss_items as $item ) {
					$title       = $item->get_title();
					$description = $item->get_description();
					$permalink   = $item->get_permalink();
					$description = str_replace( '<p>', '', $description );
					// Look for the featured image
					$pos = strpos( $description, '/>' );
					if ( !$pos ) continue;
					$featured_img = substr( $description, 0, $pos + 2 );
				?>
					<div class='nelio-rss-item'>
						<div class="nelio-rss-featured-image">
							<a href='<?php echo $permalink; ?>' target='_blank'>
								<?php echo $featured_img; ?>
							</a>
						</div>
						<div class='nelio-rss-title'>
							<a href='<?php echo $permalink; ?>' target='_blank'>
								<?php echo $title; ?>
							</a>
						</div>
					</div>
				<?php
				} ?>
				</div><?php
			endif;
		}

		public function print_cards() {

			include_once( NELIOAB_UTILS_DIR . '/wp-helper.php' );
			foreach ( $this->experiments as $exp ) {
				switch( $exp->get_type() ) {
					case NelioABExperiment::HEATMAP_EXP:
						$progress_url = str_replace( 'https://', 'http://',
							admin_url( 'admin.php?nelioab-page=heatmaps&id=%1$s&exp_type=%2$s' ) );
						$this->print_linked_beautiful_box(
							$exp->get_id(),
							$this->get_beautiful_title( $exp ),
							sprintf( $progress_url, $exp->get_id(), $exp->get_type() ),
							array( &$this, 'print_heatmap_exp_card', array( $exp ) ) );
						break;
					default:
						$this->print_linked_beautiful_box(
							$exp->get_id(),
							$this->get_beautiful_title( $exp ),
							sprintf(
									'?page=nelioab-experiments&action=progress&id=%1$s&exp_type=%2$s',
									$exp->get_id(),
									$exp->get_type()
								),
							array( &$this, 'print_alt_exp_card', array( $exp ) ) );
				}
			}

		}

		public function get_beautiful_title( $exp ) {
			$img = '<div class="tab-type tab-type-%1$s" alt="%2$s" title="%2$s"></div>';
			switch ( $exp->get_type() ) {
				case NelioABExperiment::PAGE_ALT_EXP:
					try {
						$page_on_front = get_option( 'page_on_front' );
						$aux = $exp->get_alternative_info();
						if ( $page_on_front == $aux[0]['id'] )
							$img = sprintf( $img, 'landing-page', __( 'Landing Page', 'nelioab' ) );
						else
							$img = sprintf( $img, 'page', __( 'Page', 'nelioab' ) );
					}
					catch ( Exception $e ) {
						$img = sprintf( $img, 'page', __( 'Page', 'nelioab' ) );
					}
					break;
				case NelioABExperiment::POST_ALT_EXP:
					$img = sprintf( $img, 'post', __( 'Post', 'nelioab' ) );
					break;
				case NelioABExperiment::CPT_ALT_EXP:
					$img = sprintf( $img, 'cpt', __( 'Post', 'nelioab' ) );
					break;
				case NelioABExperiment::HEADLINE_ALT_EXP:
					$img = sprintf( $img, 'title', __( 'Headline', 'nelioab' ) );
					break;
				case NelioABExperiment::WC_PRODUCT_SUMMARY_ALT_EXP:
					$img = sprintf( $img, 'wc-product-summary', __( 'WooCommerce Product Summary', 'nelioab' ) );
					break;
				case NelioABExperiment::THEME_ALT_EXP:
					$img = sprintf( $img, 'theme', __( 'Theme', 'nelioab' ) );
					break;
				case NelioABExperiment::CSS_ALT_EXP:
					$img = sprintf( $img, 'css', __( 'CSS', 'nelioab' ) );
					break;
				case NelioABExperiment::HEATMAP_EXP:
					$img = sprintf( $img, 'heatmap', __( 'Heatmap', 'nelioab' ) );
					break;
				case NelioABExperiment::WIDGET_ALT_EXP:
					$img = sprintf( $img, 'widget', __( 'Widget', 'nelioab' ) );
					break;
				case NelioABExperiment::MENU_ALT_EXP:
					$img = sprintf( $img, 'menu', __( 'Menu', 'nelioab' ) );
					break;
				default:
					$img = '';
			}

			if ( $exp->has_result_status() )
				$light = NelioABGTest::generate_status_light( $exp->get_result_status() );
			else
				$light = '';

			$title = '';
			$name = $exp->get_name();
			if ( strlen( $name ) > 50 ) {
				$title = ' title="' . esc_html( $name ) . '"';
				$name = substr( $name, 0, 50 ) . '...';
			}
			$name = '<span class="exp-title"'. $title .'>' . $name . '</span>';
			$status = '<span id="info-summary">' . $light . '</span>';

			return $img . $name . $status;
		}

		public function print_alt_exp_card( $exp ) { ?>
			<div class="row padding-top">
				<div class="col col-4">
					<div class="row data padding-left">
						<span class="value"><?php echo $exp->get_total_visitors(); ?></span>
						<span class="label"><?php _e( 'Page Views', 'nelioab' ); ?></span>
					</div>
					<div class="row data padding-left">
						<span class="value"><?php echo count( $exp->get_alternative_info() ); ?></span>
						<span class="label"><?php _e( 'Alternatives', 'nelioab' ); ?></span>
					</div>
				</div>
				<div class="col col-4">
					<div class="row data">
						<?php
							$val = $exp->get_original_conversion_rate();
							$val = number_format_i18n( $val, 2 );
							$val = preg_replace( '/(...)$/', '<span class="decimals">$1</span>', $val );
							$val .= ' %';
						?>
						<span class="value"><?php echo $val; ?></span>
						<span class="label"><?php _e( 'Original Version\'s Conversion Rate', 'nelioab' ); ?></span>
					</div>
					<div class="row data">
						<?php
							$val = $exp->get_best_alternative_conversion_rate();
							$val = number_format_i18n( $val, 2 );
							$val = preg_replace( '/(...)$/', '<span class="decimals">$1</span>', $val );
							$val .= ' %';
						?>
						<span class="value"><?php echo $val; ?></span>
						<span class="label"><?php _e( 'Best Alternative\'s Conversion Rate', 'nelioab' ); ?></span>
					</div>
				</div>
				<?php $graphic_id = 'graphic-' . $exp->get_id(); ?>
				<div class="col col-4 graphic" id="<?php echo $graphic_id; ?>">
				</div><?php
					if ( $exp->get_total_conversions() > 0 )
						$fix = '';
					else
						$fix = '.1';
					$alt_infos = $exp->get_alternative_info();
					$values = '';
					for ( $i = 0; $i < count( $alt_infos ); ++$i ) {
						$aux = $alt_infos[$i];
						$name = $aux['name'];
						$name = str_replace( '\\', '\\\\', $name );
						$name = str_replace( '\'', '\\\'', $name );
						$conv = $aux['conversions'];
						$values .= "\n\t\t\t\t{ name: '$name', y: $conv$fix },\n";
					}

					switch( $exp->get_type() ) {
						case NelioABExperiment::PAGE_ALT_EXP:
							$color = '#DE4A3A';
							break;
						case NelioABExperiment::POST_ALT_EXP:
							$color = '#F19C00';
							break;
						case NelioABExperiment::CPT_ALT_EXP:
							$color = '#FF8822';
							break;
						case NelioABExperiment::HEADLINE_ALT_EXP:
							$color = '#79B75D';
							break;
						case NelioABExperiment::THEME_ALT_EXP:
							$color = '#61B8DD';
							break;
						case NelioABExperiment::CSS_ALT_EXP:
							$color = '#6EBEC5';
							break;
						case NelioABExperiment::WIDGET_ALT_EXP:
							$color = '#2A508D';
							break;
						case NelioABExperiment::MENU_ALT_EXP:
							$color = '#8bb846';
							break;
						case NelioABExperiment::WC_PRODUCT_SUMMARY_ALT_EXP:
							$color = '#2A508D';
							break;
						default:
							$color = '#CCCCCC';
					}
				?>
				<script>jQuery(document).ready(function() {
					var aux = setTimeout( function() {
						drawGraphic('<?php echo $graphic_id; ?>',
							[<?php echo $values; ?>],
							"<?php echo esc_html( __( 'Conversions', 'nelioab' ) ); ?>",
							"<?php echo $color; ?>");
					}, <?php echo $this->graphic_delay; $this->graphic_delay += 250; ?> );
				});</script>
			</div>
			<?php
		}

		public function print_heatmap_exp_card( $exp ) {
			/** @var NelioABHeatmapExpSummary $exp */
			$hm = $exp->get_heatmap_info();
			?>
			<div class="row padding-top">
				<div class="col col-6">
					<div class="row data phone padding-left">
						<span class="value"><?php echo $hm['phone']; ?></span>
						<span class="label"><?php _e( 'Views on Phone', 'nelioab' ); ?></span>
					</div>
					<div class="row data tablet padding-left">
						<span class="value"><?php echo $hm['tablet']; ?></span>
						<span class="label"><?php _e( 'Views on Tablet', 'nelioab' ); ?></span>
					</div>
				</div>
				<div class="col col-6">
					<div class="row data desktop">
						<span class="value"><?php echo $hm['desktop']; ?></span>
						<span class="label"><?php _e( 'Views on Desktop', 'nelioab' ); ?></span>
					</div>
					<div class="row data hd">
						<span class="value"><?php echo $hm['hd']; ?></span>
						<span class="label"><?php _e( 'Views on Large Screens', 'nelioab' ); ?></span>
					</div>
				</div>
			</div>
			<?php
		}
	}//NelioABDashboardPage
}
