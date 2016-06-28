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


if ( !class_exists( 'NelioABExperiment' ) ) {

	/**
	 * Abstract class representing an Experiment in Nelio A/B Testing.
	 *
	 * In order to create an instance of this class, one must use of its
	 * concrete subclasses.
	 *
	 * @package \NelioABTesting\Models\Experiments
	 * @since 1.0.10
	 */
	abstract class NelioABExperiment {

		/**
		 * Unknown experiment type.
		 *
		 * @since 1.2.0
		 * @var int
		 */
		const UNKNOWN_TYPE = -1;


		/**
		 * It specifies that no type has been set to the current experiment.
		 *
		 * @since 1.2.0
		 * @var int
		 */
		const NO_TYPE_SET = 0;


		/**
		 * It specifies a Post alternative experiment.
		 *
		 * @since 1.2.0
		 * @var int
		 */
		const POST_ALT_EXP = 1;


		/**
		 * It specifies a Page alternative experiment.
		 *
		 * @since 1.2.0
		 * @var int
		 */
		const PAGE_ALT_EXP = 2;


		/**
		 * It specifies a CSS global alternative experiment.
		 *
		 * @since 1.2.0
		 * @var int
		 */
		const CSS_ALT_EXP = 3;


		/**
		 * It specifies a Theme global alternative experiment.
		 *
		 * @since 1.2.0
		 * @var int
		 */
		const THEME_ALT_EXP = 4;


		/**
		 * It specifies that the current experiment might be a page or post experiment.
		 *
		 * @since 1.2.0
		 * @var int It specifies that this experiment can either be a page or a
		 *          post experiment. It should be deprecated soon.
		 */
		const PAGE_OR_POST_ALT_EXP = 5;


		/**
		 * It specifies a Headline alternative experiment.
		 *
		 * @since 3.3.0
		 * @var int
		 */
		const HEADLINE_ALT_EXP = 6;


		/**
		 * It specifies a Heatmap experiment.
		 *
		 * @since 2.0.10
		 * @var int
		 */
		const HEATMAP_EXP = 7;


		/**
		 * It specifies a Widget global alternative experiment.
		 *
		 * @since 3.0.4
		 * @var int
		 */
		const WIDGET_ALT_EXP = 8;


		/**
		 * It specifies a Menu global alternative experiment.
		 *
		 * @since 3.0.4
		 * @var int
		 */
		const MENU_ALT_EXP = 9;


		/**
		 * It specifies a Custom Post Type alternative experiment.
		 *
		 * @since 4.1.0
		 * @var int
		 */
		const CPT_ALT_EXP = 10;


		/**
		 * It specifies a WooCommerce Product Summary alternative experiment.
		 *
		 * @since 4.2.0
		 * @var int
		 */
		const WC_PRODUCT_SUMMARY_ALT_EXP = 201;


		/**
		 * It specifies a Title alternative experiment, as required by Nelio's Cloud.
		 *
		 * @since 1.4.0
		 * @var string
		 */
		const UNKNOWN_TYPE_STR = 'UnknownExperiment';


		/**
		 * It specifies a Title alternative experiment, as required by Nelio's Cloud.
		 *
		 * @since 1.4.0
		 * @var string
		 */
		const POST_ALT_EXP_STR = 'PostAlternativeExperiment';


		/**
		 * It specifies a Title alternative experiment, as required by Nelio's Cloud.
		 *
		 * @since 1.4.0
		 * @var string
		 */
		const PAGE_ALT_EXP_STR = 'PageAlternativeExperiment';


		/**
		 * It specifies a Title alternative experiment, as required by Nelio's Cloud.
		 *
		 * @since 1.4.0
		 * @var string
		 */
		const CSS_ALT_EXP_STR = 'CssGlobalAlternativeExperiment';


		/**
		 * It specifies a Title alternative experiment, as required by Nelio's Cloud.
		 *
		 * @since 1.4.0
		 * @var string
		 */
		const THEME_ALT_EXP_STR = 'ThemeGlobalAlternativeExperiment';


		/**
		 * It specifies a Title alternative experiment, as required by Nelio's Cloud.
		 *
		 * @since 3.3.0
		 * @var string
		 */
		const WIDGET_ALT_EXP_STR = 'WidgetGlobalAlternativeExperiment';


		/**
		 * It specifies a Title alternative experiment, as required by Nelio's Cloud.
		 *
		 * @since 4.1.0
		 * @var string
		 */
		const CPT_ALT_EXP_STR = 'CptAlternativeExperiment';


		/**
		 * It specifies a Title alternative experiment, as required by Nelio's Cloud.
		 *
		 * @since 3.3.0
		 * @var string
		 */
		const MENU_ALT_EXP_STR = 'MenuGlobalAlternativeExperiment';


		/**
		 * It specifies a Title alternative experiment, as required by Nelio's Cloud.
		 *
		 * @since 3.3.0
		 * @var string
		 */
		const HEADLINE_ALT_EXP_STR = 'HeadlineAlternativeExperiment';


		/**
		 * It specifies a Title alternative experiment, as required by Nelio's Cloud.
		 *
		 * @since 2.0.10
		 * @var string
		 */
		const HEATMAP_EXP_STR = 'HeatmapExperiment';


		/**
		 * It specifies a WooCommerce Product Summary alternative experiment, as required by Nelio's Cloud.
		 *
		 * @since 4.2.0
		 * @var string
		 */
		const WC_PRODUCT_SUMMARY_ALT_EXP_STR = 'WC_ProductSummaryAlternativeExperiment';


		/**
		 * Experiment has to be manually stopped.
		 *
		 * @since 3.2.0
		 * @var int
		 */
		const FINALIZATION_MANUAL = 0;


		/**
		 * Experiment has to be automatically stopped after a certain amount of
		 * page views.
		 *
		 * @since 3.2.0
		 * @var int
		 */
		const FINALIZATION_AFTER_VIEWS = 1;


		/**
		 * Experiment has to be automatically stopped after a certain confidence
		 * value has been reached.
		 *
		 * @since 3.2.0
		 * @var int
		 */
		const FINALIZATION_AFTER_CONFIDENCE = 2;


		/**
		 * Experiment has to be automatically stopped at a certain date.
		 *
		 * @since 3.2.0
		 * @var int
		 */
		const FINALIZATION_AFTER_DATE = 3;


		/**
		 * The experiment is a draft.
		 *
		 * A Draft experiment has some relevant information missing. If a
		 * experiment is a draft, it cannot be started.
		 *
		 * @since 4.1.0
		 * @var int
		 */
		const STATUS_DRAFT = 1;


		/**
		 * The experiment is paused.
		 *
		 * A Paused experiments is an experiment that was running at some point
		 * before, but it's not running right now.
		 *
		 * @since 4.1.0
		 * @var int
		 */
		const STATUS_PAUSED = 2;


		/**
		 * The experiment is ready.
		 *
		 * An experiment is ready if all its relevant information is defined.
		 * Once an experiment is running, it can be started.
		 *
		 * @since 4.1.0
		 * @var int
		 */
		const STATUS_READY = 3;


		/**
		 * The experiment is running.
		 *
		 * @since 4.1.0
		 * @var int
		 */
		const STATUS_RUNNING = 4;


		/**
		 * The experiment is finished.
		 *
		 * @since 4.1.0
		 * @var int
		 */
		const STATUS_FINISHED = 5;


		/**
		 * The experiment has been removed, but can be restored.
		 *
		 * @since 4.1.0
		 * @var int
		 */
		const STATUS_TRASH = 6;


		/**
		 * The experiment is scheduled and it'll be started automatically.
		 *
		 * @since 4.1.0
		 * @var int
		 */
		const STATUS_SCHEDULED = 7;


		/**
		 * The identifier of the current experiment, as defined in AppEngine. If
		 * the experiment has not been synced to AppEngine yet, the ID will be -1.
		 *
		 * @since 1.0.10
		 * @var string
		 */
		protected $key_id;


		/**
		 * The identifier of the local nelioab_local_exp that keeps info about
		 * this post.
		 *
		 * @since 4.3.2
		 * @var int
		 */
		protected $id;


		/**
		 * The list of Goals defined for the current experiment.
		 *
		 * @see NelioABGoal
		 *
		 * @since 1.4.0
		 * @var array
		 */
		protected $goals;


		/**
		 * A descriptive name for the experiment.
		 *
		 * @since 1.0.10
		 * @var string
		 */
		private $name;


		/**
		 * An optional description of what the experiment is testing.
		 *
		 * @since 1.0.10
		 * @var string
		 */
		private $descr;


		/**
		 * The status of the experiment.
		 *
		 * @since 1.0.10
		 * @var int
		 */
		private $status;


		/**
		 * The date in which the experiment was created.
		 *
		 * @since 1.0.10
		 * @var string The pattern is: `1985-12-01T12:00:00.000Z`
		 */
		private $creation_date;


		/**
		 * The date in which the experiment was started.
		 *
		 * @since 1.0.10
		 * @var string The pattern is: `1985-12-01T12:00:00.000Z`
		 */
		private $start_date;


		/**
		 * The date in which the experiment was stopped.
		 *
		 * @since 1.0.10
		 * @var string The pattern is: `1985-12-01T12:00:00.000Z`
		 */
		private $end_date;


		/**
		 * The type of the experiment.
		 *
		 * @since 1.2.0
		 * @var int This property specifies the type of the experiment. Each
		 *          experiment type can only be used with certain subclasses.
		 */
		private $type;


		/**
		 * It specifies how the experiment should be stopped.
		 *
		 * @since 3.2.0
		 * @var int This can either be manually or automatically (and, if it's the
		 *          latter, it might be after a certain amount of page views has
		 *          been reached, when the confidence reaches a certain value, and
		 *          so on).
		 */
		private $finalization_mode;


		/**
		 * The specific value for automatically stopping the experiment.
		 *
		 * @since 3.2.0
		 * @var string Depending on the finalization mode, we need to know the
		 *             specific value that will automatically stop an experiment.
		 *             Thus, for instance, if we use <em>Confidence</em>, we may
		 *             define that an experiment has to stop when the confidence
		 *             reaches 99%.
		 */
		private $finalization_value;


		/**
		 * It specifies whether the current experiment is fully loaded.
		 *
		 * @see NelioABExperimentsManager::get_experiment_by_id
		 * @see NelioABExperimentsManager::get_relevant_running_experiments
		 *
		 * @since 3.4.0
		 * @var boolean This attribute specifies whether the experiment is fully
		 *              loaded or, on the other hand, if only some of its
		 *              attributes were retrieved from AppEngine.
		 */
		private $is_fully_loaded;


		/**
		 * Creates a new instance of this class.
		 *
		 * This constructor might be used by the concrete subclasses. It sets all
		 * attributes to their default values.
		 *
		 * @return NelioABExperiment a new instance of this class.
		 *
		 * @see self::clear
		 *
		 * @since 1.0.10
		 */
		public function __construct() {
			$this->clear();
		}


		/**
		 * Resets all experiment attributes to their default values.
		 *
		 * @return void
		 *
		 * @since 1.0.10
		 */
		public function clear() {
			$this->key_id = 0;
			$this->id = -time();
			$this->name = '';
			$this->descr = '';
			$this->status = NelioABExperiment::STATUS_DRAFT;
			$this->type = NelioABExperiment::NO_TYPE_SET;
			$this->goals = array();
			$this->is_fully_loaded = false;
			$this->finalization_mode  = self::FINALIZATION_MANUAL;
			$this->finalization_value = '';
		}


		/**
		 * Returns whether this experiment is global or not.
		 *
		 * Global experiments include CSS, menu, theme, and widget experiments.
		 *
		 * @return boolean Whether this experiment is global or not.
		 *
		 * @since 3.4.0
		 */
		public function is_global() {
			return false;
		}


		/**
		 * Returns the type of this experiment.
		 *
		 * @return int The type of this experiment.
		 *
		 * @since 1.2.0
		 */
		public function get_type() {
			return $this->type;
		}


		/**
		 * Sets the type of this experiment to `$type`.
		 *
		 * @param int $type The new type of this experiment.
		 *
		 * @return void
		 *
		 * @since 1.2.0
		 */
		public function set_type( $type ) {
			$this->type = $type;
		}


		/**
		 * It specifies whether the current experiment is fully loaded or, on the
		 * other hand, if only some of its attributes where retrieved from
		 * AppEngine.
		 *
		 * @return boolean Whether this experiment is fully loaded or not.
		 *
		 * @see NelioABExperimentsManager::get_experiment_by_id
		 * @see NelioABExperimentsManager::get_relevant_running_experiments
		 *
		 * @since 3.4.0
		 */
		public function is_fully_loaded() {
			return $this->is_fully_loaded;
		}


		/**
		 * Returns whether the current experiment is fully loaded.
		 *
		 * This function returns whether the experiment is fully loaded or, on the
		 * other hand, if only some of its attributes were retrieved from
		 * AppEngine.
		 *
		 * @param boolean $is_fully_loaded If set to true, this experiment will be
		 *                                 marked as fully loaded. Otherwise, it
		 *                                 will be considered "partially loaded".
		 *
		 * @return void
		 *
		 * @since 3.4.0
		 */
		public function mark_as_fully_loaded( $is_fully_loaded = true ) {
			$this->is_fully_loaded = $is_fully_loaded;
		}


		/**
		 * Returns the list of goals.
		 *
		 * @return array the list of goals.
		 *
		 * @since 1.4.0
		 */
		public function get_goals() {
			if ( is_array( $this->goals ) )
				return $this->goals;
			else
				return array();
		}


		/**
		 * It adds the given goal to the list of goals.
		 *
		 * This function adds the given goal to this experiment's goal list. If the
		 * given goal is the main goal, it's added in the first position.
		 *
		 * @param NelioABGoal $goal The goal to add.
		 *
		 * @return void
		 *
		 * @since 1.4.0
		 */
		public function add_goal( $goal ) {
			if ( $goal->is_main_goal() )
				array_unshift( $this->goals, $goal );
			else
				array_push( $this->goals, $goal );

			for ( $i = 0; $i < count( $this->goals ); ++$i ) {
				$g = $this->goals[$i];
				$g->set_order( $i + 1 );
			}//end for

		}


		/**
		 * Translates experiment textual kind to experiment int type.
		 *
		 * This function translates an experiment textual kind (as used/defined in
		 * AppEngine) to the corresponding experiment type (as defined in this
		 * class; i.e. integer constants).
		 *
		 * @param string $kind the experiment textual kind, as defined in AppEngine.
		 *
		 * @return int the experiment integer type that corresponds to the given
		 *             `$kind`. If none was found, it returns
		 *             `NelioABExperiment::UNKNOWN_TYPE`.
		 *
		 * @since 3.4.0
		 */
		public static function kind_to_type( $kind ) {
			switch( $kind ) {
				case NelioABExperiment::POST_ALT_EXP_STR:
					return NelioABExperiment::POST_ALT_EXP;
				case NelioABExperiment::PAGE_ALT_EXP_STR:
					return NelioABExperiment::PAGE_ALT_EXP;
				case NelioABExperiment::CPT_ALT_EXP_STR:
					return NelioABExperiment::CPT_ALT_EXP;
				case NelioABExperiment::HEADLINE_ALT_EXP_STR:
					return NelioABExperiment::HEADLINE_ALT_EXP;
				case NelioABExperiment::CSS_ALT_EXP_STR:
					return NelioABExperiment::CSS_ALT_EXP;
				case NelioABExperiment::THEME_ALT_EXP_STR:
					return NelioABExperiment::THEME_ALT_EXP;
				case NelioABExperiment::WIDGET_ALT_EXP_STR:
					return NelioABExperiment::WIDGET_ALT_EXP;
				case NelioABExperiment::MENU_ALT_EXP_STR:
					return NelioABExperiment::MENU_ALT_EXP;
				case NelioABExperiment::HEATMAP_EXP_STR:
					return NelioABExperiment::HEATMAP_EXP;
				case NelioABExperiment::WC_PRODUCT_SUMMARY_ALT_EXP_STR:
					return NelioABExperiment::WC_PRODUCT_SUMMARY_ALT_EXP;
				default:
					// This should never happen...
					return NelioABExperiment::UNKNOWN_TYPE;
			}
		}


		/**
		 * Translates an experiment type into its corresponding textual kind.
		 *
		 * This function translates an experiment type (as used in the plugin) into
		 * the corresponding experiment kind (as used/defined in AppEngine).
		 *
		 * @param int $type the experiment integer type, as used in the plugin.
		 *
		 * @return string the experiment string kind that corresponds to the given
		 *                `$type`. If none was found, it returns
		 *                `NelioABExperiment::UNKNOWN_TYPE_STR`.
		 *
		 * @since 3.4.0
		 */
		public static function type_to_kind( $type ) {
			switch( $type ) {
				case NelioABExperiment::POST_ALT_EXP:
					return NelioABExperiment::POST_ALT_EXP_STR;
				case NelioABExperiment::PAGE_ALT_EXP:
					return NelioABExperiment::PAGE_ALT_EXP_STR;
				case NelioABExperiment::CPT_ALT_EXP:
					return NelioABExperiment::CPT_ALT_EXP_STR;
				case NelioABExperiment::HEADLINE_ALT_EXP:
					return NelioABExperiment::HEADLINE_ALT_EXP_STR;
				case NelioABExperiment::CSS_ALT_EXP:
					return NelioABExperiment::CSS_ALT_EXP_STR;
				case NelioABExperiment::THEME_ALT_EXP:
					return NelioABExperiment::THEME_ALT_EXP_STR;
				case NelioABExperiment::WIDGET_ALT_EXP:
					return NelioABExperiment::WIDGET_ALT_EXP_STR;
				case NelioABExperiment::MENU_ALT_EXP:
					return NelioABExperiment::MENU_ALT_EXP_STR;
				case NelioABExperiment::HEATMAP_EXP:
					return NelioABExperiment::HEATMAP_EXP_STR;
				case NelioABExperiment::WC_PRODUCT_SUMMARY_ALT_EXP:
					return NelioABExperiment::WC_PRODUCT_SUMMARY_ALT_EXP_STR;
				default:
					// This should not happen...
					return NelioABExperiment::UNKNOWN_TYPE_STR;
			}
		}


		/**
		 * It sets the type of this experiment using the given `$kind`.
		 *
		 * @param string $kind an experiment kind, as defined/used in AppEngine.
		 *
		 * @return void
		 *
		 * @since 1.4.0
		 */
		public function set_type_using_text( $kind ) {
			$this->set_type( self::kind_to_type( $kind ) );
		}


		/**
		 * It returns the experiment kind as defined/used in AppEngine.
		 *
		 * @return string the experiment kind as defined/used in AppEngine.
		 *
		 * @since 1.4.0
		 */
		protected function get_textual_type() {
			return self::type_to_kind( $this->type );
		}


		/**
		 * It returns the ID of this experiment.
		 *
		 * @return int the ID of this experiment.
		 *
		 * @since 1.0.10
		 */
		public function get_id() {
			return intval( $this->id );
		}


		/**
		 * It returns the ID of this experiment.
		 *
		 * @return int the ID of this experiment.
		 *
		 * @since 4.3.2
		 */
		public function get_key_id() {
			return $this->key_id;
		}//end get_key_id()


		/**
		 * Sets the AE id to this experiment (if any).
		 *
		 * @param string $key_id AE id to this experiment (if any).
		 *
		 * @since 4.3.2
		 */
		public function set_key_id( $key_id ) {
			$this->key_id = $key_id;
		}//end set_key_id()


		/**
		 * It returns the name of the experiment.
		 *
		 * @return string the name of the experiment.
		 *
		 * @since 1.0.10
		 */
		public function get_name() {
			return $this->name;
		}


		/**
		 * It sets the name of the experiment.
		 *
		 * @param string $name the new name of the experiment.
		 *
		 * @return void
		 *
		 * @since 1.0.10
		 */
		public function set_name( $name ) {
			$this->name = $name;
		}


		/**
		 * It returns the description of the experiment.
		 *
		 * @return string the description of the experiment.
		 *
		 * @since 1.0.10
		 */
		public function get_description() {
			return $this->descr;
		}


		/**
		 * It sets the description of the experiment.
		 *
		 * @param string $descr the new description of the experiment.
		 *
		 * @return void
		 *
		 * @since 1.0.10
		 */
		public function set_description( $descr ) {
			$this->descr = $descr;
		}


		/**
		 * It returns the status in which the experiment is.
		 *
		 * @return int the status in which the experiment is.
		 *
		 * @since 1.0.10
		 */
		public function get_status() {
			return $this->status;
		}


		/**
		 * It sets the status of the experiment.
		 *
		 * @param int $status the new status of the experiment.
		 *
		 * @return void
		 *
		 * @since 1.0.10
		 */
		public function set_status( $status ) {
			$this->status = $status;
		}


		/**
		 * It returns the date in which the experiment was created.
		 *
		 * @return string the date in which the experiment was created.
		 *
		 * @since 1.0.10
		 */
		public function get_creation_date() {
			return $this->creation_date;
		}


		/**
		 * It sets the creation date of the experiment.
		 *
		 * @param int $creation_date the new creation date of the experiment.
		 *
		 * @return void
		 *
		 * @since 1.0.10
		 */
		public function set_creation_date( $creation_date ) {
			$this->creation_date = $creation_date;
		}


		/**
		 * It returns the date in which the experiment was started.
		 *
		 * @return string the date in which the experiment was started.
		 *
		 * @since 3.2.1
		 */
		public function get_start_date() {
			return $this->start_date;
		}


		/**
		 * It modifies the date in which the experiment was started.
		 *
		 * @param string $start_date the date in which the experiment was started.
		 *
		 * @return void
		 *
		 * @since 3.2.1
		 */
		public function set_start_date( $start_date ) {
			$this->start_date = $start_date;
		}


		/**
		 * It returns the date in which the experiment was stopped.
		 *
		 * @return string the date in which the experiment was stopped.
		 *
		 * @since 3.2.1
		 */
		public function get_end_date() {
			return $this->end_date;
		}


		/**
		 *
		 * It modifies the date in which the experiment was stopped.
		 *
		 * @param string $end_date the date in which the experiment was stopped.
		 *
		 * @return void
		 *
		 * @since 3.2.1
		 */
		public function set_end_date( $end_date ) {
			$this->end_date = $end_date;
		}


		/**
		 * It returns the number of days since the experiment was stopped.
		 *
		 * @return int the number of days since the experiment was stopped.
		 *
		 * @since 3.2.1
		 */
		public function get_days_since_finalization() {

			if ( isset( $this->end_date ) && ! empty( $this->end_date ) ) {
				return floor( ( time() - strtotime( $this->end_date ) ) / 86400 );
			} else {
				return 0;
			}//end if

		}//end get_days_since_finalization()


		/**
		 * It sets the finalization mode of the experiment.
		 *
		 * @param int $mode the new finalization mode of the experiment.
		 *
		 * @return void
		 *
		 * @since 3.2.0
		 */
		public function set_finalization_mode( $mode ) {
			$this->finalization_mode = $mode;
		}


		/**
		 * It returns the finalization mode.
		 *
		 * The finalization mode can either be manual or automatic. If automatic,
		 * then it might be after a certain amount of page views has been reached,
		 * when the confidence reaches a certain value, and so on.
		 *
		 * @return int the finalization mode.
		 *
		 * @since 3.2.0
		 */
		public function get_finalization_mode() {
			return $this->finalization_mode;
		}


		/**
		 * It sets the finalization mode of the experiment.
		 *
		 * @param string $value the new finalization mode of the experiment.
		 *
		 * @return void
		 *
		 * @since 3.2.0
		 */
		public function set_finalization_value( $value ) {
			$this->finalization_value = $value;
		}


		/**
		 * It returns the specific value for automatically stopping the experiment.
		 *
		 * @return string the specific value for automatically stopping the experiment.
		 *
		 * @since 3.2.0
		 */
		public function get_finalization_value() {
			return $this->finalization_value;
		}


		/**
		 * It returns the AppEngine uRL used by this experiment for making its goals persistent.
		 *
		 * AppEngine uses different URLs for different types of experiments and
		 * different kinds of goals. This function simplifies the process of
		 * obtaining the appropriate URL.
		 *
		 * @param NelioABGoal $goal the goal that has to be made persistent.
		 *
		 * @return string the specific value for automatically stopping the experiment.
		 *
		 * @since 1.4.0
		 */
		public function get_url_for_making_goal_persistent( $goal ) {
			$exp_url_fragment = $this->get_exp_kind_url_fragment();
			switch ( $goal->get_kind() ) {
				case NelioABGoal::ALTERNATIVE_EXPERIMENT_GOAL:
				default:
				$type = 'alternativeexp';
			}
			if ( $goal->get_id() < 0 ) {
				$url = sprintf(
					NELIOAB_BACKEND_URL . '/exp/%1$s/%2$s/goal/%3$s',
					$exp_url_fragment, $this->get_id(), $type
				);
			}
			else {
				if ( $goal->has_to_be_deleted() )
					$action = 'delete';
				else
					$action = 'update';
				$url = sprintf(
					NELIOAB_BACKEND_URL . '/goal/%2$s/%1$s/%3$s',
					$goal->get_id(), $type, $action
				);
			}
			return $url;
		}


		/**
		 * It schedules the start of the experiment for the given date.
		 *
		 * @param string $date the date in which the experiment has to be automatically started.
		 *                     This date is stored in AppEngine.
		 *
		 * @return void
		 *
		 * @since 3.2.0
		 */
		public function schedule( $date ) {
			$url = sprintf(
					NELIOAB_BACKEND_URL . '/exp/%2$s/%1$s/schedule',
					$this->get_id(), $this->get_exp_kind_url_fragment()
				);
			$object = array( 'date' => $date );
			NelioABBackend::remote_post( $url, $object );
		}


		/**
		 * It cancels the scheduling of an experiment.
		 *
		 * Once the scheduling has been cancelled, the experiment has to either
		 * be started manually or scheduled again. This operation is executed
		 * in AppEngine.
		 *
		 * @return void
		 *
		 * @since 3.2.0
		 */
		public function cancel_scheduling() {
			$url = sprintf(
					NELIOAB_BACKEND_URL . '/exp/%2$s/%1$s/unschedule',
					$this->get_id(), $this->get_exp_kind_url_fragment()
				);
			NelioABBackend::remote_get( $url );
		}


		/**
		 * It loads the goals from the JSON file returned by AppEngine.
		 *
		 * This function creates `NelioABGoal` objects using the information
		 * available in each individual goal and adds them to the given
		 * experiment.
		 *
		 * @param $exp        NelioABExperiment the experiment in which the goals will be added.
		 * @param $json_goals array             a list of JSON elements.
		 *
		 * @return void
		 *
		 * @see NelioABGoalsManager::load_goal_from_json
		 *
		 * @since 3.1.0
		 */
		public static function load_goals_from_json( $exp, $json_goals = array() ) {
			usort( $json_goals, array( 'NelioABExperiment', 'sort_goals' ) );
			foreach ( $json_goals as $goal )
				NelioABGoalsManager::load_goal_from_json( $exp, $goal );
		}


		/**
		 * Given two goals, this function indicates which one comes first.
		 *
		 * @param NelioABGoal|Object $a a goal object (which has a `order` attribute).
		 * @param NelioABGoal|Object $b a goal object (which has a `order` attribute).
		 *
		 * @return int a number less than 0 if $a comes first, greater than 0 if $b
		 *             comes first, or 0 if it doesn't matter.
		 *
		 * @since 3.3.0
		 */
		public static function sort_goals( $a, $b ) {
			if ( isset( $a->order ) && isset( $b->order ) )
				return $a->order - $b->order;
			if ( isset( $a->order ) )
				return -1;
			if ( isset( $b->order ) )
				return 1;
			return 0;
		}


		/**
		 * It duplicates the experiment in AppEngine and returns the new ID.
		 *
		 * This function should be extended by concrete subclasses, because, as it
		 * is defined in this abstract class, it only duplicates all the the
		 * information of the experiment in AppEngine. Any additional data that is
		 * stored in WordPress, has to be duplicated by the overwriting methods.
		 *
		 * @param string $new_name the new name of the duplicated experiment.
		 *
		 * @since 3.4.0
		 */
		public function duplicate( $new_name ) {

			// If the experiment is not running, or finished, or ready...
			// then it cannot be duplicated
			if ( $this->get_status() != NelioABExperiment::STATUS_RUNNING &&
			     $this->get_status() != NelioABExperiment::STATUS_FINISHED &&
			     $this->get_status() != NelioABExperiment::STATUS_SCHEDULED &&
			     $this->get_status() != NelioABExperiment::STATUS_READY ) {
				return;
			}//end if

			$post_data = get_post( $this->get_id(), ARRAY_A );
			if ( $post_data && ! is_wp_error( $post_data ) ) {

				$json = $this->post_content2json( $post_data['post_content'] );

				$json->key->id = 0;
				$json->name = $new_name;
				$json->creation = nelioab_get_time();
				$json->status = NelioABExperiment::STATUS_READY;
				$json->start = null;
				$json->finalization = null;

				// Remove heatmap goals.
				$goals = array();
				foreach ( $json->goals as $goal ) {
					if ( $goal->kind !== 'HeatmapGoal' ) {
						array_push( $goals, $goal );
					}//end if
				}//end foreach
				$json->goals = $goals;

				if ( isset( $json->goals ) ) {
					$id = -9000;
					foreach ( $json->goals as &$goal ) {
						$goal->key->id = $id;
						--$id;
					}//end foreach
				}//end if

				if ( isset( $json->alternatives ) ) {
					$id = -9000;
					$old_alt_ids = array();
					$new_alt_ids = array();
					foreach ( $json->alternatives as &$alt ) {
						array_push( $old_alt_ids, $alt->key->id );
						array_push( $new_alt_ids, $id );
						$alt->key->id = $id;
						--$id;
					}//end foreach
				}//end if

				unset( $post_data['ID'] );
				$post_data['post_title'] = $new_name;
				$id = wp_insert_post( $post_data );
				if ( $id ) {
					$post_data['ID'] = $id;
				}//end if

				$json = $this->post_duplicate( $json, $id );

				if ( isset( $json->alternatives ) ) {
					$this->update_alternatives_ids( $id, $old_alt_ids, $new_alt_ids );
				}//end if

				switch ( $json->status ) {
					case NelioABExperiment::STATUS_DRAFT:
						$status = 'draft';
						break;
					case NelioABExperiment::STATUS_PAUSED:
						$status = 'nelioab_paused';
						break;
					case NelioABExperiment::STATUS_READY:
						$status = 'nelioab_ready';
						break;
					case NelioABExperiment::STATUS_RUNNING:
						$status = 'nelioab_running';
						break;
					case NelioABExperiment::STATUS_FINISHED:
						$status = 'nelioab_finished';
						break;
					case NelioABExperiment::STATUS_TRASH:
						$status = 'trash';
						break;
					case NelioABExperiment::STATUS_SCHEDULED:
						$status = 'nelioab_scheduled';
						break;
					default:
						$status = 'draft';
				}

				$post_data['post_content'] = $this->json2post_content( $json );
				$post_data['post_status'] = $status;

				wp_update_post( $post_data );


			}//end if

			NelioABExperimentsManager::refresh();

		}//end duplicate()


		/**
		 * It saves the experiment.
		 *
		 * @return int the new ID.
		 *
		 * @since 1.0.10
		 */
		public function save() {

			NelioABExperimentsManager::get_experiments();

			if ( $this->get_id() < 0 ) {
				$this->id = wp_insert_post( array(
					'post_title' => $this->get_name(),
					'post_type'  => 'nelioab_local_exp',
				) );
			}//end if

			if ( $this->get_status() != NelioABExperiment::STATUS_PAUSED &&
				$this->get_status() != NelioABExperiment::STATUS_RUNNING &&
				$this->get_status() != NelioABExperiment::STATUS_FINISHED &&
				$this->get_status() != NelioABExperiment::STATUS_TRASH ) {
				$this->set_status( $this->determine_proper_status() );
			}

			// DO Custom Stuff (such as, for instance, preparing the alternatives)
			$this->do_save();

			$result = $this->encode_for_appengine();

			if ( isset( $result['goals'] ) ) {
				$id = -9000;
				for ( $i = 0; $i < count( $result['goals'] ); ++$i ) {
					if ( $result['goals'][$i]['key']['id'] < 0 ) {
						$result['goals'][$i]['key']['id'] = $id;
						--$id;
					}//end if
				}//end foreach
			}//end if

			if ( isset( $result['alternatives'] ) ) {

				$id = -9000;
				$old_alt_ids = array();
				$new_alt_ids = array();
				for ( $i = 0; $i < count( $result['alternatives'] ); ++$i ) {
					if ( $result['alternatives'][$i]['key']['id'] < 0 ) {
						array_push( $old_alt_ids, $result['alternatives'][$i]['key']['id'] );
						array_push( $new_alt_ids, $id );
						$result['alternatives'][$i]['key']['id'] = $id;
						--$id;
					} else {
						array_push( $old_alt_ids, $result['alternatives'][$i]['key']['id'] );
						array_push( $new_alt_ids, $result['alternatives'][$i]['key']['id'] );
					}//end if
				}//end for

				$this->update_alternatives_ids( $this->get_id(), $old_alt_ids, $new_alt_ids );

			}//end if

			switch ( $this->get_status() ) {
				case NelioABExperiment::STATUS_DRAFT:
					$status = 'draft';
					break;
				case NelioABExperiment::STATUS_PAUSED:
					$status = 'nelioab_paused';
					break;
				case NelioABExperiment::STATUS_READY:
					$status = 'nelioab_ready';
					break;
				case NelioABExperiment::STATUS_RUNNING:
					$status = 'nelioab_running';
					break;
				case NelioABExperiment::STATUS_FINISHED:
					$status = 'nelioab_finished';
					break;
				case NelioABExperiment::STATUS_TRASH:
					$status = 'trash';
					break;
				case NelioABExperiment::STATUS_SCHEDULED:
					$status = 'nelioab_scheduled';
					break;
				default:
					$status = 'draft';
			}

			$post = get_post( $this->get_id() );
			$creation_date = 0;
			try {
				if ( $post ) {
					$json = $this->post_content2json( $post->post_content );
					if ( ! empty( $json->creation ) ) {
						$creation_date = $json->creation;
					}//end if
				}//end if
			} catch ( Exception $e ) {
			}//end try

			if ( strtotime( $creation_date ) < 1451606400 ) {
				$creation_date = nelioab_get_time();
			}//end if

			// Make sure that the creation date is correct:
			$result['creation'] = $creation_date;

			wp_update_post( array(
				'ID'           => $this->get_id(),
				'post_name'    => 'nelioab-local-exp-' . $this->get_id(),
				'post_title'   => $this->get_name(),
				'post_content' => $this->json2post_content( $result ),
				'post_status'  => $status,
			) );

			NelioABExperimentsManager::refresh();

			return $id;

		}//end save()


		/**
		 * It removes the experiment.
		 *
		 * @return void
		 *
		 * @since 1.0.10
		 */
		public function remove() {

			$this->do_remove();
			wp_update_post( array(
				'ID'          => $this->get_id(),
				'post_status' => 'nelioab_deleted',
			) );
			NelioABExperimentsManager::refresh();

		}//end remove()


		/**
		 * It starts the experiment.
		 *
		 * When an experiment is started, its status is set to `STATUS_RUNNING`.
		 *
		 * @return void
		 *
		 * @since 1.0.10
		 */
		public function start() {

			if ( $this->get_status() === self::STATUS_RUNNING ) {
				return;
			}//end if

			$this->pre_start();

			try {

				$post = get_post( $this->get_id() );

				$params = array( 'body' => array(
					'customerId'         => NelioABAccountSettings::get_customer_id(),
					'siteId'             => NelioABAccountSettings::get_site_id(),
					'registrationNumber' => NelioABAccountSettings::get_reg_num(),
					'object'             => urldecode( $post->post_content ),
				) );

				$url = NELIOAB_BACKEND_SERVLET_URL . '/exp/createstart';

				$result = NelioABBackend::remote_post_raw( $url, $params );

				$json = json_decode( $result['body'] );
				if ( isset( $json->status ) && $json->status == NelioABExperiment::STATUS_RUNNING ) {

					$post->post_content = $this->json2post_content( $json );
					$post->post_status = 'nelioab_running';
					wp_update_post( $post );

				} else {

					$err = NelioABErrCodes::EXPERIMENT_NOT_RUNNING;
					throw new Exception( NelioABErrCodes::to_string( $err ), $err );

				}//end if

			}//end try
			catch ( Exception $e ) {

				throw $e;

			}//end catch

			NelioABExperimentsManager::refresh();
			update_option( 'nelioab_last_start_or_stop', time() );

		}//end start()



		/**
		 * It stops the experiment.
		 *
		 * When an experiment is stopped, its status is set to `STATUS_FINISHED`.
		 *
		 * @return void
		 *
		 * @since 1.0.10
		 */
		public function stop() {

			$this->do_stop();

			$post = get_post( $this->get_id() );
			$json = $this->post_content2json( $post->post_content );

			$json->status = NelioABExperiment::STATUS_FINISHED;
			$json->finalization = nelioab_get_time();

			$post->post_content = $this->json2post_content( $json );
			$post->post_status = 'nelioab_finished';
			wp_update_post( $post );

			NelioABExperimentsManager::refresh();
			update_option( 'nelioab_last_start_or_stop', time() );

		}//end stop()


		/**
		 * It returns the related post ID (if any) or false otherwise.
		 *
		 * Some types of experiments have an associated post. For instance, Page
		 * Alternative experiments are testing a specific page, whilst Heatmap
		 * Experiments are tracking the heatmaps for a specific post. This function
		 * returns the value of the related post.
		 *
		 * @return boolean|int the related post ID (if any) or false otherwise.
		 *
		 * @since 3.4.0
		 */
		public function get_related_post_id() {
			return false;
		}


		/**
		 * It returns a human-readable version of the given status.
		 *
		 * For instance, the constant `STATUS_DRAFT` is _Draft_, `STATUS_PAUSED` is
		 * _Paused_, and so on.
		 *
		 * @param int $status the status whose label has to be obtained.
		 *
		 * @return string the label of the given status. If the provided status
		 *                does not exist, _Unknown Status_ is returned.
		 *
		 * @since 4.1.0
		 */
		public static function get_label_for_status( $status ) {
			switch ( $status ) {
			case NelioABExperiment::STATUS_DRAFT:
					return __( 'Draft', 'nelioab' );
			case NelioABExperiment::STATUS_PAUSED:
					return __( 'Paused', 'nelioab' );
				case NelioABExperiment::STATUS_READY:
					return __( 'Prepared', 'nelioab' );
				case NelioABExperiment::STATUS_FINISHED:
					return __( 'Finished', 'nelioab' );
				case NelioABExperiment::STATUS_RUNNING:
					return __( 'Running', 'nelioab' );
				case NelioABExperiment::STATUS_SCHEDULED:
					return __( 'Scheduled', 'nelioab' );
				case NelioABExperiment::STATUS_TRASH:
					return __( 'Trash' );
				default:
					return __( 'Unknown Status', 'nelioab' );
			}
		}


		/**
		 * This function encodes this experiment for appengine.
		 *
		 * @returns array this experiment encoded for appengine.
		 *
		 * @since 1.5.6
		 */
		public function encode_for_appengine() {

			// 1. PREPARE GOALS.
			$goals = array();
			foreach ( $this->get_goals() as $goal ) {
				array_push( $goals, $goal->encode_for_appengine() );
			}//end foreach

			// 2. PREPARE THE OBJECT.
			$result = array(
				'key'  => array(
					'id'   => $this->get_key_id(),
				),
				'kind' => $this->get_textual_type(),
				'site' => NelioABAccountSettings::get_site_id(),
				'name' => $this->get_name(),
				'description'    => $this->get_description(),
				'status'         => $this->get_status(),
				'creation'       => $this->get_creation_date(),
				'finalization'   => $this->get_end_date(),
				'benefit'        => 0.0,
				'goals'          => $goals,
				'finalizationMode'      => $this->get_finalization_mode(),
				'finalizationModeValue' => $this->get_finalization_value(),
			);

			return $result;

		}//end encode_for_appengine()


		/**
		 * This static function creates a new instance of this class, loading all the required information from AppEngine.
		 *
		 * @param WP_Post|int $post the ID of the local `nelioab_local_exp` (or the related wp_post) that contains all the info about this experiment.
		 *
		 * @return NelioABExperiment a new instance of this class with all the required information from AppEngine.
		 *
		 * @abstract
		 * @since 4.1.0
		 */
		public static function load( /** @noinspection PhpUnusedParameterInspection */ $post ) {
			throw new RuntimeException( 'Not Implemented Method' );
		}


		/**
		 * It returns the kind of the experiment, as required in AppEngine URL's.
		 *
		 * For instance, Heatmap experiments use `hm` and Post or Headlines
		 * experiments use `post`.
		 *
		 * @return string the kind of the experiment, as required in AppEngine URL's.
		 *
		 * @since 1.5.6
		 */
		public abstract function get_exp_kind_url_fragment();


		/**
		 * It actually stops the experiment.
		 *
		 * This function is called by the "stop" method and makes sure that this
		 * experiment is actually stopped in AE.
		 *
		 * @return void
		 *
		 * @since 1.0.10
		 */
		public abstract function do_stop();


		/**
		 * TODO
		 *
		 * @since 4.4.0
		 */
		public abstract function do_save();


		/**
		 * It actually removes the experiment.
		 *
		 * This function is called by the "remove" method and makes sure that all
		 * content that has to be removed from WordPress DB is removed.
		 *
		 * @return void
		 *
		 * @since 4.4.0
		 */
		public abstract function do_remove();


		/**
		 * It checks if the experiment can be started and prepares any local info
		 * that may need tweaking.
		 *
		 * This function is called before the experiment is actually started in AE.
		 *
		 * @return void
		 *
		 * @since 4.4.0
		 */
		public abstract function pre_start();


		/**
		 * TODO
		 *
		 * @return void
		 *
		 * @since 4.4.0
		 */
		public function post_duplicate( $json, $exp_id ) {

			return $json;

		}//end post_duplicate()


		/**
		 * Returns an identifier of the original alternative.
		 *
		 * @return int an identifier of the original alternative.
		 *
		 * @since 4.0.0
		 */
		public abstract function get_originals_id();

		/**
		 * TODO
		 *
		 * @since 4.0.0
		 */
		public function update_alternatives_ids( $exp_id, $old_alt_ids, $new_alt_ids ) {

			// Nothing to be done by default.

		}//end update_alternatives_ids()

		/**
		 * TODO
		 *
		 * @since 4.0.0
		 */
		protected function json2post_content( $json ) {

			if ( NELIOAB_SHOW_LOCAL_EXPS ) {
				$encoded_json = json_encode( $json, JSON_PRETTY_PRINT );
			} else {
				$encoded_json = json_encode( $json );
			}//end if

			return urlencode( $encoded_json );

		}//end json2post_content()

		/**
		 * TODO
		 *
		 * @since 4.0.0
		 */
		protected function post_content2json( $post_content ) {

			return json_decode( urldecode( $post_content ) );

		}//end post_content2json

	}//NelioABExperiment

}

