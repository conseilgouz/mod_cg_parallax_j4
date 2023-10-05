<?php
/**
 * @package CG Parallax Module for Joomla!4.x/5.x
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
$modulefield	= 'media/mod_cg_parallax/';
HTMLHelper::_('jquery.framework');
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->addInlineStyle($params->get('css_gen',''));
$wa->registerAndUseStyle('parallax',$modulefield.'css/parallax.css');
$wa->registerAndUseStyle('vegas',$modulefield.'css/vegas.min.css');
$wa->registerAndUseScript('coloranim', $modulefield.'/js/color_anim.js');
$wa->registerAndUseScript('vegas', $modulefield.'/js/vegas.min.js');
if ((bool)Factory::getConfig()->get('debug')) { // Mode debug
	$document->addScript(''.URI::base(true).'/media/mod_cg_parallax/js/parallax.js'); 
} else {
	$wa->registerAndUseScript('cgpopup', $modulefield.'/js/parallax.js');
}
$parallax =  CgparallaxHelper::getParallax($params);
$document->addScriptOptions('mod_cg_parallax', 
	array('navbar_bg' => $params->get('navbar_bg','lightgrey'),'navbar_color' => $params->get('navbar_color', 'black')
		  ,'menu' => $params->get('menu','true'),'sticky' => $params->get('sticky','true')
		  ,'magic' => $params->get('sticky','false')
		  ,'magic_active' => $params->get('magic_active', 'blue')
	));
require_once(ModuleHelper::getLayoutPath($module->module));
?>