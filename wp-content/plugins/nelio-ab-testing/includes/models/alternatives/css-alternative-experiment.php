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


if ( !class_exists( 'NelioABCssAlternativeExperiment' ) ) {

	require_once( NELIOAB_MODELS_DIR . '/alternatives/global-alternative-experiment.php' );

	/**
	 * PHPDOC
	 *
	 * @package \NelioABTesting\Models\Experiments\AB
	 * @since PHPDOC
	 */
	class NelioABCssAlternativeExperiment extends NelioABGlobalAlternativeExperiment {

		/**
		 * PHPDOC
		 *
		 * @since PHPDOC
		 * @var array
		 */
		private $original_css;


		// @Override
		public function clear() {
			parent::clear();
			$this->set_type( NelioABExperiment::CSS_ALT_EXP );
			$this->original_css = $this->create_css_alternative( 'FakeOriginalCss' );
		}


		// @Override
		public function get_original() {
			return $this->original_css;
		}


		// @Override
		public function get_originals_id() {
			/** @var NelioABAlternative $aux */
			$aux = $this->get_original();
			return $aux->get_id();
		}


		// @Override
		public function set_alternatives( $alts ) {
			$aux = array();
			if ( count( $alts ) > 0 ) {
				$this->original_css = $alts[0];
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
		public function create_css_alternative( $name ) {
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


		// @Implements
		public function do_save() {

			foreach ( $this->get_alternatives() as $alt ) {
				if ( $alt->get_value() == -1 ) {
					$alt->set_value( '' );
				}//end if
			}//end foreach

		}//end do_save()


		// @Override
		protected function determine_proper_status() {
			if ( count( $this->get_alternatives() ) <= 0 )
				return NelioABExperiment::STATUS_DRAFT;
			return parent::determine_proper_status();
		}


		/**
		 * PHPDOC
		 *
		 * @param int    $alt_id  PHPDOC
		 * @param string $name    PHPDOC
		 * @param string $content PHPDOC
		 *
		 * @return void
		 *
		 * @since PHPDOC
		 */
		public function update_css_alternative( $alt_id, $name, $content ) {
			foreach ( $this->get_alternatives() as $alt ) {
				if ( $alt->get_id() == $alt_id ) {
					$alt->set_name( $name );
					$alt->set_value( $content );
					break;
				}//end if
			}//end foreach
			$this->save();
		}


		// @Implements
		public static function load( $post ) {
			if ( is_int( $post ) ) {
				$post = get_post( $post );
			}

			$exp = new NelioABCssAlternativeExperiment( $post->ID );
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
					$alt->set_value( $json_alt->content );
					array_push ( $alternatives, $alt );
				}
			}
			$exp->set_alternatives( $alternatives );

			return $exp;
		}//end load()

	}//NelioABCssAlternativeExperiment

}

