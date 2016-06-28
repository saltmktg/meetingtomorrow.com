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


if ( !class_exists( 'NelioABMenuAlternativeExperiment' ) ) {

	require_once( NELIOAB_MODELS_DIR . '/alternatives/global-alternative-experiment.php' );

	/**
	 * PHPDOC
	 *
	 * @package \NelioABTesting\Models\Experiments\AB
	 * @since PHPDOC
	 */
	class NelioABMenuAlternativeExperiment extends NelioABGlobalAlternativeExperiment {

		/**
		 * PHPDOC
		 *
		 * @since PHPDOC
		 * @var array
		 */
		private $original_menu;


		// @Override
		public function clear() {
			parent::clear();
			$this->set_type( NelioABExperiment::MENU_ALT_EXP );
			$this->original_menu = $this->create_alternative_menu( 'FakeOriginalMenu' );
		}


		// @Override
		public function get_original() {
			return $this->original_menu;
		}


		// @Override
		public function get_originals_id() {
			/** @var NelioABAlternative $aux */
			$aux = $this->get_original();
			return $aux->get_id();
		}


		public function set_originals_id( $ae_id, $menu_id ) {
			/** @var NelioABAlternative $aux */
			$aux = $this->get_original();
			$aux->set_id( $ae_id );
			$aux->set_value( $menu_id );
		}


		// @Override
		public function set_alternatives( $alts ) {
			$aux = array();
			if ( count( $alts ) > 0 ) {
				$this->original_menu = $alts[0];
				for ( $i = 1; $i < count( $alts ); $i++ )
					array_push( $aux, $alts[$i] );
			}
			parent::set_alternatives( $aux );
		}

		/**
		 * Returns PHPDOC
		 *
		 * @param string $name PHPDOC
		 *
		 * @return NelioABAlternative PHPDOC
		 *
		 * @since PHPDOC
		 */
		public function create_alternative_menu( $name ) {
			$alts = $this->get_alternatives();
			$fake_post_id = -1;
			foreach ( $alts as $aux ) {
				/** @var NelioABAlternative $aux */
				if ( $aux->get_id() <= $fake_post_id )
					$fake_post_id = $aux->get_id() - 1;
			}
			$alt = new NelioABAlternative();
			$alt->set_id( $fake_post_id );
			$alt->set_name( $name );
			$alt->set_value( '' );
			return $alt;
		}


		// @Override
		protected function determine_proper_status() {
			if ( count( $this->get_alternatives() ) <= 0 )
				return NelioABExperiment::STATUS_DRAFT;
			return parent::determine_proper_status();
		}


		// @Override
		public function do_save() {

			require_once( NELIOAB_EXP_CONTROLLERS_DIR . '/menu-experiment-controller.php' );
			$controller = NelioABMenuExpAdminController::get_instance();

			$controller->begin();

			// Remove old alternative menus and link them to their alt object.
			foreach ( $this->get_alternatives() as $alt ) {

				/** @var NelioABAlternative $alt */
				if ( $alt->was_removed() ) {
					$controller->remove_alternative_menu( $alt->get_value() );
				}

			}//end if

			// Create new alternative menus and link them to their alt object.
			// We start at -9001, because -9000 is the original one.
			$id = -9001;
			foreach ( $this->get_alternatives() as $alt ) {

				if ( $alt->was_removed() ) {
					continue;
				}

				if ( $alt->get_value() == -1 ) {
					if ( $alt->is_based_on_another_element() ) {
						$menu_id = $controller->duplicate_menu_and_create_alternative(
							$alt->get_base_element(), $this->get_id() );
					} else {
						$menu_id = $controller->create_alternative_menu( $this->get_id() );
					}//end if

					$alt->set_value( $menu_id );
					$controller->link_menu_to_experiment( $menu_id, $this->get_id() );
				}

				$alt->set_id( $id );
				--$id;
			}

			$controller->commit();

		}


		// @Override
		public function do_remove() {
			require_once( NELIOAB_EXP_CONTROLLERS_DIR . '/menu-experiment-controller.php' );
			$controller = NelioABMenuExpAdminController::get_instance();

			// 1. Remove the local alternatives
			$controller->begin();
			foreach ( $this->get_alternatives() as $alt ) {
				/** @var NelioABAlternative $alt */
				$controller->remove_alternative_menu( $alt->get_value() );
			}
			$controller->commit();

			// 2. We remove the experiment itself
			parent::do_remove();
		}


		// @Override
		public function post_duplicate( $json, $exp_id ) {

			require_once( NELIOAB_EXP_CONTROLLERS_DIR . '/menu-experiment-controller.php' );
			$controller = NelioABMenuExpAdminController::get_instance();

			$alternatives = array( $json->alternatives[0] );
			unset( $json->alternatives[0] );

			$controller->begin();
			foreach ( $json->alternatives as $alt ) {
				/** @var NelioABAlternative $alt */
				$menu_id = $controller->duplicate_menu_and_create_alternative(
					$alt->value, $exp_id );
				if ( $menu_id ) {
					$alt->value = $menu_id;
					array_push( $alternatives, $alt );
				}
			}
			$controller->commit();
			$json->alternatives = $alternatives;

			if ( count( $alternatives ) <= 1 ) {
				$json->status = NelioABExperiment::STATUS_DRAFT;
			}//end if

			return $json;

		}//end post_duplicate()

		// @Implements
		public static function load( $post ) {
			if ( is_int( $post ) ) {
				$post = get_post( $post );
			}

			$exp = new NelioABMenuAlternativeExperiment( $post->ID );
			$json_data = $exp->post_content2json( $post->post_content );

			$exp->set_key_id( $json_data->key->id );
			$exp->set_type_using_text( $json_data->kind );
			$exp->set_name( $json_data->name );
			if ( isset( $json_data->description ) )
				$exp->set_description( $json_data->description );
			$exp->set_status( $json_data->status );
			$exp->set_creation_date( $json_data->creation );
			$exp->set_finalization_mode( $json_data->finalizationMode );
			if ( isset( $json_data->finalizationModeValue ) )
				$exp->set_finalization_value( $json_data->finalizationModeValue );
			if ( isset( $json_data->start ) )
				$exp->set_start_date( $json_data->start );
			if ( isset( $json_data->finalization ) )
				$exp->set_end_date( $json_data->finalization );

			if ( isset( $json_data->goals ) ) {
				NelioABExperiment::load_goals_from_json( $exp, $json_data->goals );
			}//end if

			$alternatives = array();
			if ( isset( $json_data->alternatives ) ) {
				foreach ( $json_data->alternatives as $json_alt ) {
					$alt = new NelioABAlternative( $json_alt->key->id );
					$alt->set_name( $json_alt->name );
					if ( isset( $json_alt->value ) )
						$alt->set_value( $json_alt->value );
					else
						$alt->set_value( '' );
					array_push( $alternatives, $alt );
				}
			}

			$exp->set_alternatives( $alternatives );

			return $exp;
		}

	}//NelioABMenuAlternativeExperiment

}

