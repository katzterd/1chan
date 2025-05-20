<?php
/**
 * Хелпер для шаблонов:
 */
class TemplateHelper
{
	/**
	 * Получение урла сайта:
	 */
	public static function getSiteUrl()
	{
		return $_SERVER["HTTP_HOST"];
	}

	/**
	 * Получение иконки:
	 */
	public static function getIcon($site)
	{
		$site = str_replace('www.', '', parse_url($site, PHP_URL_HOST));
		if (is_file(WEB_DIR .'/ico/favicons/'. $site .'.png'))
			return '/ico/favicons/'. $site .'.png';

		return 'https://proxy.duckduckgo.com/ip3/'. $site . '.ico';
	}

	/**
	 * Русская дата:
	 */
	public static function date($pattern, $time = false) {
	    // Не горжусь этим хаком:
	    if ($pattern == 'Y-m-d @ H:i:s') {
	        return date($pattern, $time ? $time : time());
	    }
	
		if (date('Y') == date('Y', $time))
			$pattern = str_replace(' Y', '', $pattern);

		$date = date($pattern, $time ? $time : time());
		return strtr($date, array(
			'Jan' => 'Января',
			'Feb' => 'Февраля',
			'Mar' => 'Марта',
			'Apr' => 'Апреля',
			'May' => 'Мая',
			'Jun' => 'Июня',
			'Jul' => 'Июля',
			'Aug' => 'Августа',
			'Sep' => 'Сентября',
			'Oct' => 'Октября',
			'Nov' => 'Ноября',
			'Dec' => 'Декабря'
		));
	}

	/**
	 * Русское окончание:
	 */
	public static function ending($chislo, $n1, $n2, $n5){
		$chislo = (int)$chislo;
		$ch = substr($chislo, -1);

		if ($ch==1)
		{
			if (strlen($chislo) > 1)
				$result = substr($chislo,-2,1) == 1 ? $n5 : $n1;
			else
				$result = $n1;
		}
		elseif($ch > 1 && $ch < 5)
		{
			if (strlen($chislo) > 1)
				$result = substr($chislo, -2, 1) == 1 ? $n5 : $n2;
			else
				$result = $n2;
		}
		else
		{
			$result=$n5;
		}
		return $chislo .' '. $result;
	}

	/**
	 * Форматирование размеров файлов:
	 */
	public static function format_bytes($bytes) {
	   if ($bytes < 1024) return $bytes.' B';
	   elseif ($bytes < 1048576) return round($bytes / 1024, 2).' KB';
	   elseif ($bytes < 1073741824) return round($bytes / 1048576, 2).' MB';
	   elseif ($bytes < 1099511627776) return round($bytes / 1073741824, 2).' GB';
	   else return round($bytes / 1099511627776, 2).' TB';
	}

	/**
	 * Получение информации о категории:
	 */
	public static function BlogCategory($id, $field = null)
	{
		static $categories;
		if (!is_array($categories))
		{
			foreach (Blog_BlogCategoryModel::GetCategories() as $row)
				$categories[$row['id']] = $row;
		}

		if (is_null($field))
			return $categories[$id];
		else
			return $categories[$id][$field];
	}

	/**
	 * Функция проверки обновления поста:
	 */
	public static function isPostUpdated(&$post, $comments_only = true)
	{
		if ($post['comments'] == 0 && $comments_only)
			return false;

		$session    = Session::getInstance();
		$last_visit = $session -> activeGet('last_visit');

		if (($last_visit_post = $session -> activeGet('last_visit_post_'. $post['id'], false)) !== false)
		{
			if ($post['updated_at'] > $last_visit_post)
				return true;
		}
		else
		{
			if ($post['updated_at'] > $last_visit)
				return true;
		}

		return false;
	}

	/**
	 * Функция проверки нового поста:
	 */
	public static function isNewComment(&$comment)
	{
		$session    = Session::getInstance();
		$last_visit = $session -> activeGet('last_visit');

		if (($last_visit_post = $session -> instantGet('last_visit_post_'. $comment['post_id'], false)) !== false)
		{
			if ($comment['created_at'] > $last_visit_post)
				return true;
		}
		else
		{
			if ($comment['created_at'] > $last_visit)
				return true;
		}

		return false;
	}

	/**
	 * Функция проверки обновлений в разделе "онлайн":
	 */
	public static function isLiveUpdated()
	{
		$session = Session::getInstance();
		$cache   = KVS::getInstance();
		$last_visit = $session -> persistenceGet('live_last_visit', time());
		return $cache -> get('Blog_BlogOnlineModel', null, 'lastUpdate') > $last_visit;
	}


