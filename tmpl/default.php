<?php
/**
 * @package CG Parallax Module
 * @version 2.0.3
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2021 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
 * 
 */
defined('_JEXEC') or die('Restricted access'); 

$buttons = "";
if ($params->get('menu','true') == 'true') {
// create buttons
	$sectionsList = $params->get('sectionsList');
	$magic = $params->get('magic','false');
	$buttons = "<div id='cg_navbar'>";
	if ($magic == "true") {
		$buttons .=  "<ul id='cg_magic'>";
	} else {
		$buttons .=  "<ul>";
	}
	$first = true;
	foreach ($sectionsList as $item) {
		$title = $item->title;
		$title_alias = JFilterOutput::stringURLSafe($title);
		$class = "";
		$rel = "";
		if ($magic == "true") {
			$rel = "rel='".$item->magic_bg."'";
		}
		if ($first) $class="class='active'"; 
		if ($item->sf_type == "menu") {
			$menualias = JFactory::getApplication()->getMenu()->getItem($item->menu)->alias;
			$buttons .= "<li ".$class."><a ".$rel." class='cg_bg_btn' href='".$menualias."'>".$title."</a></li>";
		} else {
			$buttons .= "<li ".$class."><a ".$rel." class='cg_bg_btn' href='#".$title_alias."'>".$title."</a></li>";
		}
		$first = false;
	}
	$buttons .= "</ul>";
	$buttons .= "<a href='' class='cg_para_icon'>&#9776;</a></div>";
}
echo $buttons.$parallax;
?>
