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


if ( !class_exists( 'NelioABPhoneCallAction' ) ) {

	/**
	 * Class representing a "form submission" conversion action.
	 *
	 * @package \NelioABTesting\Models\Goals\Actions
	 * @since PHPDOC
	 */
	class NelioABPhoneCallAction extends NelioABAction {

		/**
		 * Creates a new instance of this class.
		 *
		 * @return NelioABPhoneCallAction a new instance of this class.
		 *
		 * @since PHPDOC
		 */
		public function __construct() {
			parent::__construct( self::PHONE_CALL );
		}


		// @Implements
		public function encode_for_appengine() {
			return array();
		}


		/**
		 * Returns a new action object built using the information described in $action.
		 *
		 * @param object $json a JSON action returned by AppEngine.
		 *
		 * @return NelioABPhoneCallAction the new action containing all the information in `$action`.
		 *
		 * @since PHPDOC
		 * @Override
		 */
		public static function decode_from_appengine( $json ) {
			$action = new NelioABPhoneCallAction();
			return $action;
		}


		// @Implements
		public function json4js() {
			return array(
					'type' => NelioABAction::PHONE_CALL
				);
		}


		/**
		 * Returns a new action object built using the information described in $action.
		 *
		 * @param object $json a JSON action as used in the admin pages of our plugin.
		 *
		 * @return NelioABPhoneCallAction the new action containing all the information in `$action`.
		 *
		 * @since PHPDOC
		 * @Override
		 */
		public static function build_action_using_json4js( $json ) {
			$action = new NelioABPhoneCallAction();
			return $action;
		}

	}//NelioABPhoneCallAction

}