	/**
	 * Верхняя панель
	 */

	private static $top_panel = null;

	public static function getTopPanel() {
		if (self::$top_panel == null) {
			$cache = KVS::getInstance();
			if ($cache -> exists('TopPanel', null, 'list')) {
				self::$top_panel = @unserialize($cache -> get('TopPanel', null, 'list')) ?? [];
			}
			else {
				$top_panel = [];
				$top_panel_json = @file_get_contents(VIEWS_DIR . '/top-panel.json');
				if ($top_panel_json) {
					$top_panel = @json_decode($top_panel_json, true) ?? [];
				}
				self::$top_panel = $top_panel;
				self::setTopPanel();
			}
		}
		return self::$top_panel;
	}

	public const SPIECIAL_LINKS = [
		[	"id" => "online",
			"name" => "Онлайн ссылки"	],
		[	"id" => "chat",
			"name" => "Анонимные чаты"	],
		[	"id" => "theme-switcher",
			"name" => "Переключатель тем"	],
		[	"id" => "force-o-meter",
			"name" => "Форсометр"	]
	];

	public const SPECIAL_LINK_LIST = ["online", "chat", "theme-switcher", "force-o-meter"];

	public static function getTopPanelPresentation() {
		$top_panel = self::getTopPanel();
		foreach ($top_panel as &$section) {
			foreach ($section as &$link) {
				if (!is_array($link)) { // special links
					if ($link == 'online') $link = [
						"href"  => '/live/',
						"text"  => 'Онлайн ссылки',
						"class" => 'b-top-panel_b-online-link' . (!self::isLiveUpdated() ? ' m-disactive' : '')
					];
					elseif ($link == 'chat') $link = [
						"href" => '/chat/',
						"text" => 'Анонимные чаты'
					];
					elseif ($link == 'theme-switcher') {
						$selected_theme = Session::getInstance() -> persistenceGet('global_theme', false);
						$html = "<form action='/service/theme/:set' id='color-theme-form' method='POST' style='display: contents'>
							Тема: <select id='color-theme-selector' name='theme'>
							<option value=':reset'>Системная</option>";
						foreach($GLOBALS['COLOR_THEMES'] as $i => $theme) {
							$name = $i==0 ? "Светлая" : ($i==1 ? "Тёмная" : ucfirst($theme));
							$html .= "<option value='$theme'"
								. ($theme == $selected_theme ? ' selected' : '')
								. ">$name</option>";
						}
						$html .= '</select><noscript><input value="" type="submit"></noscript></form>';
						$link = ["html" => $html];
					}
					elseif ($link == 'force-o-meter') $link = [
						"href" => '/service/force-o-meter/',
						"text" => 'Форсометр'
					];
					else $link = false;
				}
			}
			$section = array_filter($section, function($link) { return $link; });
		}
		return $top_panel;
	}

	public static function setTopPanel($model=null) {
		if (is_null($model)) $model = self::$top_panel;
		$cache = KVS::getInstance();
		$cache -> set('TopPanel', null, 'list', serialize($model));
	}

	private static $favicon = null;

	public static function getFavicon() {
		if (self::$favicon == null) {
			$cache = KVS::getInstance();
			if ($cache -> exists(__CLASS__, null, 'favicon')) {
				self::$favicon = @($cache -> get(__CLASS__, null, 'favicon')) ?? "";
			}
			else {
				$favicon = '<link rel="icon" type="image/png" href="/ico/favicon.png" />';
				self::$favicon = $favicon;
				self::saveFavicon($favicon);
			}
		}
		return self::$favicon;
	}

	public static function saveFavicon($value) {
		$cache = KVS::getInstance();
		$cache -> set(__CLASS__, null, 'favicon', $value);
	}

	private static $side_panel_left  = null;
	private static $side_panel_right = null;

	public static function getSidePanels() {
		$cache = KVS::getInstance();
		foreach (['left', 'right'] as $pos) {
			if (self::${'side_panel_'.$pos} == null) {
				self::${'side_panel_'.$pos} = @($cache -> get(__CLASS__, 'side-panel', $pos)) ?? "";
			}
		}
		return [self::$side_panel_left, self::$side_panel_right];
	}

	public static function setSidePanel($pos, $value) {
		$cache = KVS::getInstance();
		$cache -> set(__CLASS__, 'side-panel', $pos, $value);
	}
}
