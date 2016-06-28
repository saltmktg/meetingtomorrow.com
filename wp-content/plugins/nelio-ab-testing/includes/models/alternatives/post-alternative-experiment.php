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


if ( !class_exists( 'NelioABPostAlternativeExperiment' ) ) {

	require_once( NELIOAB_MODELS_DIR . '/alternatives/alternative-experiment.php' );

	/**
	 * PHPDOC
	 *
	 * @package \NelioABTesting\Models\Experiments\AB
	 * @since PHPDOC
	 */
	class NelioABPostAlternativeExperiment extends NelioABAlternativeExperiment {

		/**
		 * PHPDOC
		 *
		 * @since PHPDOC
		 * @var int
		 */
		private $ori;


		/**
		 * PHPDOC
		 *
		 * @since PHPDOC
		 * @var boolean|string
		 */
		private $post_type = false;


		// @Override
		public function __construct( $id ) {
			parent::__construct( $id );
			$this->set_type( NelioABExperiment::NO_TYPE_SET );
			$this->set_post_type();
		}


		// @Override
		public function clear() {
			parent::clear();
			$this->ori = new NelioABAlternative();
			$this->track_heatmaps( true );
		}


		// @Override
		public function set_type( $type ) {
			parent::set_type( $type );
			if ( $type == NelioABExperiment::HEADLINE_ALT_EXP ||
				 $type == NelioABExperiment::CPT_ALT_EXP )
				$this->track_heatmaps( false );
			else
				$this->track_heatmaps( true );
		}


		// @Override
		public function get_original() {
			if ( !is_object( $this->ori ) ) {
				$aux = new NelioABAlternative();
				$aux->set_value( $this->ori );
				$this->ori = $aux;
			}
			return $this->ori;
		}


		// @Implements
		public function get_originals_id() {
			$ori_alt = $this->get_original();
			return $ori_alt->get_value();
		}


		// @Override
		public function get_related_post_id() {
			$this->get_originals_id();
		}


		/**
		 * Returns PHPDOC
		 *
		 * @return string PHPDOC
		 *
		 * @since PHPDOC
		 */
		public function get_post_type() {
			if ( empty( $this->post_type ) || !$this->post_type )
				$this->determine_post_type();
			return $this->post_type;
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
			/** @var NelioABAlternative $ori_alt */
			$ori_alt = $this->ori;
			$ori_alt->set_value( $ori );
			$this->determine_post_type();
		}


		/**
		 * PHPDOC
		 *
		 * @return void
		 *
		 * @since 4.1.0
		 */
		private function determine_post_type() {
			// Setting type
			$post = get_post( $this->get_originals_id(), ARRAY_A );
			if ( isset( $post ) ) {
				if ( $post['post_type'] == 'page' ) {
					$this->set_type( NelioABExperiment::PAGE_ALT_EXP );
					$this->set_post_type( 'page' );
				}
				else if ( $post['post_type'] == 'post' ) {
					$this->set_type( NelioABExperiment::POST_ALT_EXP );
					$this->set_post_type( 'post' );
				}
				else {
					$this->set_type( NelioABExperiment::CPT_ALT_EXP );
					$this->set_post_type( $post['post_type'] );
				}
			}
		}


		/**
		 * PHPDOC
		 *
		 * @param boolean|string $post_type PHPDOC
		 *
		 * @return void
		 *
		 * @since PHPDOC
		 */
		public function set_post_type( $post_type = false ) {
			$this->post_type = $post_type;
		}


		// @Implements
		public function set_winning_alternative_using_id( $id ) {
			$winning_alt = false;
			if ( $this->get_originals_id() == $id ) {
				$winning_alt = $this->get_original();
			}
			else {
				$alts = $this->get_alternatives();
				foreach ( $alts as $aux ) {
					/** @var NelioABAlternative $aux */
					if ( $aux->get_value() == $id )
						$winning_alt = $aux;
				}
			}
			$this->set_winning_alternative( $winning_alt );
		}


		/**
		 * PHPDOC
		 *
		 * @param string $name         PHPDOC
		 * @param string $post_type    PHPDOC
		 * @param string $current_type PHPDOC
		 *
		 * @return boolean|int PHPDOC
		 *
		 * @since PHPDOC
		 */
		public function create_empty_alternative( $name, $post_type, $current_type = 'post' ) {
			switch ( $post_type ) {
				case NelioABExperiment::PAGE_ALT_EXP:
					$post_type = 'page';
					break;
				case NelioABExperiment::POST_ALT_EXP:
					$post_type = 'post';
					break;
				case NelioABExperiment::CPT_ALT_EXP:
					$post_type = $current_type;
					break;
				default:
					return false;
			}

			$post = array(
				'post_type'    => $post_type,
				'post_title'   => $name,
				'post_content' => '',
				'post_excerpt' => '',
				'post_status'  => 'draft',
				'post_name'    => 'nelioab_' . rand( 1, 10 ),
			);

			// Retrieve original post
			$ori_post = get_post( $this->get_originals_id(), ARRAY_A );
			if ( $ori_post )
				$post['post_author'] = $ori_post['post_author'];

			$post_id = wp_insert_post( $post, true );
			if ( is_wp_error( $post_id ) ) {
				return false;
			}

			// Prepare custom metadata
			update_post_meta( $post_id, '_is_nelioab_alternative', 'true' );

			// Update the post_name
			$aux = get_post( $post_id, ARRAY_A );
			$aux['post_name'] = 'nelioab_' . $post_id;
			wp_update_post( $aux );

			return $post_id;
		}


		/**
		 * PHPDOC
		 *
		 * @param string        $name        PHPDOC
		 * @param int           $src_post_id PHPDOC
		 * @param boolean|array $ori_post    PHPDOC
		 *
		 * @return boolean|int PHPDOC
		 *
		 * @since PHPDOC
		 */
		public function create_alternative_copying_content( $name, $src_post_id, $ori_post = false ) {
			require_once( NELIOAB_UTILS_DIR . '/wp-helper.php' );

			// Retrieve original post
			$src_post = get_post( $src_post_id, ARRAY_A );
			if ( !$src_post )
				return false;

			if ( $ori_post && $src_post['post_type'] != $ori_post['post_type'] ) {
				switch ( $ori_post['post_type'] ) {
					case 'page':
						$aux = NelioABExperiment::PAGE_ALT_EXP;
						break;
					case 'post':
						$aux = NelioABExperiment::POST_ALT_EXP;
						break;
					default:
						$aux = NelioABExperiment::CPT_ALT_EXP;
				}
				return $this->create_empty_alternative( $name, $aux, $ori_post['post_type'] );
			}

			// Create new empty post
			$post_data = array(
				'post_author'  => $src_post['post_author'],
				'post_type'    => $src_post['post_type'],
				'post_title'   => $src_post['post_title'],
				'post_content' => $src_post['post_content'],
				'post_excerpt' => $src_post['post_excerpt'],
				'post_status'  => 'draft',
				'post_name'    => 'nelioab_' . rand( 1, 10 ),
			);
			$new_post_id = wp_insert_post( $post_data, true );
			if ( is_wp_error( $new_post_id ) ) {
				return false;
			}

			// Prepare custom metadata
			update_post_meta( $new_post_id, '_is_nelioab_alternative', 'true' );

			// Update the post_name
			$new_post = get_post( $new_post_id, ARRAY_A );
			$new_post['post_name'] = 'nelioab_' . $new_post_id;
			wp_update_post( $new_post );

			// Override all information
			NelioABWpHelper::overwrite( $new_post_id, $src_post_id );

			// Custom Permalinks compatibility
			require_once( NELIOAB_UTILS_DIR . '/custom-permalinks-support.php' );
			if ( NelioABCustomPermalinksSupport::is_plugin_active() )
				NelioABCustomPermalinksSupport::remove_custom_permalink( $new_post_id );

			return $new_post_id;
		}


		// @Implements
		protected function determine_proper_status() {
			if ( count( $this->get_alternatives() ) <= 0 )
				return NelioABExperiment::STATUS_DRAFT;

			if ( $this->get_originals_id() < 0 )
				return NelioABExperiment::STATUS_DRAFT;

			if ( $this->get_type() != NelioABExperiment::HEADLINE_ALT_EXP ) {
				if ( count( $this->get_goals() ) == 0 )
					return NelioABExperiment::STATUS_DRAFT;
				foreach ( $this->get_goals() as $goal ) {
					/** @var NelioABGoal $goal */
					if ( !$goal->is_ready() )
						return NelioABExperiment::STATUS_DRAFT;
				}
			}

			return NelioABExperiment::STATUS_READY;
		}


		// @Implements
		public function do_save() {

			// 1. UPDATE ALTERNATIVE DATA.
			$ori_post = get_post( $this->get_originals_id(), ARRAY_A );
			foreach ( $this->get_alternatives() as $alt ) {

				// Delete alternatives pages/posts.
				if ( $alt->was_removed() ) {
					if ( $alt->get_value() > 0 ) {
						wp_delete_post( $alt->get_value(), true );
					}//end if
				} else if ( $alt->get_value() < 0 ) {

					// And create the new ones.
					if ( $alt->is_based_on_another_element() ) {
						$new_id = $this->create_alternative_copying_content(
							$alt->get_name(), $alt->get_base_element(), $ori_post
						);
						if ( $new_id ) {
							$alt->set_value( $new_id );
						}//end if
					} else {
						$new_id = $this->create_empty_alternative( $alt->get_name(), $this->get_type(), $this->get_post_type() );
						if ( $new_id ) {
							$alt->set_value( $new_id );
						}//end if
					}//end if

				}//end if

			}//end foreach

			// 2. SET META "_is_nelioab_alternative" WITH THE ID OF THE EXPERIMENT
			foreach ( $this->get_alternatives() as $alt ) {
				$pid = $alt->get_value();
				if ( is_int( $pid ) && $pid > 0 ) {
					$value = $this->get_id() . ',' . $this->get_status();
					update_post_meta( $pid, '_is_nelioab_alternative', $value );
				}
			}

		}//end do_save()


		// @Implements
		public function get_exp_kind_url_fragment() {
			return 'post';
		}


		// @Implements
		public function do_remove() {

			foreach ( $this->get_alternatives() as $alt ) {
				/** @var NelioABAlternative $alt */
				wp_delete_post( $alt->get_value(), true );
			}

		}//end do_remove()


		// @Implements
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
				     $running_exp->get_type() != NelioABExperiment::CPT_ALT_EXP  &&
				     $running_exp->get_type() != NelioABExperiment::HEADLINE_ALT_EXP )
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
					else /* if ( $running_exp->get_type() == NelioABExperiment::HEADLINE_ALT_EXP ) */ {
						$err_str = sprintf(
							__( 'The experiment cannot be started, because there is another experiment that is testing the title of the same page. Please, stop the experiment named «%s» before starting the new one.', 'nelioab' ),
							$running_exp->get_name() );
					}
					throw new Exception( $err_str, NelioABErrCodes::EXPERIMENT_CANNOT_BE_STARTED );
				}
			}

			// If everything is OK, we can start it!

			// (keep in mind that, if it is a title experiment, we'll create the goal in AE

			// And there we go!
			$ori_post = get_post( $this->get_originals_id() );
			if ( $ori_post && $this->get_type() != NelioABExperiment::HEADLINE_ALT_EXP ) {
				foreach ( $this->get_alternatives() as $alt ) {
					/** @var NelioABAlternative $alt */
					$alt_post = get_post( $alt->get_value() );
					if ( $alt_post ) {
						if ( get_post_meta( $alt_post->ID, '_nelioab_hide_discussion', true ) === 'true' ) {
							$alt_post->comment_status = 'closed';
						} else {
							$alt_post->comment_status = $ori_post->comment_status;
						}
						wp_update_post( $alt_post );
					}
				}
			}

		}//end pre_start()


		// @Implements
		public function do_stop() {
			require_once( NELIOAB_UTILS_DIR . '/backend.php' );
			$url = sprintf(
					NELIOAB_BACKEND_URL . '/exp/post/%s/stop',
					$this->get_key_id()
				);
			NelioABBackend::remote_post( $url );
		}//end do_stop()


		// @Implements
		public function set_status( $status ) {
			parent::set_status( $status );
			foreach ( $this->get_alternatives() as $alt ) {
				/** @var NelioABAlternative $alt */
				$value = $this->get_id() . ',' . $this->get_status();
				if ( $alt->get_value() > 0 ) {
					update_post_meta( $alt->get_value(), "_is_nelioab_alternative", $value );
					update_post_meta( $alt->get_value(), "_nelioab_original_id", $this->get_originals_id() );
				}
			}
		}


		// @Override
		public function post_duplicate( $json, $exp_id ) {

			$alternatives = array();
			foreach ( $json->alternatives as $alt ) {
				$new_id = $this->create_alternative_copying_content( $alt->name, $alt->value );
				if ( $new_id ) {
					$alt->value = $new_id;
					array_push( $alternatives, $alt );
				}//end if
			}//end foreach
			$json->alternatives = $alternatives;

			if ( count( $alternatives ) == 0 ) {
				$json->status = NelioABExperiment::STATUS_DRAFT;
			}//end if

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
			$result['testsTitleOnly'] = false;
			$result['showHeatmap']    = $this->are_heatmaps_tracked();
			$result['postType']       = $this->get_post_type();
			$result['alternatives']   = $alternatives;

			return $result;

		}//end encode_for_appengine()


		// @Implements
		public static function load( $post ) {

			if ( is_int( $post ) ) {
				$post = get_post( $post );
			}//end if

			if ( ! $post ) {
				require_once( NELIOAB_UTILS_DIR . '/backend.php' );
				$err = NelioABErrCodes::EXPERIMENT_ID_NOT_FOUND;
				throw new Exception( NelioABErrCodes::to_string( $err ), $err );
			}//end if

			$exp = new NelioABPostAlternativeExperiment( $post->ID );
			$json_data = $exp->post_content2json( $post->post_content );

			$exp->set_key_id( $json_data->key->id );
			$exp->set_name( $json_data->name );
			if ( isset( $json_data->description ) ) {
				$exp->set_description( $json_data->description );
			}
			$exp->set_creation_date( $json_data->creation );
			$exp->set_type_using_text( $json_data->kind );
			$exp->set_original( $json_data->originalPost );
			if ( isset( $json_data->postType ) ) {
				$exp->set_post_type( $json_data->postType );
			}
			$exp->set_status( $json_data->status );
			$exp->set_finalization_mode( $json_data->finalizationMode );
			if ( isset( $json_data->finalizationModeValue ) ) {
				$exp->set_finalization_value( $json_data->finalizationModeValue );
			}
			$exp->track_heatmaps( false );
			if ( isset( $json_data->showHeatmap ) && $json_data->showHeatmap  ) {
				$exp->track_heatmaps( $json_data->showHeatmap );
			}
			if ( isset( $json_data->start ) ) {
				$exp->set_start_date( $json_data->start );
			}
			if ( isset( $json_data->finalization ) ) {
				$exp->set_end_date( $json_data->finalization );
			}

			if ( isset( $json_data->goals ) ) {
				NelioABExperiment::load_goals_from_json( $exp, $json_data->goals );
			}

			$alternatives = array();
			if ( isset( $json_data->alternatives ) ) {
				foreach ( $json_data->alternatives as $json_alt ) {
					$alt = new NelioABAlternative( $json_alt->key->id );
					$alt->set_name( $json_alt->name );
					$alt->set_value( $json_alt->value );
					array_push( $alternatives, $alt );
				}
			}
			$exp->set_alternatives( $alternatives );

			return $exp;
		}//end load()

	}//end class

}

