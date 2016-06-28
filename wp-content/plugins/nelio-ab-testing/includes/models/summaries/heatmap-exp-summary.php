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


if ( !class_exists( 'NelioABHeatmapExpSummary' ) ) {

	require_once( NELIOAB_MODELS_DIR . '/summaries/experiment-summary.php' );

	/**
	 * This class summarizes a Heatmap experiment.
	 *
	 * This class can be used in Nelio's _Dashboard_ or in the
	 * _Experiment List_. It contains the basic, essential information that is
	 * shown in those pages.
	 *
	 * @package \NelioABTesting\Models\Experiments\Summaries
	 * @since 3.0.0
	 */
	class NelioABHeatmapExpSummary extends NelioABExperimentSummary {

		/**
		 * The number of visitors that contributed to build this experiment's heatmap.
		 *
		 * These visitors are classified in four groups:
		 * * phone
		 * * tablet
		 * * desktop
		 * * hd
		 *
		 * @since 3.0.0
		 * @var array
		 */
		private $heatmap_info;


		// @Override
		public function __construct( $id ) {
			parent::__construct( $id );
			$this->set_type( NelioABExperiment::HEATMAP_EXP );
			$this->heatmap_info = array(
					'phone'   => 0,
					'tablet'  => 0,
					'desktop' => 0,
					'hd'      => 0,
				);
		}


		// @Implements
		public function has_result_status() {
			return false;
		}


		/**
		 * This function returns the heatmap information of this experiment.
		 *
		 * @return array the heatmap information of this experiment.
		 *
		 * @see self::heatmap_info
		 *
		 * @since 3.0.0
		 */
		public function get_heatmap_info() {
			return $this->heatmap_info;
		}


		// @Implements
		public function build_summary( $exp ) {
			$this->set_name( $exp->get_name() );
			$this->set_creation_date( $exp->get_creation_date() );

			$this->heatmap_info = get_post_meta( $exp->get_id(), 'nelioab_hm_summary', true );
			if ( ! is_array( $this->heatmap_info ) ) {
				$this->heatmap_info = array();
			}//end if

			foreach ( array( 'phone', 'tablet', 'desktop', 'hd' ) as $res ) {
				if ( ! isset( $this->heatmap_info[ $res ] ) ) {
					$this->heatmap_info[ $res ] = 0;
				}//end if
			}//end foreach

		}//end build_summary()

	}//NelioABHeatmapExpSummary

}

