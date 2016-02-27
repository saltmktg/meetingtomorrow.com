<?php
/*
Plugin Name: Shortcode Manager
Plugin URI: http://wordpress.org/extend/plugins/shortcode-manager/
Description: Quickly and easily manage shortcodes.
Version: 1.4.0
Author: Matt Gibbs
Author URI: https://uproot.us/
License: GPLv2
*/

$sb = new Shortcode_Manager();


class Shortcode_Manager
{

    /*
     * Class constructor
     */
    function __construct() {

        define( 'SHORTCODE_MANAGER_VERSION', '1.4.0' );
        define( 'SHORTCODE_MANAGER_DIR', dirname( __FILE__ ) );
        define( 'SHORTCODE_MANAGER_URL', plugins_url( basename( __DIR__ ) ) );
        $this->init();
    }


    /**
     * Get started once WP fully loads
     */
    function init() {
        global $wpdb;

        // Update the wp_option value
        $db_version = get_option( 'scm_version' );

        if ( false === $db_version ) {
            $wpdb->query( "
            CREATE TABLE IF NOT EXISTS {$wpdb->prefix}shortcodes (
                `id` int unsigned AUTO_INCREMENT PRIMARY KEY,
                `name` varchar(128),
                `content` mediumtext)" );

            // Migrate from old table
            $wpdb->query( "INSERT INTO {$wpdb->prefix}shortcodes SELECT * FROM {$wpdb->prefix}shortbus" );
            $wpdb->query( "DROP TABLE {$wpdb->prefix}shortbus" );

            // Add the version
            add_option( 'scm_version', SHORTCODE_MANAGER_VERSION );
        }
        elseif ( version_compare( $db_version, SHORTCODE_MANAGER_VERSION, '<' ) ) {
            update_option( 'scm_version', SHORTCODE_MANAGER_VERSION );
        }

        // Add hooks
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_shortcode( 'sb', array( $this, 'shortcode' ) );
        add_action( 'wp_ajax_shortcode_manager', array( $this, 'handle_ajax' ) );
        add_filter( 'widget_text', 'do_shortcode' );
    }


    /**
     * Shortcode handler
     */
    function shortcode( $atts ) {
        global $wpdb;

        $atts = (object) $atts;
        if (false === empty($atts->name)) {
            $row = $wpdb->get_row( "SELECT content FROM `{$wpdb->prefix}shortcodes` WHERE name = '$atts->name' LIMIT 1" );
            ob_start();
            echo '<div class="shortcode-manager">' . eval( '?>' . $row->content ) . '</div>';
            return ob_get_clean();
        }
    }


    /**
     * Create the "Tools > Shortcodes" admin menu
     */
    function admin_menu() {
        add_submenu_page( 'tools.php', 'Shortcodes', 'Shortcodes', 'manage_options', 'shortcode-manager', array( $this, 'admin_page' ) );
    }


    /**
     * Format the AJAX response
     */
    function json_response( $status = 'ok', $status_message = null, $data = null ) {
        if ( empty( $status_message ) ) {
            $status_message = '<p>' . $status_message . '</p>';
        }
        return json_encode(
            array(
                'status' => $status,
                'status_message' => $status_message,
                'data' => $data,
            )
        );
    }


    /**
     * Admin AJAX handler
     */
    function handle_ajax() {

        global $wpdb;

        $post = stripslashes_deep( $_POST );
        $id = isset( $post['id'] ) ? (int) $post['id'] : 0;
        $method = isset( $post['method'] ) ? $post['method'] : '';

        // load
        if ( 'load' == $method ) {
            $content = $wpdb->get_var( "SELECT content FROM `{$wpdb->prefix}shortcodes` WHERE id = '$id' LIMIT 1" );
            echo $this->json_response( 'ok', null, $content );
        }

        // add 
        elseif ( 'add' == $method ) {
            $name = trim( $post['name'] );

            if ( !preg_match( '/^[A-Za-z0-9\-_]+$/', $name ) ) {
                echo $this->json_response( 'error', 'Please use only alphanumeric characters, hyphens, and underscores.' );
            }
            else {
                $wpdb->insert( $wpdb->prefix . 'shortcodes', array( 'name' => $name ) );
                echo $this->json_response( 'ok', 'Shortcode added.', array( 'id' => (int) $wpdb->insert_id ) );
            }
        }

        // edit
        elseif ( 'edit' == $method ) {
            $sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}shortcodes SET content = %s WHERE id = %d LIMIT 1", $post['content'], $id );
            $wpdb->query( $sql );
            echo $this->json_response( 'ok', 'Shortcode saved.' );
        }

        // delete
        elseif ( 'delete' == $method ) {
            $wpdb->query( "DELETE FROM `{$wpdb->prefix}shortcodes` WHERE id = '$id' LIMIT 1" );
            echo $this->json_response( 'ok', 'Shortcode deleted.' );
        }

        exit;
    }


