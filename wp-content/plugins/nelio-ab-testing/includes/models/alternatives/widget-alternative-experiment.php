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


if ( !class_exists( 'NelioABWidgetAlternativeExperiment' ) ) {

	require_once( NELIOAB_MODELS_DIR . '/alternatives/global-alternative-experiment.php' );

	/**
	 * PHPDOC
	 *
	 * @package \NelioABTesting\Models\Experiments\AB
	 * @since PHPDOC
	 */
	class NelioABWidgetAlternativeExperiment extends NelioABGlobalAlternativeExperiment {

		/**
		 * PHPDOC
		 *
		 * @since PHPDOC
		 * @var array
		 */
		private $new_ids;


		/**
		 * PHPDOC
		 *
		 * @since PHPDOC
		 * @var array
		 */
		private $original_widget_set;


		// @Override
		public function clear() {
			parent::clear();
			$this->set_type( NelioABExperiment::WIDGET_ALT_EXP );
			$this->original_widget_set = $this->create_widget_set_alternative( 'FakeOriginalWidget' );
			$this->new_ids = array();
		}


		// @Override
		public function get_original() {
			return $this->original_widget_set;
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
				$this->original_widget_set = $alts[0];
				for ( $i = 1; $i < count( $alts ); $i++ )
					array_push( $aux, $alts[$i] );
			}
			parent::set_alternatives( $aux );
		}


		/**
		 * PHPDOC
		 *
		 * @param string $name PHPDOC
		 *
		 * @return NelioABAlternative PHPDOC
		 *
		 * @since PHPDOC
		 */
		public function create_widget_set_alternative( $name ) {
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

			// Nothing to be done here.

		}//end do_save()


		// @Override
		public function start() {

			parent::start();

			// Finally, we need to fix the alternatives.
			$post = get_post( $this->get_id() );
			$json = $this->post_content2json( $post->post_content );
			$new_ids = array();
			$id = -9000;
			foreach ( $json->alternatives as $alt ) {
				$new_ids[ $id ] = $alt->key->id;
				--$id;
			}

			NelioABWidgetExpAdminController::update_alternatives_ids(
				$this->get_id(), $new_ids
			);

			// This fake widget is inserted to make the system believe that there is
			// at least one alternative with a widget that can be "applied" (and, thus,
			// the Apply and Clean buttons in the progress of the experiment make
			// sense).
			$aux = NelioABWidgetExpAdminController::get_widgets_in_experiments();
			NelioABWidgetExpAdminController::link_widget_to_experiment(
				'nelioab-fake-' . $this->get_id(),
				$this->get_id(), 'no-alternative',
				$aux );
			NelioABWidgetExpAdminController::set_widgets_in_experiments( $aux );

		}//end start()


		// @Override
		public function do_remove() {

			// 1. Remove all the alternative widgets.
			require_once( NELIOAB_EXP_CONTROLLERS_DIR . '/widget-experiment-controller.php' );
			NelioABWidgetExpAdminController::clean_widgets_in_experiment( $this->get_id() );

			// 2. We remove the experiment itself.
			parent::do_remove();

		}//end do_remove()


		// @Override
		public function post_duplicate( $json, $exp_id ) {

			$alts_src  = $this->get_alternatives();
			$alt_dest_id = -9001;
			for ( $i = 0; $i < count( $alts_src ); ++$i ) {
				/** @var NelioABAlternative $alt_src */
				$alt_src  = $alts_src[$i];
				NelioABWidgetExpAdminController::duplicate_widgets(
					$this->get_id(), $alt_src->get_id(),
					$exp_id, $alt_dest_id
				);
				--$alt_dest_id;
			}//end for

			return $json;

		}

		// @Override
		public function update_alternatives_ids( $exp_id, $old_alt_ids, $new_alt_ids ) {

			// Save the mapping for future reference.
			$this->new_ids = array();
			$count = count( $old_alt_ids );
			for ( $i = 0; $i < $count; ++$i ) {
				$this->new_ids[ $old_alt_ids[ $i ] ] = $new_alt_ids[ $i ];
			}//end for

			NelioABWidgetExpAdminController::update_alternatives_ids(
				$this->get_id(), $this->new_ids
			);

		}//end update_alternatives_ids()


		/**
		 * TODO
		 *
		 * @since PHPDOC
		 */
		public function get_real_id_for_alt( $id ) {

			if ( isset( $this->new_ids[ $id ] ) ) {
				return $this->new_ids[ $id ];
			} else {
				return $id;
			}//end if

		}//end get_real_id_for_alt()


		// @Implements
		public static function load( $post ) {
			if ( is_int( $post ) ) {
				$post = get_post( $post );
			}

			$exp = new NelioABWidgetAlternativeExperiment( $post->ID );
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
					if ( isset( $json_alt->content ) )
						$alt->set_value( $json_alt->value );
					else
						$alt->set_value( '' );
					array_push ( $alternatives, $alt );
				}
			}
			$exp->set_alternatives( $alternatives );

			return $exp;
		}


	}//NelioABWidgetAlternativeExperiment

}

