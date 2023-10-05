<?php
/**
 * @package CG Parallax Module
 * @version 3.0.0 
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2023 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
 */
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use ConseilGouz\Module\CGParallax\Site\Helper\CgparallaxHelper;

$document 		= Factory::getDocument();
$modulefield	= ''.URI::base(true).'/modules/mod_cg_parallax/';

HTMLHelper::_('jquery.framework');

$document->addStyleSheet($modulefield.'css/parallax.css'); 
$document->addStyleSheet($modulefield.'css/vegas.min.css'); 
$document->addScript($modulefield .'js/parallax.js');
$document->addScript($modulefield .'js/color_anim.js');
$document->addScript($modulefield .'js/vegas.min.js');
$document->addStyleDeclaration($params->get('css','')); 

$parallax =  CgparallaxHelper::getParallax($params);

$document->addScriptOptions('mod_cg_parallax', 
	array('navbar_bg' => $params->get('navbar_bg','lightgrey'),'navbar_color' => $params->get('navbar_color', 'black')
		  ,'menu' => $params->get('menu','true'),'sticky' => $params->get('sticky','true')
		  ,'magic' => $params->get('sticky','false')
		  ,'magic_active' => $params->get('magic_active', 'blue')
		  ));

require_once(ModuleHelper::getLayoutPath($module->module));
?>