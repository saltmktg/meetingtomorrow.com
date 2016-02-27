// Replace Meeting Tomorrow phone number with local city phone number
jQuery(document).ready(function(){
  var cityInfo = jQuery('.page .city-page-banner span.txt-semibold.orange').text();
  var cityInfoSplit = cityInfo.split('|');
  var cityNumber = cityInfoSplit[0];

  jQuery('.phone-number').replaceWith(cityNumber);
});

// Style Tab Content on Process Page
jQuery(document).ready(function(){
  jQuery(".process-wrap.fusion-tabs li a").each(function() {
    var text = this.innerHTML.split("<br>");
    this.innerHTML = "";
    for (var i = 0; i < text.length; i++) {
        var span = jQuery("<span />").html(text[i]);
        if (i == 0) span.addClass("process-tab-number");
        if (i == 1) span.addClass("process-tab-text");
        span.appendTo(this);
    }
  }); 
});

// Cause "Get a quote" button to load the modal window with form
jQuery(document).ready(function(){
  var quoteBtn = jQuery('a.button.large.button.default.fusion-button.button-flat.button-round.button-large.button-default.button-1.buttonshadow-no');
  if (jQuery('a.button .fusion-button-text:contains("Get a quote")').length > 0) {
      quoteBtn.attr({
        "href" : "#",
        "data-target" : ".quote_tab_popup",
        "data-toggle" : "modal"
      })
  }
});

// Cause "Get a quote" button to load the modal window with form
jQuery(document).ready(function(){
  var quoteBtn = jQuery('a.button.small.button.default.fusion-button.button-flat.button-round.button-small.button-default.button-1.buttonshadow-no');
  if (jQuery('a.button .fusion-button-text:contains("Get a Free Quote")').length > 0) {
      quoteBtn.attr({
        "href" : "#",
        "data-target" : ".quote_tab_popup",
        "data-toggle" : "modal"
      })
  }
});

// Add placeholder text to Google Custom Search
jQuery(document).ready(function(){
  setTimeout(function(){ 
    jQuery(".gsc-input").attr("placeholder", "Search");
  }
 , 1000 );
});

// Change position of Process Map on click
jQuery(document).ready(function(){
  jQuery('.process-wrap.fusion-tabs li:nth-child(1) a').click(function() { 
    jQuery('.process-image').css({'background-position': 'center 20px'}); 
  });   
});

jQuery(document).ready(function(){
  jQuery('.process-wrap.fusion-tabs li:nth-child(2) a').click(function() { 
    jQuery('.process-image').css({'background-position': 'center -254px'}); 
  });   
});

jQuery(document).ready(function(){
  jQuery('.process-wrap.fusion-tabs li:nth-child(3) a').click(function() { 
    jQuery('.process-image').css({'background-position': 'center -503px'}); 
  });   
});

// Change position of Process Map on click (ON MOBILE)
jQuery(window).resize(function(){     
    if (jQuery('#wrapper').width() <= 460 ) {
      jQuery('.process-wrap.fusion-tabs li:nth-child(1) a').click(function() { 
      jQuery('.process-image').css({'background-position': 'center top'}); 
    }); 
  }
});

jQuery(window).resize(function(){     
    if (jQuery('#wrapper').width() <= 460 ) {
      jQuery('.process-wrap.fusion-tabs li:nth-child(2) a').click(function() { 
      jQuery('.process-image').css({'background-position': 'center -191px'}); 
    }); 
  }
});

jQuery(window).resize(function(){     
    if (jQuery('#wrapper').width() <= 460 ) {
      jQuery('.process-wrap.fusion-tabs li:nth-child(3) a').click(function() { 
      jQuery('.process-image').css({'background-position': 'center -364px'}); 
    }); 
  }
});

// Track phone number clicks as Google Analytics events
jQuery(document).ready(function(){
  jQuery('.phone-number-link').on('click', function() {
    ga('send', 'event', 'Click-to-Call', 'Click', 'Call Initiated');
  });
});

// Add class to last column on Virtual Meetings page
jQuery(document).ready(function(){
  jQuery('.vm-cat-box').last().addClass('last'); 
});

// Add classes to Logo Slider
jQuery(document).ready(function(){
  jQuery('.logo-slider li:nth-child(1)').addClass('item-1');
  jQuery('.logo-slider li:nth-child(2)').addClass('item-2');
  jQuery('.logo-slider li:nth-child(3)').addClass('item-3');
  jQuery('.logo-slider li:nth-child(4)').addClass('item-4');
  jQuery('.logo-slider li:nth-child(5)').addClass('item-5');
  jQuery('.logo-slider li:nth-child(6)').addClass('item-6');
});

// Fade in the Logo Slider
//jQuery(document).ready(function(){
//    jQuery('.logo-slider').fadeIn();
//});

// Fade in the Logo Slider
jQuery(document).ready(function(){
  jQuery('.logo-slider').addClass('hidden').fadeIn(7100).removeClass('hidden');
});

// Custom Tooltips
// More info here: http://osvaldas.info/elegant-css-and-jquery-tooltip-responsive-mobile-friendly
jQuery( function()
{
    var targets = jQuery( '[rel~=tooltip]' ),
        target  = false,
        tooltip = false,
        title   = false;
 
    targets.bind( 'mouseenter', function()
    {
        target  = jQuery( this );
        tip     = target.attr( 'title' );
        tooltip = jQuery( '<div id="customTooltip"></div>' );
 
        if( !tip || tip == '' )
            return false;
 
        target.removeAttr( 'title' );
        tooltip.css( 'opacity', 0 )
               .html( tip )
               .appendTo( 'body' );
 
        var init_tooltip = function()
        {
            if( jQuery( window ).width() < tooltip.outerWidth() * 1.5 )
                tooltip.css( 'max-width', jQuery( window ).width() / 2 );
            else
                tooltip.css( 'max-width', 400 );
 
            var pos_left = target.offset().left + ( target.outerWidth() / 2 ) - ( tooltip.outerWidth() / 2 ),
                pos_top  = target.offset().top + 95;
 
            if( pos_left < 0 )
            {
                pos_left = target.offset().left + target.outerWidth() / 2 - 20;
                tooltip.addClass( 'left' );
            }
            else
                tooltip.removeClass( 'left' );
 
            if( pos_left + tooltip.outerWidth() > jQuery( window ).width() )
            {
                pos_left = target.offset().left - tooltip.outerWidth() + target.outerWidth() / 2 + 20;
                tooltip.addClass( 'right' );
            }
            else
                tooltip.removeClass( 'right' );
 
            if( pos_top < 0 )
            {
                var pos_top  = target.offset().top + target.outerHeight();
                tooltip.addClass( 'top' );
            }
            else
                tooltip.removeClass( 'top' );
 
            tooltip.css( { left: pos_left, top: pos_top } )
                   .animate( { opacity: 1 }, 0 );
        };
 
        init_tooltip();
        jQuery( window ).resize( init_tooltip );
 
        var remove_tooltip = function()
        {
            tooltip.animate( { opacity: 0 }, 0, function()
            {
                jQuery( this ).remove();
            });
 
            target.attr( 'title', tip );
        };
 
        target.bind( 'mouseleave', remove_tooltip );
        tooltip.bind( 'click', remove_tooltip );
    });
});