<?php

/*
 * @since      1.0
 * @author     MAHABUB <mahabub@solvease.com>
 */

class Solvease_Roles_Capabilities_Admin {

    /**
     * plugin name
     * @var string
     */
    private $plugin_name;

    /**
     * plugin version
     * @var string
     */
    private $plugin_version;

    /**
     * maintain the capabilities
     * @var object  
     */
    protected $capability_table;

    /**
     * translation domain
     * @var string  
     */
    protected $translation_domain;
    protected $plugin_caps;

    /**
     *  class construction
     * @param string $plugin_name
     * @param string $plugin_version
     * @param string $translation_domain
     */
    function __construct($plugin_name, $plugin_version, $translation_domain) {

        $this->plugin_name = $plugin_name;
        $this->plugin_version = $plugin_version;
        $this->translation_domain = $translation_domain;
        $this->plugin_caps = Solvease_Roles_Capabilities_User_Caps::solvease_roles_capabilities_caps();

        // reguster script
        add_action('admin_enqueue_scripts', array($this, 'solvease_roles_capabilities_register_script'));

        // regester styles
        add_action('admin_enqueue_scripts', array($this, 'solvease_roles_capabilities_register_styles'));

        $this->capability_table = new Solvease_Roles_Capabilities_Table($translation_domain, $this->plugin_caps);
    }

    private function solvease_roles_capabilities_menu_title() {
        return __('Roles & Capabilities', $this->translation_domain);
    }

    /**
     * generate plugin Menu
     */
    public function solvease_roles_capabilities_menu() {
        //$menu_title = __('Roles & Capabilities', $this->translation_domain);
        //$plugin_caps = Solvease_Roles_Capabilities_User_Caps::solvease_roles_capabilities_caps();

        add_submenu_page(
                'users.php', $this->solvease_roles_capabilities_menu_title(), $this->solvease_roles_capabilities_menu_title(), $this->plugin_caps['manage_all_capabilities'], 'solvease-roles-capablities', array($this->capability_table, 'my_test_function')
        );

        add_submenu_page(
                null, null, null, $this->plugin_caps['manage_user_capabilities'], 'solvease-def', array($this->capability_table, 'solvese_roles_capabilities_users_roles_caps')
        );
    }

    /**
     *  add cpabilities link in user list table
     * @param array $actions
     * @param object $user_object
     * @return string
     */
    public function solvease_roles_capabilities_add_user_row_action($actions, $user_object) {
        if (current_user_can($this->plugin_caps['manage_user_capabilities'])) {
            $actions['edit_user_cap'] = "<a class='solvease-edit-user-cap' href='" . wp_nonce_url("users.php?page=solvease-def&amp;user=$user_object->ID", "solvease_rnc_save_user_rc_" . $user_object->ID, "solvease_rnc_save_user_rc") . "'>" . $this->solvease_roles_capabilities_menu_title() . "</a>";
        }
        return $actions;
    }

    /**
     * register scripts
     */
    public function solvease_roles_capabilities_register_script() {

        wp_register_script(
                'solvease-roles-capabilities-validator-js', plugins_url('/js/jquery.validate.min.js', dirname(__FILE__)), array('jquery')
        );

        wp_register_script(
                'solvease-roles-capabilities-custom-js', plugins_url('/js/custom_script.js', dirname(__FILE__)), array('jquery')
        );

        wp_register_script(
                'solvease-roles-capabilities-sticky-js', plugins_url('/js/sticky.js', dirname(__FILE__)), array('jquery')
        );

        wp_register_script(
                'solvease-roles-capabilities-uniform-js', plugins_url('/js/jquery.uniform.js', dirname(__FILE__)), array('jquery')
        );

        wp_register_script(
                'solvease-roles-capabilities-bootstrap-js', 'http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js', array('jquery')
        );
    }

    /**
     * register Stylesheet
     */
    public function solvease_roles_capabilities_register_styles() {
        wp_register_style('solvease-roles-capabilities-bootstrap-css', plugins_url('/css/bs-modal.css', dirname(__FILE__)));
        wp_register_style('solvease-roles-capabilities-custom-css', plugins_url('/css/custom.css', dirname(__FILE__)));

        wp_register_style('solvease-roles-capabilities-font-awesome', plugins_url('/css/font-awesome.css', dirname(__FILE__)));
        wp_register_style('solvease-roles-capabilities-uniform', plugins_url('/css/uniform.default.css', dirname(__FILE__)));
    }

}
