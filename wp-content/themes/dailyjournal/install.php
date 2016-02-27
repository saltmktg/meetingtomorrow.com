function install_dailyjournal_theme() {
        global $pagenow;
        if ( !( 'install.php' == $pagenow && isset( $_REQUEST['step'] ) && 2 == $_REQUEST['step'] ) ) {
                return;
        }
        switch_theme( 'dailyjournal' );
}
add_action( 'shutdown', 'install_dailyjournal_theme' );
