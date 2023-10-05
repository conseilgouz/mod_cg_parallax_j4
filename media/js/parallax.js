/**
 * @package CG Parallax Module
 * @version 2.0.3
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2021 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
 */
var options;
var $curr_menu;
// from http://www.design-fluide.com/17-11-2013/un-defilement-anime-smooth-scroll-en-jquery-sans-plugin/
jQuery(document).ready(function($) {
	if (typeof Joomla === 'undefined' || typeof Joomla.getOptions === 'undefined') {
		console.error('Joomla.getOptions not found!\nThe Joomla core.js file is not being loaded.');
	}
	options = Joomla.getOptions('mod_cg_parallax');
	if (typeof options === 'undefined' ) { // cache Joomla problem
		request = {
			'option' : 'com_ajax',
			'module' : 'cg_parallax',
			'data'   : 'param',
			'format' : 'raw'
			};
			jQuery.ajax({
				type   : 'POST',
				data   : request,
				success: function (response) {
					options = JSON.parse(response);
					go_parallax(options);
					return true;
				}
			});
		return false;
	};
	go_parallax(options);
});
function go_parallax(options) {
	jQuery('#cg_navbar').css("background-color",options.navbar_bg);
	jQuery('#cg_navbar a').css("color",options.navbar_color);
	if (options.magic == "true") {
		jQuery("#cg_navbar .active a").css("color",options.magic_active);
	}

	jQuery('.cg_bg_btn').on('click', function() { 
		var page = jQuery(this).attr('href'); 
		var speed = 600;
		jQuery('html, body').animate( { scrollTop: jQuery(page).offset().top }, speed ); 
		jQuery('#cg_navbar').removeClass("responsive"); 
		return false;
	});
	jQuery('.cg_para_icon').on('click', function() { 
		jQuery('#cg_navbar').addClass("responsive"); 
		return false;
	});

	if ((options.menu == 'true') && (options.sticky == 'true')) {  
		jQuery.fn.isInViewport = function() { // 1.0.8 : Following scroll menu
			var elementTop = jQuery(this).offset().top;
			var elementBottom = elementTop + jQuery(this).outerHeight();
			var viewportTop = jQuery(window).scrollTop();
			var viewportBottom = viewportTop + jQuery(window).height();
			return elementBottom > viewportTop && elementTop < viewportBottom;
		};		
		window.onscroll = function() {fn_scroll()};
		function fn_scroll() {
			fn_sticky();
			fn_anchor();
		}
		var navbar = document.getElementById("cg_navbar");
		var sticky = navbar.offsetTop+60;
		function fn_sticky() {
			if (window.pageYOffset >= sticky) {
				navbar.classList.add("cg_sticky")
			} else {
				navbar.classList.remove("cg_sticky");
				jQuery('#cg_navbar').removeClass("responsive"); 
			}
		}
		function fn_anchor() { // 1.0.8 : Following scroll menu
			jQuery('.cg_anchor').each(function () {
				var $this = jQuery(this);
				if (jQuery($this).isInViewport()){
					var $id = $this.attr('id');
					var $mymenu = jQuery("a[href='#"+$id+"']");
					if (!$curr_menu) $curr_menu = $mymenu.attr("href");
					if ($curr_menu == $mymenu.attr("href")) return false; 
					$curr_menu = $mymenu.attr("href");
					$el = jQuery($mymenu[0].parentNode);
					jQuery('#cg_magic li').removeClass('active');	
					jQuery('#cg_magic li a').css('color',options.navbar_color);	
					$el.addClass('active'); 
					jQuery($mymenu[0]).css("color",options.magic_active);
					leftPos = $mymenu.position().left;
					topPos = $mymenu.position().top;
					newWidth = $mymenu.outerWidth();
					var $magicLine = jQuery("#cg_magic_line");	
					$height = jQuery(".active a").outerHeight(true);
					if ($height == 0) $height = '3em'; // hidden element: assume 3em
					$magicLine
						.width(jQuery(".active").width())
						.height($height)
						.css("left", jQuery(".active a").position().left);
					$magicLine.stop().animate({
						left: leftPos,
						top: topPos,
						width: newWidth,
						backgroundColor: $mymenu.attr("rel")
					})
					return false;
				}
			})
			
		}
	}
}
/* Magic button */
jQuery(document).ready(function($) {
    var $el, leftPos, newWidth;
    $nav = jQuery("#cg_magic");
    $nav.append("<li id='cg_magic_line'></li>");
    var $magicLine = jQuery("#cg_magic_line");
	$height = jQuery(".active a").outerHeight(true);
	if ($height == 0) $height = '3em'; // hidden element: assume 3em
    $magicLine
        .width(jQuery(".active").width())
		.height($height)
        .css("left", jQuery(".active a").position().left);
    jQuery("#cg_magic a").hover(function() {
        $el = jQuery(this);
        leftPos = $el.position().left;
        topPos = $el.position().top;
        newWidth = $el.outerWidth();
        $magicLine.stop().animate({
            left: leftPos,
			top: topPos,
            width: newWidth,
            backgroundColor: $el.attr("rel")
        })
    });
    jQuery("#cg_magic a").on('click', function() { 
		$el = jQuery(this.parentNode);
		jQuery('#cg_magic li').removeClass('active');	
		jQuery('#cg_magic li a').css('color',options.navbar_color);	
		$el.addClass('active'); 
		jQuery(this).css("color",options.magic_active);
		$curr_menu = $el.attr('href');
	});
    jQuery(".active a").mouseenter();
});
