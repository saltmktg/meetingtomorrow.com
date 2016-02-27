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


if ( !class_exists( 'NelioABErrorPage' ) ) {

	require_once( NELIOAB_UTILS_DIR . '/admin-ajax-page.php' );

	class NelioABErrorPage extends NelioABAdminAjaxPage {

		private $msg;

		public function __construct( $msg ) {
			parent::__construct( __( 'Something happened...', 'nelioab' ) );
			$this->msg = $msg;
		}

		protected function do_render() {
			echo "<div class='nelio-message'>";
			printf( '<img class="animated flipInY" src="%s" alt="%s" />',
				nelioab_admin_asset_link( '/images/error-icon.png' ),
				__( 'Error Notice', 'nelioab' )
			);
			echo "<h2>$this->msg</h2>";
			echo '</div>';
		}

	}//NelioABErrorPage

}
