<?php
/**
 * @package CG Parallax Module
 * @version 3.0.0 
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2023 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
 */
namespace ConseilGouz\Module\CGParallax\Site\Helper;
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Version;
use Joomla\CMS\Language\Text;
use Joomla\Component\Content\Site\Model\ArticleModel;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Registry\Registry;
use Joomla\Component\Content\Site\Helper\RouteHelper;

class CgparallaxHelper
{
	static function getParallax($params)
	{   // apply contents plugins
		PluginHelper::importPlugin('content');
		// create parallax layout
		$result = "<style>".$params->get('css_gen','')."</style>"; // custom module css
		$result .= "<div class='cg_parallax' id='cg_parallax'>";	
		$ix = 1;
		$sectionsList = $params->get('sectionsList');
		foreach ($sectionsList as $item) {
			if ($item->sf_type == 'menu') {
				continue;
			}
			$title = $item->title;
			$title_alias = OutputFilter::stringURLSafe($title);
			$imgname = $item->image;
			$result .= "<style>".$item->css." .vegas-slide-inner {background-attachment: fixed;} .cg_bg_img_".$ix." .vegas-wrapper{background: rgba(256,256,256,0.".$item->lighten.");}</style>";
			$result .= "<a id='".$title_alias."' class='cg_anchor'></a><div class='cg_bg_section cg_bg_img_".$ix."'><h2>".$title."</h2>";
			if ($item->sf_type == "content") { // one article selected
				$article = self::getArticle($item->article,$params);
				$article = $article[0];
				// apply contents plugins
				$item_tmp = new \stdClass;
				if ($item->intro_full == "full") {
					$item_tmp->text = $article->fulltext;
				} elseif ($item->intro_full == "introfull") {
					$item_tmp->text = $article->introtext.$article->fulltext;
				} else 	{ // intro
					$item_tmp->text = $article->introtext;
					if ($item->readmore == "true") {
						$readmore = '<p class="cg-readmore"><a class="cg-readmore-title btn btn-primary" href="'.$article->link.'">'.Text::_('CG_LIB_READMORE').'</a></p>';			
						$item_tmp->text .= $readmore;
					}
				}
				$item_tmp->params = $params;
				Factory::getApplication()->triggerEvent('onContentPrepare', array ('com_content.article',$item_tmp, $params,0)  );
				$result .= $item_tmp->text;
			} else { // free text
				$article = $item->text;
				// apply contents plugins
				$item_tmp = new \stdClass;
				$item_tmp->text = $article;
				$item_tmp->params = $params;
				Factory::getApplication()->triggerEvent('onContentPrepare', array ('com_content.article', $item_tmp, $item_tmp->params, 0));
				$result .=  $item_tmp->text;
			}
			// "background-image: linear-gradient(rgba(255, 255, 255, 0.".$item->lighten."), rgba(250, 250, 250, 0.".$item->lighten."))"
			$images ="";
			$animation ="random";
			$trans= "fade";
			$duration = "1000";
			$delay = "12000";
			if (!isset($item->cg_img_type))  {
				$item->cg_img_type = "one";
				$item->cg_anim = "false";
			}
			if ($item->cg_anim == "false") {
				$animation = "none";
			} else { 
				$vegas = $item->vegas_configJ4;
				if ($vegas) {
				    $trans = $vegas->vegas_trans;
				    $animation = $vegas->vegas_anim;
				    $delay = $vegas->vegas_delay;
				    $duration = $vegas->vegas_duration;
				}
			}
			if ($item->cg_img_type == "one") {
				if ($item->cg_anim != "false")  {// on double juste pour garder l'animation
					$images = 	"[{src: '".URI::root()."/".$item->image."'},{src: '".URI::root()."/".$item->image."'}]";
				} else { // pas d'animation
					$images = 	"[{src: '".URI::root()."/".$item->image."'}]";
				}
			} elseif ($item->cg_img_type == "files"){
			    $images = "[";
		        $unearray = [];
		        foreach ($item->slideslistJ4 as $uneimage) {
		            $unearray[] = $uneimage->file_name;
		        }
				foreach ($unearray as $uneimage) {
					if (strlen(trim($uneimage)) == 0) continue;
					if (strlen($images) > 1) $images .= ",";
					$images .= 	"{src: '".URI::root()."/".$uneimage."'}";
				}
				$images .= "]";
			}
			if ($images) {
			     $lighten = (10 - $item->lighten) / 10;
			     $result .= "<style>.cg_bg_img_".$ix." .vegas-slide-inner {filter:opacity(".$lighten.")}";
			     if ($animation == "none") {
					$result .= '.cg_bg_img_'.$ix.' .vegas-slide,.cg_bg_img_'.$ix.' .vegas-slide-inner {will-change: opacity;transform:none}';
			     }	
			     $result .= "</style>";
			     $result .= "<script>jQuery('.cg_bg_img_".$ix."').vegas({slides:".$images.",delay:".$delay.",transitionDuration: ".$duration.",cover:true, transition:'".$trans."',animation: '".$animation."',loop: true});</script>";
			}
			$result .="</div>";
			$ix +=1;
		}
		return $result."</div>";
	}
	static function getArticle($id, $params) {
		// Get an instance of the generic articles model
	    $model     = new ArticleModel(array('ignore_request' => true));
        if ($model) {
		// Set application parameters in model
		$app       = Factory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set the filters based on the module params
		$model->setState('list.start', 0);
		$model->setState('list.limit', 1);
		$model->setState('filter.published', 1);
		$model->setState('filter.featured', $params->get('show_front', 1) == 1 ? 'show' : 'hide');

		// Access filter
		$access = ComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = Access::getAuthorisedViewLevels(Factory::getUser()->get('id'));
		$model->setState('filter.access', $access);

		// Category filter
		$model->setState('filter.category_id', $params->get('catid', array()));

		// Date filter
		$date_filtering = $params->get('date_filtering', 'off');

		if ($date_filtering !== 'off')
		{
			$model->setState('filter.date_filtering', $date_filtering);
			$model->setState('filter.date_field', $params->get('date_field', 'a.created'));
			$model->setState('filter.start_date_range', $params->get('start_date_range', '1000-01-01 00:00:00'));
			$model->setState('filter.end_date_range', $params->get('end_date_range', '9999-12-31 23:59:59'));
			$model->setState('filter.relative_date', $params->get('relative_date', 30));
		}
		// Filter by language
		$model->setState('filter.language', $app->getLanguageFilter());
		// Ordering
		$model->setState('list.ordering', 'a.hits');
		$model->setState('list.direction', 'DESC');

		$item = $model->getItem($id);

		$item->slug    = $item->id . ':' . $item->alias;
		$item->catslug = $item->catid . ':' . $item->category_alias;
		if ($access || in_array($item->access, $authorised))
		{
			// We know that user has the privilege to view the article
		    $item->link = Route::_(RouteHelper::getArticleRoute($item->slug, $item->catid, $item->language));
		}
		else
		{
			$item->link = Route::_('index.php?option=com_users&view=login');
		}
		$arr[0] = $item;
        }
        else {
        	$arr = false;
        }
		return $arr;
	}
	public static function getAjax() {
		$module = ModuleHelper::getModule('cg_parallax');
		$params = new Registry($module->params);  		
        $input = Factory::getApplication()->input;
		if ($input->get('data') == "param") {
			return '{"name":"'.$module->name.'","navbar_bg":"'.$params->get('navbar_bg','lightgrey').'","navbar_color":"'.$params->get('navbar_color', 'black').'","menu":"'.$params->get('menu','true').'","sticky":"'.$params->get('sticky','true').'","magic":"'.$params->get('magic','false').'","magic_active":"'.$params->get('magic_active','blue').'"}';
		}
		return false;
	}
}
