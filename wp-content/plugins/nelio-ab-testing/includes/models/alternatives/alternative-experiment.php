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


if ( !class_exists( 'NelioABAlternativeExperiment' ) ) {

	require_once( NELIOAB_MODELS_DIR . '/experiment.php' );
	require_once( NELIOAB_UTILS_DIR . '/backend.php' );

	require_once( NELIOAB_MODELS_DIR . '/alternatives/alternative.php' );
	require_once( NELIOAB_MODELS_DIR . '/alternatives/alternative-statistics.php' );
	require_once( NELIOAB_MODELS_DIR . '/alternatives/gtest.php' );
	require_once( NELIOAB_MODELS_DIR . '/goals/alternative-experiment-goal.php' );

	/**
	 * Abstract class representing an A/B Experiment.
	 *
	 * In order to create an instance of this class, one must use of its
	 * concrete subclasses.
	 *
	 * @package \NelioABTesting\Models\Experiments\AB
	 * @since PHPDOC
	 */
	abstract class NelioABAlternativeExperiment extends NelioABExperiment {

		/**
		 * PHPDOC
		 *
		 * @since PHPDOC
		 * @var array
		 */
		protected $alternatives;


		/**
		 * PHPDOC
		 *
		 * @since PHPDOC
		 * @var int
		 */
		private $winning_alternative;


		/**
		 * PHPDOC
		 *
		 * @since PHPDOC
		 * @var boolean
		 */
		private $track_heatmaps;


		/**
		 * Creates a new instance of this class.
		 *
		 * This constructor might be used by the concrete subclasses. It sets all
		 * attributes to their default values.
		 *
		 * @param int $id PHPDOC
		 *
		 * @return NelioABAlternativeExperiment a new instance of this class.
		 *
		 * @see self::clear
		 *
		 * @since PHPDOC
		 */
		public function __construct( $id ) {
			parent::__construct();
			$this->id = $id;
		}


		// @Override
		public function clear() {
			parent::clear();
			$this->alternatives = array();
			$this->track_heatmaps = false;
			$this->winning_alternative = false;
		}


		/**
		 * PHPDOC
		 *
		 * @param boolean $do_track PHPDOC
		 *
		 * @return void
		 *
		 * @since PHPDOC
		 */
		public function track_heatmaps( $do_track ) {
			$this->track_heatmaps = $do_track;
		}


		/**
		 * Returns PHPDOC
		 *
		 * @return boolean PHPDOC
		 *
		 * @since PHPDOC
		 */
		public function are_heatmaps_tracked() {
			return $this->track_heatmaps;
		}


		/**
		 * Returns PHPDOC
		 *
		 * @return array PHPDOC
		 *
		 * @since PHPDOC
		 */
		public function get_alternatives() {
			return $this->alternatives;
		}


		/**
		 * PHPDOC
		 *
		 * @param array $alts PHPDOC
		 *
		 * @return void
		 *
		 * @since PHPDOC
		 */
		public function set_alternatives( $alts ) {
			$this->alternatives = $alts;
		}


		/**
		 * Returns PHPDOC
		 *
		 * @return array PHPDOC
		 *
		 * @since PHPDOC
		 */
		public function get_json4js_alternatives() {
			$result = array();

			foreach ( $this->alternatives as $alt ) {
				/** @var NelioABAlternative $alt */
				if ( $alt->was_removed() )
					continue;
				$json_alt = $alt->json4js();
				array_push( $result, $json_alt );
			}

			return $result;
		}


		/**
		 * Returns PHPDOC
		 *
		 * @param int $id PHPDOC
		 *
		 * @return NelioABAlternative PHPDOC
		 *
		 * @since PHPDOC
		 */
		public function get_alternative_by_id( $id ) {
			foreach ( $this->get_alternatives() as $alt ) {
				/** @var NelioABAlternative $alt */
				if ( $alt->get_id() == $id )
					return $alt;
			}
			return false;
		}


		/**
		 * PHPDOC
		 *
		 * @return void
		 *
		 * @see self::save
		 *
		 * @since PHPDOC
		 */
		public function untrash() {
			$this->update_status_and_save( $this->determine_proper_status() );
		}


		/**
		 * PHPDOC
		 *
		 * @param int $status PHPDOC
		 *
		 * @return void
		 *
		 * @see self::save
		 *
		 * @since PHPDOC
		 */
		public function update_status_and_save( $status ) {
			if ( $this->get_id() < 0 )
				$this->save();

			$this->set_status( $status );
			$this->save();
		}


		/**
		 * PHPDOC
		 *
		 * @param NelioABAlternative $alt PHPDOC
		 *
		 * @return void
		 *
		 * @since PHPDOC
		 */
		public function add_alternative( $alt ) {
			array_push( $this->alternatives, $alt );
		}


		/**
		 * PHPDOC
		 *
		 * @param int $id PHPDOC
		 *
		 * @return void
		 *
		 * @since PHPDOC
		 */
		public function remove_alternative_by_id( $id ) {
			foreach ( $this->get_alternatives() as $alt ) {
				/** @var NelioABAlternative $alt */
				if ( $alt->get_id() == $id ) {
					$alt->mark_as_removed();
					return;
				}
			}
		}


		/**
		 * PHPDOC
		 *
		 * @param array $json_alts PHPDOC
		 *
		 * @return void
		 *
		 * @since PHPDOC
		 */
		public function load_json4js_alternatives( $json_alts ) {
			$this->alternatives = array();
			foreach ( $json_alts as $json_alt ) {
				if ( isset( $json_alt->isNew ) && $json_alt->isNew &&
				     isset( $json_alt->wasDeleted ) && $json_alt->wasDeleted )
					continue;
				$alt = NelioABAlternative::build_alternative_using_json4js( $json_alt );
				$this->add_alternative( $alt );
			}
		}


		/**
		 * PHPDOC
		 *
		 * @param NelioABAlternative $alt PHPDOC
		 *
		 * @return void
		 *
		 * @since PHPDOC
		 */
		protected function set_winning_alternative( $alt ) {
			$this->winning_alternative = $alt;
		}


		/**
		 * Returns PHPDOC
		 *
		 * @return NelioABAlternative PHPDOC
		 *
		 * @since PHPDOC
		 */
		public function get_winning_alternative() {
			return $this->winning_alternative;
		}


		/**
		 * Returns PHPDOC
		 *
		 * @return NelioABAlternative PHPDOC
		 *
		 * @since PHPDOC
		 */
		public abstract function get_original();


		/**
		 * PHPDOC
		 *
		 * @param int $id PHPDOC
		 *
		 * @return void
		 *
		 * @since PHPDOC
		 */
		public abstract function set_winning_alternative_using_id( $id );


		/**
		 * Returns PHPDOC
		 *
		 * @return int PHPDOC
		 *
		 * @since PHPDOC
		 */
		protected abstract function determine_proper_status();

	}//NelioABAlternativeExperiment

}

