<?php
/**
 * Copyright 2015 Nelio Software S.L.
 * This script is distributed under the terms of the GNU General Public
 * License.
 *
 * This script is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free
 * Software Foundation, either version 3 of the License.
 *
 * This script is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see <http://www.gnu.org/licenses/>.
 */

add_action( 'init', 'nelioab_register_exp_post_type', 5 );
/**
 * Register a new post type for saving experiments locally.
 *
 * @since 4.3.2
 */
function nelioab_register_exp_post_type() {
	if ( post_type_exists( 'nelioab_local_exp' ) ) {
		return;
	}

	register_post_type( 'nelioab_local_exp',
		array(
			'labels' => array(
				'name'     => 'Split Tests',
				'singular' => 'Split Test',
			),
			'can_export'      => false,
			'capability_type' => 'post',
			'hierarchical'    => false,
			'map_meta_cap'    => true,
			'menu_position'   => 2,
			'query_var'       => false,
			'rewrite'         => false,
			'public'          => NELIOAB_SHOW_LOCAL_EXPS,
			'show_in_menu'    => NELIOAB_SHOW_LOCAL_EXPS,
			'show_ui'         => NELIOAB_SHOW_LOCAL_EXPS,
			'supports'        => array( 'title', 'excerpt', 'editor' ),
		)
	);

	$args = array(
		'public' => false,
		'internal' => false,
		'exclude_from_search' => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
	);

	$args['label'] = __( 'Paused', 'nelioab' );
	$args['label_count'] = _n_noop( 'Paused <span class="count">(%s)</span>', 'Paused <span class="count">(%s)</span>' );
	register_post_status( 'nelioab_paused', $args );

	$args['label'] = __( 'Ready', 'nelioab' );
	$args['label_count'] = _n_noop( 'Ready <span class="count">(%s)</span>', 'Ready <span class="count">(%s)</span>' );
	register_post_status( 'nelioab_ready', $args );

	$args['label'] = __( 'Running', 'nelioab' );
	$args['label_count'] = _n_noop( 'Running <span class="count">(%s)</span>', 'Running <span class="count">(%s)</span>' );
	register_post_status( 'nelioab_running', $args );

	$args['label'] = __( 'Finished', 'nelioab' );
	$args['label_count'] = _n_noop( 'Finished <span class="count">(%s)</span>', 'Finished <span class="count">(%s)</span>' );
	register_post_status( 'nelioab_finished', $args );

	$args['label'] = __( 'Scheduled', 'nelioab' );
	$args['label_count'] = _n_noop( 'Scheduled <span class="count">(%s)</span>', 'Scheduled <span class="count">(%s)</span>' );
	register_post_status( 'nelioab_scheduled', $args );

	$args['label'] = __( 'Deleted', 'nelioab' );
	$args['label_count'] = _n_noop( 'Deleted <span class="count">(%s)</span>', 'Deleted <span class="count">(%s)</span>' );
	register_post_status( 'nelioab_deleted', $args );

}//end nelioab_register_exp_post_type()

if ( NELIOAB_SHOW_LOCAL_EXPS && is_admin() ) {

	add_action( 'pre_get_posts', 'nelioab_show_all_tests' );
	function nelioab_show_all_tests( $query ) {
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
		}//end if
		if ( isset( $screen->id ) && $screen->id === 'edit-nelioab_local_exp' ) {
			if ( ! isset( $_GET['post_status'] ) && ! isset( $_GET['post'] ) ) {
				$query->set( 'post_status', 'draft,nelioab_paused,nelioab_ready,nelioab_running,nelioab_finished,nelioab_scheduled' );
			}//end if
		}//end if
		return $query;
	}//end nelioab_show_all_tests()

}//end if




