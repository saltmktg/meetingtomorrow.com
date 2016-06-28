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


if ( ! class_exists( 'NelioABAltExpGoal' ) ) {

	require_once( NELIOAB_MODELS_DIR . '/goals/actions/action.php' );
	require_once( NELIOAB_MODELS_DIR . '/goals/actions/page-accessed-action.php' );
	require_once( NELIOAB_MODELS_DIR . '/goals/actions/form-submission-action.php' );
	require_once( NELIOAB_MODELS_DIR . '/goals/actions/click-element-action.php' );
	require_once( NELIOAB_MODELS_DIR . '/goals/actions/wc-order-completed-action.php' );
	require_once( NELIOAB_MODELS_DIR . '/goals/actions/phone-call-action.php' );
	require_once( NELIOAB_MODELS_DIR . '/goal-results/alternative-experiment-goal-result.php' );

	require_once( NELIOAB_MODELS_DIR . '/goals/goal.php' );


	/**
	 * PHPDOC
	 *
	 * @package \NelioABTesting\Models\Goals
	 * @since PHPDOC
	 */
	class NelioABAltExpGoal extends NelioABGoal {


		/**
		 * PHPDOC
		 *
		 * @since PHPDOC
		 * @var array
		 */
		private $actions;


		/**
		 * PHPDOC
		 *
		 * @param NelioABExperiment $exp PHPDOC
		 *
		 * @return NelioABAltExpGoal PHPDOC
		 *
		 * @sine PHPDOC
		 */
		public function __construct( $exp ) {
			parent::__construct( $exp );
			$this->set_kind( NelioABGoal::ALTERNATIVE_EXPERIMENT_GOAL );
			$this->actions = array();
		}


		/**
		 * PHPDOC
		 *
		 * @return void
		 *
		 * @since PHPDOC
		 */
		public function clear_actions() {
			$this->actions = array();
		}


		/**
		 * PHPDOC
		 *
		 * @return array PHPDOC
		 *
		 * @since PHPDOC
		 */
		public function get_actions() {
			return $this->actions;
		}


		/**
		 * PHPDOC
		 *
		 * @param NelioABAction $action PHPDOC
		 *
		 * @return void
		 *
		 * @since PHPDOC
		 */
		public function add_action( $action ) {
			array_push( $this->actions, $action );
		}


		/**
		 * PHPDOC
		 *
		 * @param int $post_id PHPDOC
		 *
		 * @return boolean PHPDOC
		 *
		 * @since PHPDOC
		 */
		public function includes_internal_page( $post_id ) {
			foreach( $this->actions as $action ) {
				/** @var NelioABAction $action */
				switch( $action->get_type() ) {
					case NelioABAction::PAGE_ACCESSED:
					case NelioABAction::POST_ACCESSED:
					case NelioABAction::EXTERNAL_PAGE_ACCESSED:
						/** @var NelioABPageAccessedAction $action */
						if ( !$action->is_internal() )
							continue;
						if ( $action->get_reference() == $post_id )
							return true;
					default:
						// Nothing to be done
				}
			}
			return false;
		}


		/**
		 * PHPDOC
		 *
		 * @param string $url PHPDOC
		 *
		 * @return boolean PHPDOC
		 *
		 * @since PHPDOC
		 */
		public function includes_external_page( $url ) {
			foreach( $this->actions as $action ) {
				/** @var NelioABAction $action */
				switch( $action->get_type() ) {
					case NelioABAction::PAGE_ACCESSED:
					case NelioABAction::POST_ACCESSED:
					case NelioABAction::EXTERNAL_PAGE_ACCESSED:
						/** @var NelioABPageAccessedAction $action */
						if ( !$action->is_external() )
							continue;
						if ( $action->get_reference() == $url )
							return true;
					default:
						// Nothing to be done
				}
			}
			return false;
		}


		/**
		 * PHPDOC
		 *
		 * @return boolean PHPDOC
		 *
		 * @since PHPDOC
		 */
		public function is_ready() {
			if ( $this->has_to_be_deleted() )
				return true;
			return count( $this->get_actions() ) > 0;
		}


		/**
		 * PHPDOC
		 *
		 * @param NelioABExperiment $exp  PHPDOC
		 * @param object            $json PHPDOC
		 *
		 * @return NelioABAltExpGoal PHPDOC
		 *
		 * @since PHPDOC
		 */
		public static function decode_from_appengine( $exp, $json ) {

			$result = new NelioABAltExpGoal( $exp );

			$result->set_id( $json->key->id );
			$result->set_name( $json->name );
			$result->set_benefit( $json->benefit );
			$result->set_as_main_goal( $json->isMainGoal );
			$ae_actions = array();

			if ( isset( $json->pageAccessedActions ) ) {
				foreach ( $json->pageAccessedActions as $action ) {
					$action = (array)$action;
					$action['_type'] = 'page-accessed-action';
					$action = (object)$action;
					array_push( $ae_actions, $action );
				}
			}

			if ( isset( $json->formActions ) ) {
				foreach ( $json->formActions as $action ) {
					$action = (array)$action;
					$action['_type'] = 'form-action';
					$action = (object)$action;
					array_push( $ae_actions, $action );
				}
			}

			if ( isset( $json->clickActions ) ) {
				foreach ( $json->clickActions as $action ) {
					$action = (array)$action;
					$action['_type'] = 'click-element-action';
					$action = (object)$action;
					array_push( $ae_actions, $action );
				}
			}

			if ( isset( $json->orderCompletedActions ) ) {
				foreach ( $json->orderCompletedActions as $action ) {
					$action = (array)$action;
					$action['_type'] = 'order-completed-action';
					$action = (object)$action;
					array_push( $ae_actions, $action );
				}
			}

			if ( isset( $json->phoneAction ) ) {
				$action = $json->phoneAction;
				$action = (array)$action;
				$action['_type'] = 'phone-call-action';
				$action = (object)$action;
				array_push( $ae_actions, $action );
			}

			usort( $ae_actions, array( 'NelioABAltExpGoal', 'sort_goals' ) );
			foreach ( $ae_actions as $action ) {
				/** @var object $action */
				switch( $action->_type ) {
					case 'page-accessed-action':
						$result->add_action( NelioABPageAccessedAction::decode_from_appengine( $action ) );
						break;
					case 'form-action':
						$result->add_action( NelioABFormSubmissionAction::decode_from_appengine( $action ) );
						break;
					case 'click-element-action':
						$result->add_action( NelioABClickElementAction::decode_from_appengine( $action ) );
						break;
					case 'order-completed-action':
						$result->add_action( NelioABWooCommerceOrderCompletedAction::decode_from_appengine( $action ) );
						break;
					case 'phone-call-action':
						$result->add_action( NelioABPhoneCallAction::decode_from_appengine( $action ) );
						break;
				}
			}
			return $result;
		}


		/**
		 * PHPDOC
		 *
		 * @return array PHPDOC
		 *
		 * @since PHPDOC
		 */
		public function encode_for_appengine() {
			$res = array(
				'key'        => array(
					'id' => $this->get_id(),
				),
				'name'       => $this->get_name(),
				'benefit'    => $this->get_benefit(),
				'kind'       => $this->get_textual_kind(),
				'isMainGoal' => $this->is_main_goal(),
				'order'      => $this->get_order(),
			);

			$page_accessed_actions = array();
			$form_actions = array();
			$click_actions = array();
			$order_completed_actions = array();
			$phone_call_action = false;

			$order = 0;
			foreach ( $this->get_actions() as $action ) {
				/** @var NelioABAction $action */
				$encoded_action = $action->encode_for_appengine();

				switch( $action->get_type() ) {

					case NelioABAction::PAGE_ACCESSED:
					case NelioABAction::POST_ACCESSED:
					case NelioABAction::EXTERNAL_PAGE_ACCESSED:
						$order++;
						$encoded_action['order'] = $order;
						array_push( $page_accessed_actions, $encoded_action );
						break;

					case NelioABAction::SUBMIT_CF7_FORM:
					case NelioABAction::SUBMIT_GRAVITY_FORM:
						$order++;
						$encoded_action['order'] = $order;
						array_push( $form_actions, $encoded_action );
						break;

					case NelioABAction::CLICK_ELEMENT:
						$order++;
						$encoded_action['order'] = $order;
						array_push( $click_actions, $encoded_action );
						break;

					case NelioABAction::WC_ORDER_COMPLETED:
						$order++;
						$encoded_action['order'] = $order;
						array_push( $order_completed_actions, $encoded_action );
						break;

					case NelioABAction::PHONE_CALL:
						$order++;
						$encoded_action['order'] = $order;
						$phone_call_action = $encoded_action;
						break;

				}
			}

			$res['pageAccessedActions'] = $page_accessed_actions;
			$res['formActions'] = $form_actions;
			$res['clickActions'] = $click_actions;
			$res['orderCompletedActions'] = $order_completed_actions;
			$res['benefit'] = $this->get_benefit();
			$res['benefitUnit'] = '$';

			if ( $phone_call_action ) {
				$res['phoneAction'] = $phone_call_action;
			}

			return $res;
		}


		/**
		 * PHPDOC
		 *
		 * @param object $a PHPDOC
		 * @param object $b PHPDOC
		 *
		 * @return int PHPDOC
		 *
		 * @since PHPDOC
		 */
		public static function sort_goals( $a, $b ) {
			if ( isset( $a->order ) && isset( $b->order ) )
				return $a->order - $b->order;
			if ( isset( $a->order ) )
				return -1;
			if ( isset( $b->order ) )
				return 1;
			return 0;
		}


		/**
		 * PHPDOC
		 *
		 * @return NelioABAltExpGoalResult PHPDOC
		 *
		 * @since PHPDOC
		 */
		public function get_results() {
			$results    = new NelioABAltExpGoalResult();
			/** @var NelioABAlternativeExperiment $experiment */
			$experiment = $this->get_experiment();

			// Results object here.
			$goal_results = $this->get_local_results();

			if ( $goal_results ) {
				$json_data = json_decode( urldecode( $goal_results['data'] ), true );
			}//end if

			if ( empty( $json_data ) ) {
				$err = NelioABErrCodes::RESULTS_NOT_AVAILABLE_YET;
				throw new Exception( NelioABErrCodes::to_string( $err ), $err );
			}//end if

			$results->set_total_visitors( $json_data['totalVisitors'] );
			$results->set_total_conversions( $json_data['totalConversions'] );
			$results->set_total_conversion_rate( $json_data['totalConversionRate'] );
			$results->set_visitors_history( $json_data['historyVisitors'] );
			$results->set_conversions_history( $json_data['historyConversions'] );
			$results->set_first_update( $json_data['firstUpdate'] );
			$results->set_last_update( $json_data['lastUpdate'] );

			// NELIO LOCAL EXPS UPDATE. Auto stop experiment (if needed).
			if ( $experiment->get_status() === NelioABExperiment::STATUS_RUNNING ) {
				if ( isset( $json_data['expStatus'] ) &&
						$json_data['expStatus'] === NelioABExperiment::STATUS_FINISHED ) {
					$experiment->stop();
				}//end if
			}//end if

			$confidence = 0;
			if ( isset( $json_data['resultStatus'] ) )
				$confidence = $json_data['confidenceInResultStatus'];
			$results->set_summary_status(
				NelioABGTest::get_result_status_from_str( $json_data['resultStatus'] ),
				$confidence );

			$alt_res = new NelioABAltStats( true ); // Original
			$alt_res->set_name( __( 'Original', 'nelioab' ) );
			$alt_res->set_alt_id( $json_data['originalStats']['altId'] );
			$alt_res->set_num_of_visitors( $json_data['originalStats']['visitors'] );
			$alt_res->set_num_of_conversions( $json_data['originalStats']['conversions'] );
			$alt_res->set_conversion_rate( $json_data['originalStats']['conversionRate'] );
			if ( isset( $json_data['originalStats']['historyVisitors'] ) )
				$alt_res->set_visitors_history( $json_data['originalStats']['historyVisitors'] );
			if ( isset( $json_data['originalStats']['historyConversions'] ) )
				$alt_res->set_conversions_history( $json_data['originalStats']['historyConversions'] );
			$results->add_alternative_results( $alt_res );

			if ( is_array( $json_data['alternativeStats'] ) ) {
				foreach ( $json_data['alternativeStats'] as $json_alt ) {
					$alt_res = new NelioABAltStats();

					$alternative = null;
					foreach ( $experiment->get_alternatives() as $alt ) {
						/** @var NelioABAlternative $alt */
						if ( $alt->get_id() == $json_alt['altId'] )
							$alternative = $alt;
					}

					if ( $alternative == null ) {
						foreach ( $experiment->get_alternatives() as $alt ) {
							if ( $alt->applies_to_post_id( $json_alt['altId'] ) )
								$alternative = $alt;
						}
					}

					if ( $alternative == null )
						continue;

					$alt_res->set_name( $alternative->get_name() );
					$alt_res->set_alt_id( $json_alt['altId'] );
					$alt_res->set_num_of_visitors( $json_alt['visitors'] );
					$alt_res->set_num_of_conversions( $json_alt['conversions'] );
					$alt_res->set_conversion_rate( $json_alt['conversionRate'] );
					$alt_res->set_improvement_factor( $json_alt['improvementFactor'] );
					if ( isset( $json_alt['historyVisitors'] ) )
						$alt_res->set_visitors_history( $json_alt['historyVisitors'] );
					if ( isset( $json_alt['historyConversions'] ) )
						$alt_res->set_conversions_history( $json_alt['historyConversions'] );

					$results->add_alternative_results( $alt_res );
				}
			}

			if ( is_array( $json_data['gTests'] ) ) {
				foreach ( $json_data['gTests'] as $stats ) {
					$g = new NelioABGTest( $stats['message'], $experiment->get_originals_id() );
					$min_ver = NULL;
					if ( isset( $stats['minVersion'] ) ) {
						$min_ver = $stats['minVersion'];
						$g->set_min( $min_ver );
					}

					$max_ver = NULL;
					if ( isset( $stats['maxVersion'] ) ) {
						$max_ver = $stats['maxVersion'];
						$g->set_max( $max_ver );
					}

					if ( isset( $stats['gtest'] ) )
						$g->set_gtest( $stats['gtest'] );
					if ( isset( $stats['pvalue'] ) )
						$g->set_pvalue( $stats['pvalue'] );
					if ( isset( $stats['certainty'] ) )
						$g->set_certainty( $stats['certainty'] );

					$g->set_min_name( __( 'Unknown', 'nelioab' ) );
					$g->set_max_name( __( 'Unknown', 'nelioab' ) );
					$alts = $experiment->get_alternatives();
					for( $i = 0; $i < count( $alts ); ++$i ) {
						$alt = $alts[$i];
						$short_name = sprintf( __( 'Alternative %s', 'nelioab' ), ( $i + 1 ) );
						if ( $alt->get_identifiable_value() == $min_ver || $alt->get_id() == $min_ver )
							$g->set_min_name( $short_name, $alt->get_name() );
						if ( $alt->get_identifiable_value() == $max_ver || $alt->get_id() == $max_ver )
							$g->set_max_name( $short_name, $alt->get_name() );
					}
					if ( $experiment->get_originals_id() == $min_ver )
						$g->set_min_name( __( 'Original', 'nelioab' ) );
					if ( $experiment->get_originals_id() == $max_ver )
						$g->set_max_name( __( 'Original', 'nelioab' ) );

					$results->add_gtest( $g );
				}
			}

			return $results;
		}//end get_results()


		/**
		 * PHPDOC
		 *
		 * @since PHPDOC
		 */
		public function sync() {

			/** @var NelioABAlternativeExperiment $experiment */
			$experiment = $this->get_experiment();
			$goal_results = $this->get_local_results();
			$previous_total_visitors = 0;
			$previous_total_conversions = 0;
			$previous_last_update = 0;

			if ( $goal_results ) {

				if ( isset( $goal_results['data'] ) ) {
					$json_data = json_decode( urldecode( $goal_results['data'] ) );
					if ( isset( $json_data->totalVisitors ) ) {
						$previous_total_visitors = $json_data->totalVisitors;
					}//end if
					if ( isset( $json_data->totalConversions ) ) {
						$previous_total_conversions = $json_data->totalConversions;
					}//end if
				}//end if

				if ( isset( $json_data->lastUpdate ) ) {
					$previous_last_update = $json_data->lastUpdate;
				}//end if

				if ( isset( $goal_results['last_check'] ) ) {
					$last_check = $goal_results['last_check'];
				} else {
					$last_check = 0;
				}//end if

				if ( $this->is_new_enough( $last_check ) ) {
					wp_send_json( 'nelioab-results-are-ok' );
				}//end if

			}//end if

			// Now, maybe we didn't have any results, or they were too old. In
			// any case, if we don't have results ready, we need to go look for
			// them in AE:
			try {

				$url = sprintf(
					NELIOAB_BACKEND_URL . '/goal/alternativeexp/%s/result',
					$this->get_id()
				);

				$json_data = null;
				$json_data = NelioABBackend::remote_get( $url );
				$json_data = json_decode( urldecode( $json_data['body'] ), true );

				if ( ! isset( $json_data['beErrCode'] ) || $json_data['beErrCode'] !== 0 ) {
					include_once( NELIOAB_UTILS_DIR . '/backend.php' );
					$err = NelioABErrCodes::BACKEND_UNKNOWN_ERROR;
					throw new Exception( NelioABErrCodes::to_string( $err ), $err );
				}//end if

				unset( $json_data['etag'] );

				$last_update = $json_data['lastUpdate'];

				// The results we just got from AE might be too old too...
				// If that was the case, then we need to make sure that,
				// in just a few minutes, when the user refreshes the UI,
				// we'll make a new request to AE to see if it has already
				// computed the new data. Otherwise, we're good to go.
				if ( ! $this->is_new_enough( strtotime( $last_update ) ) ) {
					$last_check_time = $this->get_time_for_recheck_in_five_minutes();
				} else {
					$last_check_time = time();
				}//end if

				$found = false;
				$goal_result_set = get_post_meta( $experiment->get_id(), 'nelioab_goal_results', true );
				if ( empty( $goal_result_set ) ) {
					$goal_result_set = array();
				}//end if

				$num_of_goals = count( $goal_result_set );
				for ( $i = 0; $i < $num_of_goals; ++$i ) {

					if ( $goal_result_set[ $i ]['id'] === $this->get_id() ) {
						$goal_result_set[ $i ]['last_check'] = $last_check_time;
						$goal_result_set[ $i ]['data'] = urlencode( json_encode( $json_data ) );
						$found = true;
						break;
					}//end if

				}//end foreach

				if ( ! $found ) {
					array_push( $goal_result_set, array(
						'id'         => $this->get_id(),
						'last_check' => $last_check_time,
						'data'       => urlencode( json_encode( $json_data ) ),
					) );
				}//end if

				update_post_meta( $experiment->get_id(), 'nelioab_goal_results', $goal_result_set );

				if ( $previous_last_update === $last_update ) {
					wp_send_json( 'nelioab-results-are-ok' );
				} else if ( $previous_total_visitors == $json_data->totalVisitors && $previous_total_conversions == $json_data->totalConversions ) {
					wp_send_json( 'nelioab-results-are-ok' );
				} else {
					wp_send_json( 'nelioab-new-results-available' );
				}//end if

			} catch ( Exception $e ) {

				wp_send_json( 'Exception: ' . $e->getMessage() . "\nGoal ID:" . $this->get_id() );

			}//end try

		}//end sync()

		/**
		 * PHPDOC
		 *
		 * @return array PHPDOC
		 *
		 * @since PHPDOC
		 */
		public function json4js() {
			$benefit = $this->get_benefit();
			if ( NelioABSettings::get_def_conv_value() == $benefit )
				$benefit = '';
			$result = array(
					'id'      => $this->get_id(),
					'name'    => $this->get_name(),
					'benefit' => $benefit,
					'actions' => array()
				);

			foreach ( $this->get_actions() as $action ) {
				/** @var NelioABAction $action */
				$json_action = $action->json4js();
				if ( $json_action )
					array_push( $result['actions'], $json_action );
			}

			return $result;
		}


		/**
		 * PHPDOC
		 *
		 * @param object            $json_goal PHPDOC
		 * @param NelioABExperiment $exp       PHPDOC
		 *
		 * @return NelioABAltExpGoal PHPDOC
		 *
		 * @since PHPDOC
		 */
		public static function build_goal_using_json4js( $json_goal, $exp ) {
			$goal = new NelioABAltExpGoal( $exp );

			// If the goal was new, but it was also deleted, do nothing...
			if ( isset( $json_goal->wasDeleted) && $json_goal->wasDeleted ) {
				if ( $json_goal->id < 0 )
					return false;
				else
					$goal->set_to_be_deleted( true );
			}

			$goal->set_id( $json_goal->id );
			$goal->set_name( $json_goal->name );
			if ( isset( $json_goal->benefit ) && !empty( $json_goal->benefit ) )
				$goal->set_benefit( $json_goal->benefit );
			else
				$goal->set_benefit( NelioABSettings::get_def_conv_value() );

			foreach ( $json_goal->actions as $json_action ) {
				$action = NelioABAction::build_action_using_json4js( $json_action );
				if ( $action )
					$goal->add_action( $action );
			}

			return $goal;
		}

	}//NelioABAltExpGoal

}

