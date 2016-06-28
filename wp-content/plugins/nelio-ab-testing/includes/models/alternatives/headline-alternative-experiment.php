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


if ( !class_exists( 'NelioABHeadlineAlternativeExperiment' ) ) {

	require_once( NELIOAB_MODELS_DIR . '/alternatives/headline-alternative.php' );
	require_once( NELIOAB_MODELS_DIR . '/alternatives/alternative-experiment.php' );

	/**
	 * PHPDOC
	 *
	 * @package \NelioABTesting\Models\Experiments\AB
	 * @since PHPDOC
	 */
	class NelioABHeadlineAlternativeExperiment extends NelioABAlternativeExperiment {

		/**
		 * PHPDOC
		 *
		 * PHPDOC: This should probably be inherited from PostAlternative
		 *
		 * @since PHPDOC
		 * @var int
		 */
		private $ori;


		// @Override
		public function __construct( $id ) {
			parent::__construct( $id );
			$this->set_type( NelioABExperiment::HEADLINE_ALT_EXP );
		}


		// @Override
		public function clear() {
			parent::clear();
			$this->ori = new NelioABHeadlineAlternative();
			$this->track_heatmaps( false );
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
		public function set_original( $ori ) {
			$aux = $ori;
			if ( !is_object( $ori ) ) {
				$aux = new NelioABHeadlineAlternative();
				$aux->set_value( $ori );
			}

			if ( !is_array( $aux->get_value() ) ) {
				$id = $aux->get_value();
				$aux->set_value_compat( $id, $id );
			}

			$this->ori = $aux;

			$post = get_post( $this->get_originals_id() );
			if ( $post )
				$aux->set_name( $post->post_title );
		}


		// @Override
		public function get_original() {
			return $this->ori;
		}


		/**
		 * Returns PHPDOC
		 *
		 * @return int PHPDOC
		 *
		 * @since PHPDOC
		 */
		public function get_originals_id() {
			/** @var NelioABHeadlineAlternative $ori_alt */
			$ori_alt = $this->get_original();
			$val = $ori_alt->get_value();
			return $val['id'];
		}


		// @Override
		public function get_related_post_id() {
			$this->get_originals_id();
		}


		// @Override
		public function set_winning_alternative_using_id( $id ) {
			$winning_alt = false;
			if ( $this->get_originals_id() == $id ) {
				$winning_alt = $this->get_original();
			}
			else {
				$alts = $this->get_alternatives();
				foreach ( $alts as $aux ) {
					/** @var NelioABHeadlineAlternative $aux */
					$val = $aux->get_value();
					if ( $val['id'] == $id )
						$winning_alt = $aux;
				}
			}
			$this->set_winning_alternative( $winning_alt );
		}


		// @Override
		protected function determine_proper_status() {
			if ( count( $this->get_alternatives() ) <= 0 )
				return NelioABExperiment::STATUS_DRAFT;

			if ( $this->get_originals_id() < 0 )
				return NelioABExperiment::STATUS_DRAFT;

			return NelioABExperiment::STATUS_READY;
		}


		// @Override
		public function load_json4js_alternatives( $json_alts ) {
			foreach ( $json_alts as $json_alt ) {
				if ( isset( $json_alt->isNew ) && $json_alt->isNew &&
				     isset( $json_alt->wasDeleted ) && $json_alt->wasDeleted )
					continue;
				$alt = NelioABHeadlineAlternative::build_alternative_using_json4js( $json_alt );
				$this->add_alternative( $alt );
			}
		}


		/**
		 * PHPDOC
		 *
		 * @param array $headline_info PHPDOC
		 *
		 * @return array PHPDOC
		 *
		 * @since PHPDOC
		 */
		public function fix_image_id_in_value( $headline_info ) {
			if ( !isset( $headline_info['image_id'] ) || 'inherit' == $headline_info['image_id'] ) {
				$alt = get_post_thumbnail_id( $this->get_originals_id() );
				if ( $alt )
					$headline_info['image_id'] = intval( $alt );
				else
					$headline_info['image_id'] = intval( 0 );
			}
			return $headline_info;

		}


		// @Override
		public function add_alternative( $alt ) {
			$fake_post_id = -1;
			foreach ( $this->get_alternatives() as $aux ) {
				/** @var NelioABAlternative $aux */
				if ( $aux->get_value() <= $fake_post_id )
					$fake_post_id = $aux->get_value() - 1;
			}
			$val = $alt->get_value();
			$val['id'] = $fake_post_id;
			$alt->set_value( $val );
			parent::add_alternative( $alt );
		}


		// @Override
		public function do_save() {

			// Nothing to be done.

		}//end do_save()


		// @Implements
		public function get_exp_kind_url_fragment() {
			return 'post';
		}


		// @Override
		public function do_remove() {

			// Nothing to be done.

		}//end do_remove()




		// @Override
		public function pre_start() {

			// If the experiment is already running, quit
			if ( $this->get_status() == NelioABExperiment::STATUS_RUNNING )
				return;

			if ( get_post_status( $this->get_originals_id() ) == 'draft' ) {
				if ( get_post_type( $this->get_originals_id() ) == 'page' ) {
					$err_str = __( 'The experiment cannot be started, because the tested page is a draft.', 'nelioab' );
				} else {
					$err_str = __( 'The experiment cannot be started, because the tested post is a draft.', 'nelioab' );
				}
				throw new Exception( $err_str, NelioABErrCodes::EXPERIMENT_CANNOT_BE_STARTED );
			}

			// Checking whether the experiment can be started or not...
			require_once( NELIOAB_UTILS_DIR . '/backend.php' );
			require_once( NELIOAB_MODELS_DIR . '/experiments-manager.php' );
			$running_exps = NelioABExperimentsManager::get_running_experiments();
			foreach ( $running_exps as $running_exp ) {
				/** @var NelioABExperiment $running_exp */

				if ( $running_exp->get_type() != NelioABExperiment::PAGE_ALT_EXP &&
				     $running_exp->get_type() != NelioABExperiment::POST_ALT_EXP &&
				     $running_exp->get_type() != NelioABExperiment::CPT_ALT_EXP &&
				     $running_exp->get_type() != NelioABExperiment::HEADLINE_ALT_EXP &&
				     $running_exp->get_type() != NelioABExperiment::WC_PRODUCT_SUMMARY_ALT_EXP )
					continue;

				if ( $running_exp->get_originals_id() == $this->get_originals_id() ) {
					if ( $running_exp->get_type() == NelioABExperiment::PAGE_ALT_EXP ) {
						$err_str = sprintf(
							__( 'The experiment cannot be started, because there is another experiment running that is testing the same page. Please, stop the experiment named «%s» before starting the new one.', 'nelioab' ),
							$running_exp->get_name() );
					}
					else if ( $running_exp->get_type() == NelioABExperiment::POST_ALT_EXP ) {
						$err_str = sprintf(
							__( 'The experiment cannot be started, because there is another experiment running that is testing the same post. Please, stop the experiment named «%s» before starting the new one.', 'nelioab' ),
							$running_exp->get_name() );
					}
					else if ( $running_exp->get_type() == NelioABExperiment::CPT_ALT_EXP ) {
						$err_str = sprintf(
							__( 'The experiment cannot be started, because there is another experiment running that is testing the same custom post. Please, stop the experiment named «%s» before starting the new one.', 'nelioab' ),
							$running_exp->get_name() );
					}
					else if ( $running_exp->get_type() == NelioABExperiment::HEADLINE_ALT_EXP ) {
						$err_str = sprintf(
							__( 'The experiment cannot be started, because there is another experiment that is testing the title of the same page. Please, stop the experiment named «%s» before starting the new one.', 'nelioab' ),
							$running_exp->get_name() );
					}
					else if ( $running_exp->get_type() == NelioABExperiment::WC_PRODUCT_SUMMARY_ALT_EXP ) {
						$err_str = sprintf(
							__( 'The experiment cannot be started, because there is another experiment that is testing the same product. Please, stop «%s» before starting the new experiment.', 'nelioab' ),
							$running_exp->get_name() );
					}
					throw new Exception( $err_str, NelioABErrCodes::EXPERIMENT_CANNOT_BE_STARTED );
				}
			}

		}


		// @Implements
		public function do_stop() {
			require_once( NELIOAB_UTILS_DIR . '/backend.php' );
			$url = sprintf(
					NELIOAB_BACKEND_URL . '/exp/post/%s/stop',
					$this->get_key_id()
				);
			NelioABBackend::remote_post( $url );
		}


		// @Override
		public function post_duplicate( $json, $exp_id ) {

			$json->goals = array();
			return $json;

		}//end post_duplicate()


		// @Implements
		public function encode_for_appengine() {

			// 1. ADD ALTERNATIVES TO THE RESULT.
			$alternatives = array();
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
			$result['key']['kind']    = 'PostAlternativeExperiment';
			$result['originalPost']   = $this->get_originals_id();
			$result['testsTitleOnly'] = true;
			$result['showHeatmap']    = $this->are_heatmaps_tracked();
			$result['postType']       = 'post';
			$result['alternatives']   = $alternatives;

			return $result;

		}//end encode_for_appengine()


		// @Override
		public static function load( $post ) {
			if ( is_int( $post ) ) {
				$post = get_post( $post );
			}

			$exp = new NelioABHeadlineAlternativeExperiment( $post->ID );
			$json_data = $exp->post_content2json( $post->post_content );

			$exp->set_key_id( $json_data->key->id );
			$exp->set_name( $json_data->name );
			if ( isset( $json_data->description ) )
				$exp->set_description( $json_data->description );
			$exp->set_type_using_text( $json_data->kind );
			$exp->set_original( $json_data->originalPost );
			$exp->set_creation_date( $json_data->creation );
			$exp->set_status( $json_data->status );
			$exp->set_finalization_mode( $json_data->finalizationMode );
			if ( isset( $json_data->finalizationModeValue ) )
				$exp->set_finalization_value( $json_data->finalizationModeValue );
			$exp->track_heatmaps( false );
			if ( isset( $json_data->showHeatmap ) && $json_data->showHeatmap  )
				$exp->track_heatmaps( $json_data->showHeatmap );
			if ( isset( $json_data->start ) )
				$exp->set_start_date( $json_data->start );
			if ( isset( $json_data->finalization ) )
				$exp->set_end_date( $json_data->finalization );

			if ( isset( $json_data->goals ) )
				NelioABExperiment::load_goals_from_json( $exp, $json_data->goals );

			$alternatives = array();
			if ( isset( $json_data->alternatives ) ) {
				foreach ( $json_data->alternatives as $json_alt ) {
					$alt = new NelioABHeadlineAlternative( $json_alt->key->id );
					$alt->set_name( $json_alt->name );
					if ( NelioABExperiment::HEADLINE_ALT_EXP_STR == $json_alt->kind ) {
						$alt->set_value( json_decode( $json_alt->value, true ) );
					} else if ( NelioABExperiment::WC_PRODUCT_SUMMARY_ALT_EXP_STR == $json_alt->kind ) {
						$alt->set_value( json_decode( $json_alt->value, true ) );
					} else {
						// This else part is for compatibility with previous Title exp
						$alt->set_value_compat( $json_alt->value, $json_data->originalPost );
					}//end if
					array_push( $alternatives, $alt );
				}
			}
			$exp->set_alternatives( $alternatives );

			return $exp;
		}

	}//NelioABHeadlineAlternativeExperiment

}

