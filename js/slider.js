	jQuery(document).ready(function($) {
	  if(typeof slider_speed === "undefined"){
	  	slider_speed = 3;
	  }

	  $('.bxslider').bxSlider({  mode: 'horizontal',
        slideMargin: 10,
        mode:'fade',
        auto:true,
        pause:slider_speed*1000,
        speed:500,
        tickerHover:true,
        adaptiveHeight:true,
        pager:false,
        autoDelay:2000,
        controls:false});
        $('.bx-pager-item').hide();
	});