add_action( 'wp_ajax_nelioab_update_results', 'nelioab_update_results' );
function nelioab_update_results() {
	if ( ! isset( $_GET['exp'] ) || empty( $_GET['exp'] ) ) {
		wp_send_json( 'Exception: no exp set' );
	}//end if

	include_once( NELIOAB_MODELS_DIR . '/experiments-manager.php' );
	try {

		$exp = NelioABExperimentsManager::get_experiment_by_id( intval( $_GET['exp'] ) );

		// Heatmap Experiments are special
		if ( $exp->get_type() == NelioABExperiment::HEATMAP_EXP ) {

			$url = sprintf( NELIOAB_BACKEND_URL . '/exp/hm/%s/result', $exp->get_key_id() );
			$result = NelioABBackend::remote_get( $url );
			$result = json_decode( $result['body'] );
			$old_results = get_post_meta( $exp->get_id(), 'nelioab_hm_summary', true );

			$summary = array();
			if ( isset( $result->data ) ) {
				foreach ( $result->data as $item ) {
					if ( ! $item->click ) {
						$summary[ $item->resolution ] = $item->views;
					}//end if
				}//end if
			}//end if

			update_post_meta( $exp->get_id(), 'nelioab_hm_summary', $summary );

			foreach ( $summary as $res => $value ) {
				if ( ! isset( $old_results[ $res ] ) || $old_results[ $res ] != $value ) {
					wp_send_json( 'nelioab-new-results-available' );
				}//end if
			}//end if

			// NELIO LOCAL EXPS UPDATE. Auto stop experiment (if needed).
			if ( $exp->get_status() === NelioABExperiment::STATUS_RUNNING ) {
				if ( isset( $result->expStatus ) &&
						$result->expStatus === NelioABExperiment::STATUS_FINISHED ) {
					$exp->stop();
					wp_send_json( 'nelioab-new-results-available' );
				}//end if
			}//end if

			wp_send_json( 'nelioab-results-are-ok' );

		}//end if


		// Other experiments
		if ( isset( $_REQUEST['goal'] ) && ! empty( $_REQUEST['goal'] ) ) {

			$goal_id = $_REQUEST['goal'];

		} else {

			$goal_id = false;

			$goals = $exp->get_goals();
			foreach ( $goals as $goal ) {
				if ( $goal->is_main_goal() ) {
					$goal_id = $goal->get_id();
					break;
				}//end if
			}//end foreach

			if ( ! $goal_id && count( $goals ) > 0 ) {
				$goal = $goals[0];
				$goal_id = $goal->get_id();
			}//end if

		}//end if

		foreach ( $exp->get_goals() as $goal ) {

			if ( $goal->get_id() == $goal_id ) {
				// This function will automatically send a JSON and die.
				$goal->sync();
			}//end if

		}//end foreach

		wp_send_json( 'Exception: Goal «' . $goal_id . '» not found' );

	} catch( Exception $e ) {

		wp_send_json( 'Exception: ' . $e->getMessage() );

	}//end try

}//end nelioab_update_results()




