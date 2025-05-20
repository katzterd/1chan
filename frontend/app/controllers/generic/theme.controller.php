<?php
/**
 * Контроллер смены тем оформления сайта:
 */
class Generic_ThemeController
{
	/**
	 * Смена темы:
	 */
	public function switchAction(Application $application, Template $template)
	{
		$theme = @$_GET['theme'];
		if ($theme == ':set') {
			$theme = @$_POST['theme'];
		}
		self::switchTheme($theme);

		$template -> headerSeeOther(@$_SERVER['HTTP_REFERER'] ?? '/');
		return false;
	}

	private static function switchTheme($theme) {
		$session = Session::getInstance();

		if ($theme == ":next") {
			$theme = $GLOBALS['COLOR_THEMES'][0]; // fallback
			$current = $session -> persistenceGet('global_theme');
			$pos = array_search($current, $GLOBALS['COLOR_THEMES']);
			if ($pos !== false) {
				if ($pos >= count($GLOBALS['COLOR_THEMES']))
					$pos = 0;
				$theme = $GLOBALS['COLOR_THEMES'][$pos];
			}
		}
		elseif (is_null($theme) || $theme==":reset" || !in_array($theme, $GLOBALS['COLOR_THEMES']))
			$theme = false;
		$session -> persistenceSet('global_theme', $theme);
		return $theme;
	}

	public function switchAjaxAction(Application $application) {
		return self::switchTheme(@$_GET['theme']);
	}
}
