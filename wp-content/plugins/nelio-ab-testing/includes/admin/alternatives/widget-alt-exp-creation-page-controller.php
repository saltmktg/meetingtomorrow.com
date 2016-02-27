<?php
/**
 * Copyright 2013 Nelio Software S.L.
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

if ( !class_exists( 'NelioABWidgetAltExpCreationPageController' ) ) {

	require_once( NELIOAB_ADMIN_DIR . '/alternatives/widget-alt-exp-edition-page-controller.php' );
	require_once( NELIOAB_ADMIN_DIR . '/views/alternatives/widget-alt-exp-creation-page.php' );

	class NelioABWidgetAltExpCreationPageController extends
			NelioABWidgetAltExpEditionPageController {

		public static function get_instance() {
			return new NelioABWidgetAltExpCreationPageController();
		}

		public static function build() {
			// Check settings
			require_once( NELIOAB_ADMIN_DIR . '/error-controller.php' );
			$error = NelioABErrorController::build_error_page_on_invalid_settings();
			if ( $error ) return;

			$aux  = NelioABWidgetAltExpCreationPageController::get_instance();
			$view = $aux->do_build();
			$view->render();
		}

		public static function generate_html_content() {
			$aux  = NelioABWidgetAltExpCreationPageController::get_instance();
			$view = $aux->do_build();
			$view->render_content();
			die();
		}

		public function create_view() {
			$title = __( 'Add Widget Experiment', 'nelioab' );
			return new NelioABWidgetAltExpCreationPage( $title );
		}

	}//NelioABWidgetAltExpCreationPageController

}

if ( isset( $_POST['nelioab_new_ab_widget_exp_form'] ) ) {
	$controller = NelioABWidgetAltExpCreationPageController::get_instance();
	$controller->manage_actions();
	if ( !$controller->validate() )
		$controller->print_ajax_errors();
}

