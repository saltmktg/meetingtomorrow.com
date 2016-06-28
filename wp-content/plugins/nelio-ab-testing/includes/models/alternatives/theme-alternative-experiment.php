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


if ( !class_exists( 'NelioABThemeAlternativeExperiment' ) ) {

	require_once( NELIOAB_MODELS_DIR . '/alternatives/global-alternative-experiment.php' );

	/**
	 * PHPDOC
	 *
	 * @package \NelioABTesting\Models\Experiments\AB
	 * @since PHPDOC
	 */
	class NelioABThemeAlternativeExperiment extends NelioABGlobalAlternativeExperiment {

		/**
		 * PHPDOC
		 *
		 * @since PHPDOC
		 * @var array
		 */
		private $original_theme;


		/**
		 * PHPDOC
		 *
		 * @since PHPDOC
		 * @var array
		 */
		private $selected_themes;


		// @Override
		public function __construct( $id ) {
			parent::__construct( $id );
			$this->set_type( NelioABExperiment::THEME_ALT_EXP );
			$this->original_theme = false;
			$this->selected_themes = array();
		}


		// @Override
		public function get_original() {
			return $this->original_theme;
		}


		// @Override
		public function get_originals_id() {
			/** @var NelioABAlternative $aux */
			$aux = $this->get_original();
			return $aux->get_id();
		}


		// @Override
		protected function determine_proper_status() {
			if ( count( $this->selected_themes ) <= 0 )
				return NelioABExperiment::STATUS_DRAFT;
			return parent::determine_proper_status();
		}


		// @Override
		public function set_alternatives( $alts ) {
			$aux = array();
			if ( count( $alts ) > 0 ) {
				$this->original_theme = $alts[0];
				for ( $i = 1; $i < count( $alts ); $i++ )
					array_push( $aux, $alts[$i] );
			}
			parent::set_alternatives( $aux );
		}


		/**
		 * PHPDOC
		 *
		 * @param int    $id   PHPDOC
		 * @param string $name PHPDOC
		 *
		 * @return void
		 *
		 * @since PHPDOC
		 */
		public function add_selected_theme( $id, $name ) {
			foreach ( $this->selected_themes as $theme )
				if ( $theme->value === $id )
					return;
			if ( strlen( $id ) === 0 )
				return;
			array_push( $this->selected_themes,
				json_decode( json_encode(
					array( 'name' => $name, 'value' => $id, 'isSelected' => true )
				) ) );
		}


		/**
		 * PHPDOC
		 *
		 * @return array PHPDOC
		 *
		 * @since PHPDOC
		 */
		public function get_selected_themes() {
			return $this->selected_themes;
		}


		/**
		 * PHPDOC
		 *
		 * @param int $theme_id
		 *
		 * @return boolean PHPDOC
		 *
		 * @since PHPDOC
		 */
		public function is_theme_selected( $theme_id ) {
			foreach( $this->selected_themes as $selected_theme )
				if ( $selected_theme->value == $theme_id )
					return true;
			return false;
		}


		// @Implements
		public function encode_for_appengine() {

			$current_theme = wp_get_theme();
			$alt = new NelioABAlternative();
			$alt->set_name( $current_theme['Name'] );
			$alt->set_value( $current_theme['Stylesheet'] );
			$this->original_theme = $alt;

			$this->alternatives = array();
			foreach ( $this->selected_themes as $theme ) {

				$alt = new NelioABAlternative();
				$alt->set_name( $theme->name );
				$alt->set_value( $theme->value );
				if ( $theme->value !== $current_theme['Stylesheet'] ) {
					array_push( $this->alternatives, $alt );
				}//end if

			}//end if

			return parent::encode_for_appengine();

		}//end encode_for_appengine()


		// @Implements
		public static function load( $post ) {
			if ( is_int( $post ) ) {
				$post = get_post( $post );
			}

			$exp = new NelioABThemeAlternativeExperiment( $post->ID );
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

			if ( isset( $json_data->goals ) )
				NelioABExperiment::load_goals_from_json( $exp, $json_data->goals );

			$alternatives = array();
			if ( isset( $json_data->alternatives ) ) {
				foreach ( $json_data->alternatives as $json_alt ) {
					$alt = new NelioABAlternative( $json_alt->key->id );
					$alt->set_name( $json_alt->name );
					$alt->set_value( $json_alt->value );
					array_push ( $alternatives, $alt );
				}
			}
			$exp->set_alternatives( $alternatives );

			return $exp;
		}

	}//NelioABThemeAlternativeExperiment

}

