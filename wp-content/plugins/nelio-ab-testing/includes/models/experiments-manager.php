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

if ( !class_exists( 'NelioABExperimentsManager' ) ) {

	require_once( NELIOAB_UTILS_DIR . '/data-manager.php' );
	require_once( NELIOAB_MODELS_DIR . '/alternatives/post-alternative-experiment.php' );
	require_once( NELIOAB_MODELS_DIR . '/alternatives/headline-alternative-experiment.php' );
	require_once( NELIOAB_MODELS_DIR . '/alternatives/css-alternative-experiment.php' );
	require_once( NELIOAB_MODELS_DIR . '/alternatives/theme-alternative-experiment.php' );
	require_once( NELIOAB_MODELS_DIR . '/alternatives/widget-alternative-experiment.php' );
	require_once( NELIOAB_MODELS_DIR . '/alternatives/menu-alternative-experiment.php' );
	require_once( NELIOAB_MODELS_DIR . '/woocommerce/product-summary-alternative-experiment.php' );
	require_once( NELIOAB_MODELS_DIR . '/heatmap-experiment.php' );

	/**
	 * A class containing some useful functions for managing experiments.
	 *
	 * This class was originally designed for loading, saving, and keeping track
	 * of our customers' experiments. With the latest releases, however, we've
	 * shifted some of its logic (such as, for instance, how to parse a JSON
	 * result to build an experiment instance) to specific experiment classes.
	 *
	 * @package \NelioABTesting\Models\Experiments
	 * @since 1.0.10
	 */
	class NelioABExperimentsManager implements iNelioABDataManager {

		/**
		 * List of experiments defined within the current site.
		 *
		 * If the variable has not yet been initialized, it's false.
		 *
		 * @since 3.4.0
		 * @var boolean|array
		 */
		private static $experiments = false;


		/**
		 * List of running experiments defined within the current site.
		 *
		 * If the variable has not yet been initialized, it's false.
		 *
		 * @since 1.4.0
		 * @var boolean|array
		 */
		private static $running_experiments = false;


		/**
		 * List of relevant running experiments defined within the current site.
		 *
		 * A relevant running experiment is a running experiment in which the
		 * current visitor participates. Therefore, this only makes sense if the
		 * plugin has been "launched" by a normal request from a regular visitor.
		 *
		 * If the variable has not yet been initialized, it's false.
		 *
		 * @since 4.0.0
		 * @var boolean|array
		 */
		private static $relevant_running_experiments = false;


		// @Implements
		public function list_elements() {
			return self::get_experiments();
		}//end list_experiments()


		/**
		 * Returns the list of experiments.
		 *
		 * @return array the list of experiments.
		 *
		 * @since 1.0.10
		 */
		public static function get_experiments() {
			require_once( NELIOAB_MODELS_DIR . '/goals/goals-manager.php' );

			// Retrieve the experiments from the current static class.
			if ( self::$experiments ) {
				return self::$experiments;
			}

			$aux = array();
			self::$experiments = $aux;

			$posts = get_posts( array(
				'post_type' => 'nelioab_local_exp',
				'posts_per_page' => -1,
				'post_status' => 'any',
			) );
			foreach ( $posts as $post ) {
				try {
					$exp = self::get_experiment_by_id( $post->ID );
				} catch( Exception $e ) {
					continue;
				}//end if
				if ( $post->post_status === 'nelioab_deleted' ) {
					continue;
				}//end if
				if ( $exp ) {
					$aux[$post->ID] = $exp;
				}//end if
			}

			$posts = get_posts( array(
				'post_type' => 'nelioab_local_exp',
				'posts_per_page' => -1,
				'post_status' => 'trash',
			) );
			foreach ( $posts as $post ) {
				try {
					$exp = self::get_experiment_by_id( $post->ID );
				} catch( Exception $e ) {
					continue;
				}//end if
				if ( $exp ) {
					$aux[$post->ID] = $exp;
				}//end if
			}

			self::$experiments = $aux;
			return self::$experiments;
		}//end get_experiments()


		/**
		 * Returns the list of experiments.
		 *
		 * @return array the list of experiments.
		 *
		 * @since 1.0.10
		 */
		public static function refresh() {

			self::$experiments = false;

		}//end refresh()

		/**
		 * TODO
		 *
		 * @param int $id   TODO
		 * @param int $type the type of the experiment we want to retrieve.
		 *
		 * @return NelioABExperiment the experiment whose ID is `$id` and whose type is `$type`.
		 *
		 * @throws Exception `EXPERIMENT_ID_NOT_FOUND`
		 *                   This exception is thrown if the experiment was not
		 *                   found in AppEngine.
		 *
		 * @since 1.0.10
		 */
		public static function get_experiment_by_id( $id, $type = false ) {

			require_once( NELIOAB_UTILS_DIR . '/backend.php' );

			if ( self::$experiments ) {
				foreach ( self::$experiments as $candidate_id => $exp ) {
					if ( $candidate_id === $id ) {
						return $exp;
					}
				}
			}

			$post = get_post( $id );

			if ( ! $post ) {
				$err = NelioABErrCodes::EXPERIMENT_ID_NOT_FOUND;
				throw new Exception( NelioABErrCodes::to_string( $err ), $err );
			}//end if

			if ( ! $type ) {
				$aux = json_decode( urldecode( $post->post_content ) );
				if ( ! $aux ) {
					$err = NelioABErrCodes::EXPERIMENT_ID_NOT_FOUND;
					throw new Exception( NelioABErrCodes::to_string( $err ), $err );
				}
				$type = NelioABExperiment::kind_to_type( $aux->kind );
			}

			require_once( NELIOAB_MODELS_DIR . '/goals/goals-manager.php' );
			/** @var NelioABExperiment $exp */
			switch( $type ) {
				case NelioABExperiment::POST_ALT_EXP:
				case NelioABExperiment::PAGE_ALT_EXP:
				case NelioABExperiment::PAGE_OR_POST_ALT_EXP:
				case NelioABExperiment::CPT_ALT_EXP:
					$exp = NelioABPostAlternativeExperiment::load( $post );
					break;

				case NelioABExperiment::HEADLINE_ALT_EXP:
					$exp = NelioABHeadlineAlternativeExperiment::load( $post );
					break;

				case NelioABExperiment::WC_PRODUCT_SUMMARY_ALT_EXP:
					$exp = NelioABProductSummaryAlternativeExperiment::load( $post );
					break;

				case NelioABExperiment::THEME_ALT_EXP:
					$exp = NelioABThemeAlternativeExperiment::load( $post );
					break;

				case NelioABExperiment::CSS_ALT_EXP:
					$exp = NelioABCssAlternativeExperiment::load( $post );
					break;

				case NelioABExperiment::WIDGET_ALT_EXP:
					$exp = NelioABWidgetAlternativeExperiment::load( $post );
					break;

				case NelioABExperiment::MENU_ALT_EXP:
					$exp = NelioABMenuAlternativeExperiment::load( $post );
					break;

				case NelioABExperiment::HEATMAP_EXP:
					$exp = NelioABHeatmapExperiment::load( $post );
					break;

				default:
					$err = NelioABErrCodes::EXPERIMENT_ID_NOT_FOUND;
					throw new Exception( NelioABErrCodes::to_string( $err ), $err );
			}

			$exp->mark_as_fully_loaded();

			return $exp;
		}


		/**
		 * Checks if the current user is allowed to manage a certain type of experiment.
		 * If she can't, an Exception is thrown.
		 *
		 * @param int     $type      the type of the experiment we want to remove.
		 * @param string  $action    the specific action a certain user is trying to perform.
		 * @param string  $exception throw an exception when the user can't manage the experiment.
		 *
		 * @return
		 *
		 * @throws Exception `YOU_DONT_HAVE_PERMISSION`
		 *
		 * @since 4.3.1
		 */
		public static function current_user_can( $exp, $action = 'anything', $exception = 'no-exceptions' ) {

			$user = wp_get_current_user();
			$user = $user->ID;
			$user_can = true;

			// Some global experiments
			$kinds = array( NelioABExperiment::THEME_ALT_EXP, NelioABExperiment::WIDGET_ALT_EXP, NelioABExperiment::MENU_ALT_EXP );
			if ( ! current_user_can( 'edit_theme_options' ) && in_array( $exp->get_type(), $kinds ) ) {
				$user_can = false;
			}

			// PAGES
			if ( $exp->get_type() === NelioABExperiment::PAGE_ALT_EXP ) {
				if ( ! current_user_can( 'delete_pages' ) ) {
					$user_can = false;
				} else if ( ! current_user_can( 'delete_others_pages' ) ) {
					$page = get_post( $exp->get_originals_id() );
					if ( $page && $page->post_author != $user ) {
						$user_can = false;
					}
				}
			}

			// POSTS, CPTs, and HEADLINES
			if ( $exp->get_type() === NelioABExperiment::POST_ALT_EXP ||
					$exp->get_type() === NelioABExperiment::HEADLINE_ALT_EXP ||
					$exp->get_type() === NelioABExperiment::CPT_ALT_EXP ) {
				if ( ! current_user_can( 'delete_posts' ) ) {
					$user_can = false;
				} else if ( ! current_user_can( 'delete_others_posts' ) ) {
					$post = get_post( $exp->get_originals_id() );
					if ( $post && $post->post_author != $user ) {
						$user_can = false;
					}
				}
			}

			// HEATMAPS
			if ( $exp->get_type() === NelioABExperiment::HEATMAP_EXP ) {
				$post = get_post( $exp->get_originals_id() );
				if ( $post ) {
					if ( $post->post_type == 'page' ) {
						if ( ! current_user_can( 'delete_pages' ) ) {
							$user_can = false;
						} else if ( ! current_user_can( 'delete_others_pages' ) &&
								$post->post_author != $user ) {
							$user_can = false;
						}
					} else {
						if ( ! current_user_can( 'delete_posts' ) ) {
							$user_can = false;
						} else if ( ! current_user_can( 'delete_others_posts' ) &&
								$post->post_author != $user ) {
							$user_can = false;
						}
					}
				}
			}

			// WOOCOMMERCE
			if ( $exp->get_type() === NelioABExperiment::WC_PRODUCT_SUMMARY_ALT_EXP ) {
				if ( ! current_user_can( 'manage_woocommerce' ) ) {
					$user_can = false;
				}
			}

			// NOTHING SPECIAL FOR CSS
			// ...

			if ( ! $user_can && 'throw-exception' === $exception ) {
				$err = NelioABErrCodes::YOU_DONT_HAVE_PERMISSION;
				throw new Exception( NelioABErrCodes::to_string( $err ), $err );
			}
			return $user_can;

		}


		/**
		 * Removes the experiment from AppEngine, as well as any local information it created.
		 *
		 * @param int $id   the ID of the experiment we want to remove.
		 * @param int $type the type of the experiment we want to remove.
		 *
		 * @throws Exception `EXPERIMENT_ID_NOT_FOUND`
		 *                   This exception is thrown if the experiment was not
		 *                   found in AppEngine.
		 *
		 * @see NelioABExperiment::remove
		 *
		 * @since 1.0.10
		 */
		public static function remove_experiment_by_id( $id, $type ) {

			$exp = self::get_experiment_by_id( $id, $type );
			$exp->remove();

		}//end remove_experiment_by_id()


		/**
		 * Returns the list of running experiments from the local cache.
		 *
		 * @return array the list of running experiments from the local cache.
		 *
		 * @since 1.0.10
		 */
		public static function get_running_experiments() {
			if ( self::$running_experiments )
				return self::$running_experiments;

			// LOAD. Improve using actual status.
			$aux = self::get_experiments();
			self::$running_experiments = array();
			foreach ( $aux as $exp ) {
				/** @var NelioABExperiment $exp */
				if ( $exp->get_status() === NelioABExperiment::STATUS_RUNNING ) {
					array_push( self::$running_experiments, $exp );
				}
			}
			return self::$running_experiments;

		}//end get_running_experiments()


		/**
		 * Returns the list of running experiments for which the current user has one alternative assigned.
		 *
		 * @return array the list of running experiments for which the current user has one alternative assigned.
		 *
		 * @see NelioABVisitor::get_experiment_ids_in_request
		 *
		 * @since 4.0.0
		 */
		public static function get_relevant_running_experiments() {
			if ( self::$relevant_running_experiments )
				return self::$relevant_running_experiments;

			$env_ids = NelioABVisitor::get_experiment_ids_in_request();
			$running_experiments = self::get_running_experiments();

			$relevant_running_experiments = array();
			foreach ( $running_experiments as $exp ) {
				/** @var NelioABExperiment $exp */
				$is_relevant = false;
				for ( $i = 0; $i < count( $env_ids ) && !$is_relevant; ++$i ) {
					if ( $exp->get_id() == $env_ids[$i] ) {
						$is_relevant = true;
					}
				}
				if ( $is_relevant ) {
					$already_in_array = false;
					foreach ( $relevant_running_experiments as $relevant_exp ) {
						/** @var NelioABExperiment $relevant_exp */
						if ( $relevant_exp->get_id() == $exp->get_id() ) {
							$already_in_array = true;
						}
					}
					if ( !$already_in_array ) {
						array_push( $relevant_running_experiments, $exp );
					}
				}
			}

			if ( NelioABVisitor::is_fully_loaded() ) {
				self::$relevant_running_experiments = $relevant_running_experiments ;
			} else {
				self::$relevant_running_experiments = false;
			}

			return $relevant_running_experiments;
		}//end get_relevant_running_experiments()


		/**
		 * Returns the most relevant information of Nelio A/B Testing (to be displayed in the dashboard).
		 *
		 * @return array {
		 *     Most relevant information of Nelio A/B Testing (to be displayed in the dashboard).
		 *
		 *     @type array $exps  List of running experiments.
		 *     @type array $quota Two integers: the amount of `used` quota and the `total` quota available.
		 * }
		 *
		 * @since 3.4.4
		 */
		public static function get_dashboard_summary() {

			// Including types of experiments...
			require_once( NELIOAB_MODELS_DIR . '/summaries/alt-exp-summary.php' );
			require_once( NELIOAB_MODELS_DIR . '/summaries/heatmap-exp-summary.php' );

			// LOCAL TODO. Obtain the quota from the option.
			$result = array(
				'exps'  => array(),
			);

			global $wpdb;
			$query = 'SELECT ID FROM ' . $wpdb->posts . ' WHERE ' .
						'post_type = \'nelioab_local_exp\' AND post_status = \'nelioab_running\'';
			$exp_ids = $wpdb->get_col( $query );

			$summaries = array();
			foreach ( $exp_ids as $id ) {

				try {
					$exp = NelioABExperimentsManager::get_experiment_by_id( $id );

					/** @var NelioABExperimentSummary $exp */
					switch ( $exp->get_type() ) {
						case NelioABExperiment::HEATMAP_EXP:
							$summary = new NelioABHeatmapExpSummary( $id );
							break;
						default:
							$summary = new NelioABAltExpSummary( $id );
					}

					$summary->build_summary( $exp );
					array_push( $summaries, $summary );

				} catch ( Exception $e ) {
				}//end try

			}//end foreach

			$result['exps'] = $summaries;

			return $result;
		}//end get_dashboard_summary()

	}//end class

}

