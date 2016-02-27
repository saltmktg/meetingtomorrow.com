(function( $, undefined ) {

	"use strict";

	/**
	 * Given a certain date (string or number), it returns the remaining time.
	 *
	 * @param  {string|number}  endtime  the goal date.
	 * @return {object}  the remaining time for reaching the given date.
	 */
	function getTimeRemaining( endtime ) {
		var t;
		var now = Date.parse( new Date() );
		if ( typeof endtime === 'string' ) {
			t = Date.parse( endtime ) - now;
		} else {
			t = endtime - now;
		}
		var seconds = Math.floor( ( t / 1000 ) % 60 );
		var minutes = Math.floor( ( t / 1000 / 60 ) % 60 );
		var hours = Math.floor( ( t / ( 1000 * 60 * 60 ) ) % 24 );
		var days = Math.floor( t / ( 1000 * 60 * 60 * 24 ) );
		return {
			'total': t,
			'days': days,
			'hours': hours,
			'minutes': minutes,
			'seconds': seconds
		};
	}//end getTimeRemaining

	// Let's see if the NelioABAvailablePromos object has been loaded and,
	// if it hasn't, let's create it:
	if ( typeof window.NelioABAvailablePromos !== 'object' ) {
		window.NelioABAvailablePromos = {
			trial:[],
			monthlyBasic:[],        yearlyBasic:[],
			monthlyProfessional:[], yearlyProfessional:[],
			monthlyEnterprise:[],   yearlyEnterprise:[]
		};
	}//endif

	// We need to select the promo that is now relevant for our customer.
	var currentPromo = false;
	if ( typeof NelioABPromo === 'object' ) {

		switch ( NelioABPromo.plan ) {

			case 'trial':
			case 'free-trial':
				if ( NelioABAvailablePromos.freeTrial.length > 0 ) {
					currentPromo = NelioABAvailablePromos.freeTrial[0];
				}//endif
				break;

			case 'monthly-basic':
				if ( NelioABAvailablePromos.monthlyBasic.length > 0 ) {
					currentPromo = NelioABAvailablePromos.monthlyBasic[0];
				}//endif
				break;

			case 'yearly-basic':
				if ( NelioABAvailablePromos.yearlyBasic.length > 0 ) {
					currentPromo = NelioABAvailablePromos.yearlyBasic[0];
				}//endif
				break;

			case 'monthly-professional':
				if ( NelioABAvailablePromos.monthlyProfessional.length > 0 ) {
					currentPromo = NelioABAvailablePromos.monthlyProfessional[0];
				}//endif
				break;

			case 'yearly-professional':
				if ( NelioABAvailablePromos.yearlyProfessional.length > 0 ) {
					currentPromo = NelioABAvailablePromos.yearlyProfessional[0];
				}//endif
				break;

			case 'monthly-enterprise':
				if ( NelioABAvailablePromos.monthlyEnterprise.length > 0 ) {
					currentPromo = NelioABAvailablePromos.monthlyEnterprise[0];
				}//endif
				break;

			case 'yearly-enterprise':
				if ( NelioABAvailablePromos.yearlyEnterprise.length > 0 ) {
					currentPromo = NelioABAvailablePromos.yearlyEnterprise[0];
				}//endif
				break;

		}//endswitch

	}//endif


	// If I didn't find a promo, just quit.
	if ( ! currentPromo ) {
		return;
	}


	// Let's prepare the URL
	var buttonUrl = currentPromo.buttonUrl;
	buttonUrl = buttonUrl.replace( '${customer.firstname}',
			NelioABPromo.user.firstname );
	buttonUrl = buttonUrl.replace( '${customer.firstname.encoded}',
			encodeURIComponent( NelioABPromo.user.firstname ) );

	buttonUrl = buttonUrl.replace( '${customer.lastname}',
			NelioABPromo.user.lastname );
	buttonUrl = buttonUrl.replace( '${customer.lastname.encoded}',
			encodeURIComponent( NelioABPromo.user.lastname ) );

	buttonUrl = buttonUrl.replace( '${customer.email}',
			NelioABPromo.user.email );
	buttonUrl = buttonUrl.replace( '${customer.email.encoded}',
			encodeURIComponent( NelioABPromo.user.email ) );


	// Now we create an HTML node object that will contain the promo
	var $promoContent = $( '<div>' +
			'<p class="expl">' + currentPromo.description + '</p>' +
			'<p class="action"><a class="button button-primary" target="_blank" href="' +
				 buttonUrl + '">' + currentPromo.buttonLabel + '</a></p>' +
			'</div>'
		);


	// Let's check where the promo starts
	var promoStartDate = 0;
	if ( typeof currentPromo.startDate === 'string' ) {
		switch ( currentPromo.startDate ) {

			case 'lastRenewal':
				promoStartDate = Date.parse( NelioABPromo.dates.lastRenewal );
				break;

			case 'creation':
				promoStartDate = Date.parse( NelioABPromo.dates.creation );
				break;

			default:
				promoStartDate = Date.parse( currentPromo.startDate );
				break;

		}//endswitch
	}

	// If the promo start date is in the future, quit.
	if ( promoStartDate > Date.parse( new Date() ) ) {
		$promoContent.remove();
		return;
	}



	// A date after which the promo is no longer available
	var promoEndDate = false;

	// If we specified an end date, the promo will not be available to anyone after that date.
	if ( typeof currentPromo.endDate === 'string' ) {
		promoEndDate = Date.parse( currentPromo.endDate );
	}

	// If the promo includes a duration...
	if ( typeof currentPromo.endDate === 'string' && typeof currentPromo.duration === 'number' ) {
		// We compute the end date.
		var promoEndDateBasedOnDuration = promoStartDate + currentPromo.duration * 3600000;
		// But careful! The computed end date has to be within the promo lifespan; otherwise, we can't apply the promo
		if ( promoEndDateBasedOnDuration <= promoEndDate ) {
			promoEndDate = promoEndDateBasedOnDuration ;
		} else {
			$promoContent.remove();
			return;
		}
	}


	// If the `promoEndDate` object is set, let's check if the promo is still valid
	if ( promoEndDate ) {

		// If it already ended, there's no need to add the promo
		if ( promoEndDate <= Date.parse( new Date() ) ) {
			$promoContent.remove();
			return;
		}

	}


	if ( promoEndDate && currentPromo.showCountdown ) {

		// Otherwise, we can add the timer
		$promoContent.html( '<p class="timer"></p>' + $promoContent.html() );
		var $timer = $promoContent.find( 'p.timer' );

		/**
		 * Returns an HTML that displays the given number in a timer box:
		 *
		 * For example:
		 *     +-----+
		 *     | 2 7 |
		 *     +-----+
		 *      hours
		 *
		 * @param  {int}     number  the number of units that will be displayed in the box.
		 * @param  {string}  unit    the unit that will be displayed below the box.
		 *
		 * @return {string}  the HTML code that displays the given number in a timer box:
		 */
		function makeBox( number, unit ) {
			if ( number < 10 ) {
				number = '0' + number;
			} else {
				number = '' + number;
			}
			number = number.replace( /(\d)(\d)/,
					'<span class="first">$1</span>' +
					'<span class="second">$2</span>'
				);
			return '<div class="block">' +
				'<div class="number">' + number + '</div>' +
				'<div class="unit">' + unit + '</div>' +
				'</div>';
		}

		/**
		 * Updates the timer HTML node (`p.timer`).
		 *
		 * Once the function has been called, a timeout that will update the timer
		 * every second is set.
		 */
		function updateTimer() {
			var timeRemaining = getTimeRemaining( promoEndDate );
			var string = '';

			if ( timeRemaining.days > 0 ) {

				string += makeBox( timeRemaining.days, 'days' );

			}//endif - days

			string += makeBox( timeRemaining.hours, 'hours' );

			string += makeBox( timeRemaining.minutes, 'mins' );

			string += makeBox( timeRemaining.seconds, 'secs' );

			$timer.html( string );

			if ( timeRemaining.total > 0 ) {
				setTimeout( updateTimer, 1000 );
			}
		}

		// Call the function a first time and, thus, set the timer.
		updateTimer();
	}

	// We add the promo notice
	$(document).ready(function() {
		var $promo = $( '#nelioab-promo-notice' );
		if ( currentPromo.isDismissable ) {
			$promo.html( '<a href="#" class="dismiss-nelioab-promo" title="Dismiss"><span class="dashicons dashicons-dismiss"></span></a>' );
		}
		$promo.append( $promoContent );
		$promo.show();

		$promo.find( '.dismiss-nelioab-promo' ).click( function( ev ) {
			ev.preventDefault();
			$.ajax({
				url: ajaxurl,
				data: {
					action: 'nelioab_dismiss_promo'
				},
				success: function() {
					$promo.hide();
				},
				error: function() {
					$promo.hide();
				}
			});
		});
	});

})( jQuery );

