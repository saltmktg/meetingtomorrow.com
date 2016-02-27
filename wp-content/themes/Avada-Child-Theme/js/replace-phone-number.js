// Replace Meeting Tomorrow phone number with local city phone number
jQuery(document).ready(function(){
  var cityInfo = jQuery('.page .city-page-banner span.txt-semibold.orange').text();
  var cityInfoSplit = cityInfo.split('|');
  var cityNumber = '<div class="header-phone-number-replaced">' + cityInfoSplit[0]; + '</div>'

  // Replace the phone number if 'cityInfo' variable exists (i.e. if there's a city phone number)
  if (cityInfo.length > 0) {
    jQuery('.header-phone-number').replaceWith(cityNumber);
  }  
});

// Replace Meeting Tomorrow phone number with local city phone number
jQuery(document).ready(function(){
  var cityProjectorInfo = jQuery('.page .city-page-banner span.blue').text();
  var cityProjectorInfoSplit = cityProjectorInfo.split('|');
  var cityNumber = '<div class="header-phone-number-replaced">' + cityProjectorInfoSplit[0]; + '</div>'

  // Replace the phone number if 'cityInfo' variable exists (i.e. if there's a city phone number)
  if (cityProjectorInfo.length > 0) {
    jQuery('.header-phone-number').replaceWith(cityNumber);
  }  
});