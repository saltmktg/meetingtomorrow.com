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

function nelioab_migrate_cloud_exps_to_local() {

	try {
		update_option( 'nelioab_local_exps_migration_status', 'done' );

		include_once( NELIOAB_DIR . '/experiment-controllers/widget-experiment-controller.php' );
		include_once( NELIOAB_MODELS_DIR . '/account-settings.php' );
		include_once( NELIOAB_UTILS_DIR . '/backend.php' );

		$site_id = NelioABAccountSettings::get_site_id();
		if ( empty( $site_id ) ) {
			wp_send_json( 'ok' );
		}//end if

		$url = sprintf(
			NELIOAB_BACKEND_URL . '/site/%s/exp',
			NelioABAccountSettings::get_site_id()
		);
		$cloud_exps = NelioABBackend::remote_get( $url );
		$cloud_exps = json_decode( $cloud_exps['body'] );
		$cloud_exps = $cloud_exps->items;

		$local_exps = NelioABExperimentsManager::get_experiments();

		foreach ( $cloud_exps as $cloud_exp ) {
			$exp = false;

			foreach ( $local_exps as $local_exp ) {
				if ( $local_exp->get_key_id() == $cloud_exp->key->id ) {
					$exp = $local_exp;
					break;
				} else if ( get_post_meta( $local_exp->get_id(), 'nelioab_cloud_key_id', true ) == $cloud_exp->key->id ) {
					$exp = $local_exp;
					break;
				}//end if
			}//end foreach

			// If the local experiment doesn't exist, we'll create it.
			if ( ! $exp ) {

				// Let's save the cloud's key ID, so that later we can know that this
				// exp has been migrated to a local exp.
				$key_id = $cloud_exp->key->id;

				// Let's get the status.
				switch ( $cloud_exp->status ) {
					case NelioABExperiment::STATUS_DRAFT:
						$status = 'draft';
						break;
					case NelioABExperiment::STATUS_PAUSED:
						$status = 'nelioab_paused';
						break;
					case NelioABExperiment::STATUS_READY:
						$status = 'nelioab_ready';
						break;
					case NelioABExperiment::STATUS_RUNNING:
						$status = 'nelioab_running';
						break;
					case NelioABExperiment::STATUS_FINISHED:
						$status = 'nelioab_finished';
						break;
					case NelioABExperiment::STATUS_TRASH:
						$status = 'trash';
						break;
					case NelioABExperiment::STATUS_SCHEDULED:
						$cloud_exp->status = NelioABExperiment::STATUS_READY;
						$status = 'nelioab_ready';
						break;
					default:
						$status = 'draft';
				}

				// If the experiment is not running and it hasn't finished, we should
				// fix all its IDs.
				if ( $cloud_exp->status != NelioABExperiment::STATUS_RUNNING &&
						$cloud_exp->status != NelioABExperiment::STATUS_FINISHED ) {

					// The experiment's ID.
					$cloud_exp->key->id = -1;

					// Goal IDs.
					if ( isset( $cloud_exp->goals ) ) {
						$id = -9000;
						foreach ( $cloud_exp->goals as &$goal ) {
							$goal->key->id = $id;
							--$id;
						}//end foreach
					}//end if

					// Alternative IDs.
					if ( isset( $cloud_exp->alternatives ) ) {
						$id = -9000;
						$alt_mapping_ids = array();
						foreach ( $cloud_exp->alternatives as &$alt ) {
							$alt_mapping_ids[ $alt->key->id ] = $id;
							$alt->key->id = $id;
							--$id;
						}//end foreach

					}//end if

				}//end if

				// Now, we can save the post.
				$post = array(
					'post_name'    => 'nelioab-local-exp-' . microtime(),
					'post_title'   => $cloud_exp->name,
					'post_content' => urlencode( json_encode( $cloud_exp ) ),
					'post_status'  => $status,
					'post_type'    => 'nelioab_local_exp',
				);

				$new_local_id = wp_insert_post( $post );

				if ( is_int( $new_local_id ) && $new_local_id > 0 ) {
					update_post_meta( $new_local_id, 'nelioab_cloud_key_id', $key_id );
				}//end if

				// Finally, if the experiment is a widget experiment, we need to remap
				// alternative widget sets to new IDs.
				if ( $cloud_exp->status != NelioABExperiment::STATUS_RUNNING &&
						$cloud_exp->status != NelioABExperiment::STATUS_FINISHED &&
				    $cloud_exp->kind == NelioABExperiment::WIDGET_ALT_EXP_STR ) {

					$widgets_in_experiments = NelioABWidgetExpAdminController::get_widgets_in_experiments();
					foreach ( $widgets_in_experiments as &$aux ) {
						if ( $aux['exp'] == $key_id ) {
							$aux['exp'] = $new_local_id;
						}//end if
					}//end foreach
					NelioABWidgetExpAdminController::set_widgets_in_experiments( $widgets_in_experiments );

					NelioABWidgetExpAdminController::update_alternatives_ids(
						$new_local_id, $alt_mapping_ids
					);

				}//end if

			}//end if

		}//end foreach

		// Migrate .
		$menus_in_exps = get_option( 'nelioab_menus_in_experiments', array() );
		foreach ( $menus_in_exps as &$exp_and_alt ) {
			if ( is_array( $exp_and_alt ) && isset( $exp_and_alt['exp'] ) ) {
				$exp_and_alt = $exp_and_alt['exp'];
			}//end if
		}//end foreach
		update_option( 'nelioab_menus_in_experiments', $menus_in_exps );

	} catch ( Exception $e ) {
		wp_send_json( 'Error: ' . $e->getMessage() );
	}//end try

	wp_send_json( 'ok' );

}//end nelioab_migrate_cloud_exps_to_local

add_action( 'wp_ajax_nelioab_migrate_cloud_exps_to_local', 'nelioab_migrate_cloud_exps_to_local' );

