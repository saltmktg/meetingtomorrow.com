jQuery(document).ready(function(){

  jQuery('.accordion-expand').click(function() {
    jQuery('.accordion-content').removeClass('visually-hidden');
  });

  jQuery('.accordion-collapse').click(function() {
    jQuery('.accordion-content').addClass('visually-hidden');
  });

  jQuery('.accordion-header.accordion-header-expandable').click(function() {
    jQuery(this).next().toggleClass('visually-hidden');
  });

});