    /**
     * Settings page HTML
     */
    function admin_page() {
        global $wpdb;
?>
<link href="<?php echo SHORTCODE_MANAGER_URL; ?>/style.css" rel="stylesheet" />
<link href="<?php echo SHORTCODE_MANAGER_URL; ?>/js/codemirror/lib/codemirror.css" rel="stylesheet" />
<link href="<?php echo SHORTCODE_MANAGER_URL; ?>/js/select2/select2.css" rel="stylesheet" />
<script src="<?php echo SHORTCODE_MANAGER_URL; ?>/js/select2/select2.min.js"></script>
<script src="<?php echo SHORTCODE_MANAGER_URL; ?>/js/codemirror/lib/codemirror.js"></script>
<script src="<?php echo SHORTCODE_MANAGER_URL; ?>/js/codemirror/mode/xml/xml.js"></script>
<script src="<?php echo SHORTCODE_MANAGER_URL; ?>/js/codemirror/mode/javascript/javascript.js"></script>
<script src="<?php echo SHORTCODE_MANAGER_URL; ?>/js/codemirror/mode/css/css.js"></script>
<script src="<?php echo SHORTCODE_MANAGER_URL; ?>/js/codemirror/mode/htmlmixed/htmlmixed.js"></script>
<script src="<?php echo SHORTCODE_MANAGER_URL; ?>/js/codemirror/mode/clike/clike.js"></script>
<script src="<?php echo SHORTCODE_MANAGER_URL; ?>/js/codemirror/mode/php/php.js"></script>
<script src="<?php echo SHORTCODE_MANAGER_URL; ?>/js/admin.js"></script>
<div class="wrap">
    <h2>Shortcode Manager</h2>

    <div id="shortcode-response" class="updated"></div>

    <div style="margin:15px 0">
        <select id="sb-select">
            <option value="">Choose a shortcode</option>
            <?php $results = $wpdb->get_results( "SELECT id, name FROM `{$wpdb->prefix}shortcodes` ORDER BY name ASC" ); ?>
            <?php foreach ( $results as $result ) : ?>
            <option value="<?php echo $result->id; ?>"><?php echo $result->name; ?></option>
            <?php endforeach; ?>
        </select>
        - or -
        <input id="shortcode-name" type="text" placeholder="Type shortcode name" value="" />
        <a id="add-shortcode" class="button">Add New</a>
    </div>
    <div id="shortcode-area" class="hidden">
        <div><textarea id="shortcode-content"></textarea></div>
        <div id="save-area">
            <input type="submit" class="button-primary" id="edit-shortcode" value="Save Changes" />
            or <a id="delete-shortcode" href="javascript:;">Delete</a>
        </div>
        <div id="loading-area" class="hidden">
            <span id="loading"></span> Loading, please wait...
        </div>
    </div>
</div>
<?php
    }
}
