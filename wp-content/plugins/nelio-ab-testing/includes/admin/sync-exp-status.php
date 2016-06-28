<script type="text/javascript" src="<?php
	echo '//storage.googleapis.com/2' . NELIOAB_BACKEND_NAME . '/' . NelioABAccountSettings::get_site_id() . '.js?time=' . time();
?>"></script>

<script type="text/javascript">
(function( $ ) {

	jQuery.holdReady( true );

	try {
		var runningExps = <?php
			include_once( NELIOAB_MODELS_DIR . '/experiments-manager.php' );
			$exps = NelioABExperimentsManager::get_running_experiments();
			$running_exps = array();
			$now = time();
			foreach ( $exps as $exp ) {
				$time_since_start = $now - strtotime( $exp->get_start_date() );
				if ( $exp->get_finalization_mode() !== NelioABExperiment::FINALIZATION_MANUAL &&
				     $time_since_start > 600 ) {
					array_push( $running_exps, array(
						'id'    => $exp->get_id(),
						'keyId' => $exp->get_key_id(),
					) );
				}//end if
			}//end foreach
			echo json_encode( $running_exps );
		?>;
		var ids = [];
		var keyIds = [];

		// Check if the IDs are still in the JSON. If one of the IDs is no longer
		// in the JSON generated in our cloud, the experiment is no longer running
		// and, therefore, has to be stopped locally too.
		for ( var q = 0; q < runningExps.length; ++q ) {
			var runningId = runningExps[q].id;
			var runningKeyId = runningExps[q].keyId;
			var found = false;
			for ( var i = 0; i < NelioABBasic.envs.length; ++i ) {
				var env = NelioABBasic.envs[i];
				for ( var j = 0; j < env.ab.length; ++j ) {
					var id = env.ab[j].name.replace( /[a-z_A-Z]/g, '' );
					if ( id == runningKeyId ) {
						found = true;
					}//end if
				}//end for
			}//end for
			if ( ! found ) {
				ids.push( runningId );
				keyIds.push( runningKeyId );
			}//end if
		}//end for

		if ( ids.length > 0 ) {

			$.ajax({

				url: ajaxurl,

				data: {
					action: 'nelioab_sync_experiment_status',
					ids: ids,
					keyIds: keyIds
				},

				success: function() {
					jQuery.holdReady( false );
				},

				error: function() {
					jQuery.holdReady( false );
				}

			});

		} else {

			jQuery.holdReady( false );

		}//end if

	} catch ( e ) {

		jQuery.holdReady( false );

	}//end try

})( jQuery );
</script>
