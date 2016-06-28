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


if ( !class_exists( 'NelioABGlobalAlternativeExperiment' ) ) {

	require_once( NELIOAB_MODELS_DIR . '/alternatives/alternative-experiment.php' );

	/**
	 * Abstract class representing a global A/B Experiment.
	 *
	 * In order to create an instance of this class, one must use of its
	 * concrete subclasses.
	 *
	 * @package \NelioABTesting\Models\Experiments\AB
	 * @since PHPDOC
	 */
	abstract class NelioABGlobalAlternativeExperiment extends NelioABAlternativeExperiment {

		/**
		 * PHPDOC
		 *
		 * @since PHPDOC
		 * @var array
		 */
		private $ori;

		/**
		 * Creates a new instance of this class.
		 *
		 * This constructor might be used by the concrete subclasses. It sets all
		 * attributes to their default values.
		 *
		 * @param int $id PHPDOC
		 *
		 * @return NelioABGlobalAlternativeExperiment a new instance of this class.
		 *
		 * @see self::clear
		 *
		 * @since PHPDOC
		 */
		public function __construct( $id ) {
			parent::__construct( $id );
		}


		// @Overrides
		public function is_global() {
			return true;
		}


		// @Overrides
		public function clear() {
			parent::clear();
			$this->ori = array( -1 );
		}


		// @Overrides
		public function set_winning_alternative_using_id( $id ) {
			if ( $this->get_originals_id() == $id )
				$winning_alt = $this->get_original();
			else
				$winning_alt = $this->get_alternative_by_id( $id );
			$this->set_winning_alternative( $winning_alt );
		}


		/**
		 * Returns PHPDOC
		 *
		 * @return array PHPDOC
		 *
		 * @since PHPDOC
		 */
		public function get_origins() {
			return $this->ori;
		}


		/**
		 * PHPDOC
		 *
		 * @param array $ori PHPDOC
		 *
		 * @return void
		 *
		 * @since PHPDOC
		 */
		public function set_origins( $ori ) {
			$this->ori = $ori;
		}


		/**
		 * PHPDOC
		 *
		 * @param int $ori PHPDOC
		 *
		 * @return void
		 *
		 * @since PHPDOC
		 */
		public function add_origin( $ori ) {
			array_push( $this->ori, $ori );
		}


		// @Implements
		protected function determine_proper_status() {
			if ( count( $this->get_goals() ) == 0 )
				return NelioABExperiment::STATUS_DRAFT;

			foreach ( $this->get_goals() as $goal ) {
				/** @var NelioABGoal $goal */
				if ( !$goal->is_ready() )
					return NelioABExperiment::STATUS_DRAFT;
			}

			return NelioABExperiment::STATUS_READY;
		}


		// @Implements
		public function do_remove() {

			// Nothing to be done.

		}//end do_remove()


		// @Implements
		public function do_save() {

			// Nothing to be done.

		}//end do_save()

		// @Implements
		public function pre_start() {
			// If the experiment is already running, quit
			if ( $this->get_status() == NelioABExperiment::STATUS_RUNNING )
				return;

			// Checking whether the experiment can be started or not...
			require_once( NELIOAB_UTILS_DIR . '/backend.php' );
			require_once( NELIOAB_MODELS_DIR . '/experiments-manager.php' );
			$running_exps = NelioABExperimentsManager::get_running_experiments();
			$this_exp_origins = $this->get_origins();
			array_push( $this_exp_origins, -1 );

			foreach ( $running_exps as $running_exp ) {
				/** @var NelioABExperiment $running_exp */

				switch ( $running_exp->get_type() ) {

					case NelioABExperiment::THEME_ALT_EXP:
						$err_str = sprintf(
							__( 'The experiment cannot be started, because there is a theme experiment running. Please, stop the experiment named «%s» before starting the new one.', 'nelioab' ),
							$running_exp->get_name() );
						throw new Exception( $err_str, NelioABErrCodes::EXPERIMENT_CANNOT_BE_STARTED );

					case NelioABExperiment::CSS_ALT_EXP:
						/** @var NelioABGlobalAlternativeExperiment $running_exp */
						foreach( $this_exp_origins as $origin_id ) {
							if ( in_array( $origin_id, $running_exp->get_origins() ) ) {
								$err_str = sprintf(
									__( 'The experiment cannot be started, because there is a CSS experiment running. Please, stop the experiment named «%s» before starting the new one.', 'nelioab' ),
									$running_exp->get_name() );
								throw new Exception( $err_str, NelioABErrCodes::EXPERIMENT_CANNOT_BE_STARTED );
							}
						}
						break;

					case NelioABExperiment::WIDGET_ALT_EXP:
						/** @var NelioABGlobalAlternativeExperiment $running_exp */
						foreach( $this_exp_origins as $origin_id ) {
							if ( in_array( $origin_id, $running_exp->get_origins() ) ) {
								$err_str = sprintf(
									__( 'The experiment cannot be started, because there is a Widget experiment running. Please, stop the experiment named «%s» before starting the new one.', 'nelioab' ),
									$running_exp->get_name() );
								throw new Exception( $err_str, NelioABErrCodes::EXPERIMENT_CANNOT_BE_STARTED );
							}
						}
						break;

					case NelioABExperiment::MENU_ALT_EXP:
						/** @var NelioABGlobalAlternativeExperiment $running_exp */
						foreach( $this_exp_origins as $origin_id ) {
							if ( in_array( $origin_id, $running_exp->get_origins() ) ) {
								$err_str = sprintf(
									__( 'The experiment cannot be started, because there is a Menu experiment running. Please, stop the experiment named «%s» before starting the new one.', 'nelioab' ),
									$running_exp->get_name() );
								throw new Exception( $err_str, NelioABErrCodes::EXPERIMENT_CANNOT_BE_STARTED );
							}
						}
						break;

					case NelioABExperiment::HEATMAP_EXP:
						if ( $this->get_type() != NelioABExperiment::WIDGET_ALT_EXP ) {
							$err_str = __( 'The experiment cannot be started, because there is one (or more) heatmap experiments running. Please make sure to stop any running heatmap experiment before starting the new one.', 'nelioab' );
							throw new Exception( $err_str, NelioABErrCodes::EXPERIMENT_CANNOT_BE_STARTED );
						}
						break;

				}
			}

		}//end pre_start()


		// @Implements
		public function do_stop() {
			$url = sprintf(
				NELIOAB_BACKEND_URL . '/exp/global/%s/stop',
				$this->get_key_id()
			);
			NelioABBackend::remote_post( $url );
			$this->set_status( NelioABExperiment::STATUS_FINISHED );
		}//end do_stop()


		// @Implements
		public function get_exp_kind_url_fragment() {
			return 'global';
		}


		// @Implements
		public function encode_for_appengine() {

			// 1. ADD ALTERNATIVES TO THE RESULT.
			$alternatives = array();
			$alt = $this->get_original();
			array_push(
				$alternatives,
				$alt->json4local( $this->get_id(), $this->get_textual_type() )
			);
			foreach ( $this->get_alternatives() as $alt ) {
				if ( ! $alt->was_removed() ) {
					array_push(
						$alternatives,
						$alt->json4local( $this->get_id(), $this->get_textual_type() )
					);
				}
			}

			// 2. PREPARE THE OBJECT.
			$result = parent::encode_for_appengine();
			$result['key']['kind']    = 'GlobalAlternativeExperiment';
			$result['origin']         = array( -1 );
			$result['testsTitleOnly'] = false;
			$result['showHeatmap']    = $this->are_heatmaps_tracked();
			$result['alternatives']   = $alternatives;

			return $result;

		}//end encode_for_appengine()


	}//NelioABGlobalAlternativeExperiment

}

