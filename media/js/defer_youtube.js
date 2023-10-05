/**
 * @package CG Parallax Module
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2021 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
 */
function init_you() { 
	var vidDefer = document.getElementsByTagName('iframe'); 
	for (var i=0; i<vidDefer.length; i++) { 
		if(vidDefer[i].getAttribute('yousrc')) { 
			vidDefer[i].setAttribute('src',vidDefer[i].getAttribute('yousrc')); 
		} 
	} 
} 
window.onload = init_you;