add_action( 'wp_ajax_nelioab_get_quota', 'nelioab_get_quota' );
function nelioab_get_quota() {

	$green_light = '#69be7a';
	$green_dark  = '#54ae65';
	$orange_light = '#e28f25';
	$orange_dark  = '#e38000';
	$red_light = '#cc4444';
	$red_dark  = '#c81212';

	$result = array(
		'available'        => '',
		'lightColor'       => $green_light,
		'darkColor'        => $green_dark,
		'monthly'          => '',
		'normalWidth'      => '0',
		'extraWidth'       => '0',
		'quotaPercentage'  => '&mdash; %',
	);

	try {

		// BUILD THE QUOTA OBJECT
		require_once( NELIOAB_UTILS_DIR . '/backend.php' );
		$json_data = NelioABBackend::remote_get( sprintf(
			NELIOAB_BACKEND_URL . '/customer/%s/check',
			NelioABAccountSettings::get_customer_id()
		) );

		$json_data = json_decode( $json_data['body'] );
		$quota = array(
			'regular' => 5000,
			'monthly' => 5000,
			'extra'   => 0,
		);

		if ( isset( $json_data->quota ) ) {
			$quota['regular'] = $json_data->quota + $json_data->quotaExtra;
		}

		if ( isset( $json_data->quotaPerMonth ) ) {
			$quota['monthly'] = $json_data->quotaPerMonth;
		}

		if ( $quota['regular'] > $quota['monthly'] ) {
			$diff = $quota['regular'] - $quota['monthly'];
			$quota['extra'] = $diff;
			$quota['regular'] = $quota['monthly'];
		}

		// COMPUTE THE RESULTS
		$result['available'] = number_format_i18n( $quota['regular'] );
		$result['monthly'] = '/ ' . number_format_i18n( $quota['monthly'] );
		if ( $quota['extra'] > 0 ) {
			$result['monthly'] .= ' ' . sprintf( __( '(+%s extra)', 'nelioab' ),
				number_format_i18n( $quota['extra'], 0 ) );
		}//end if

		if ( $quota['extra'] > 0 ) {
			$extra = $quota['extra'];
			$max_extra = $quota['monthly'] / 2;
			if ( $extra > $max_extra ) {
				$extra = $max_extra;
			}
			$extra_perc = ( $extra / $max_extra  ) * 20;
		} else {
			$extra_perc = 0;
		}//end if
		$extra_perc = number_format( $extra_perc, 0, '.', '' );

		// Now let's compute the size of the regular bar
		if ( $quota['regular'] > 0 ) {
			$perc = ( $quota['regular'] / $quota['monthly'] ) * 100;
		} else {
			$perc = 0;
		}//end if
		$num_of_decs = 1;
		if ( 100 == $perc ) {
			$num_of_decs = 0;
		}//end if
		$result['quotaPercentage'] = number_format( $perc, $num_of_decs, '.', '' ) . ' %';

		$perc = number_format( $perc, 0, '.', '' );
		if ( $perc + $extra_perc > 100 ) {
			$perc = 100 - $extra_perc;
		}//end if

		if ( $perc > 0 ) {
			$result['normalWidth'] = $perc . '%';
		} else {
			$result['normalWidth'] = $perc;
		}//end if

		if ( $extra_perc > 0 ) {
			$result['extraWidth'] = $extra_perc . '%';
		} else {
			$result['extraWidth'] = $extra_perc;
		}//end if

		if ( $perc < 5 ) {
			$result['lightColor'] = $red_light;
			$result['darkColor']  = $red_dark;
		} else if ( $perc < 15 ) {
			$result['lightColor'] = $orange_light;
			$result['darkColor']  = $orange_dark;
		} else {
			$result['lightColor'] = $green_light;
			$result['darkColor']  = $green_dark;
		}//end if

	} catch ( Exception $e ) {
	}//end try


	wp_send_json( $result );

}//end nelioab_get_quota()

add_action( 'wp_ajax_nelioab_sync_experiment_status', 'nelioab_sync_experiment_status' );
function nelioab_sync_experiment_status() {

	include_once( NELIOAB_UTILS_DIR . '/backend.php' );
	include_once( NELIOAB_MODELS_DIR . '/account-settings.php' );
	include_once( NELIOAB_MODELS_DIR . '/experiments-manager.php' );
	$url = sprintf(
		NELIOAB_BACKEND_URL . '/site/%s/exp',
		NelioABAccountSettings::get_site_id()
	);
	$result = NelioABBackend::remote_post( $url );
	$json = json_decode( $result['body'] );

	$ids = array();
	if ( isset( $_REQUEST['ids'] ) ) {
		$ids = $_REQUEST['ids'];
	}//end if

	$key_ids = array();
	if ( isset( $_REQUEST['keyIds'] ) ) {
		$key_ids = $_REQUEST['keyIds'];
	}//end if

	if ( is_array( $ids ) && isset( $json->items ) && is_array( $json->items ) ) {

		$count = count( $ids );
		for ( $i = 0; $i < $count; ++$i ) {
			$id = $ids[ $i ];
			$key_id = $key_ids[ $i ];
			foreach ( $json->items as $ae_exp ) {
				if ( $ae_exp->key->id == $key_id && $ae_exp->status == NelioABExperiment::STATUS_FINISHED ) {
					$local_exp = NelioABExperimentsManager::get_experiment_by_id( $id );
					$local_exp->set_status( NelioABExperiment::STATUS_FINISHED );
					$local_exp->set_end_date( $ae_exp->finalization );
					$local_exp->save();
				}//endif
			}//end foreach
		}//end foreach

	}//end if

	wp_send_json( 'ok' );

}//end nelioab_sync_experiment_status

