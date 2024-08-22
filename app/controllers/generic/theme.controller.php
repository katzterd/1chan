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
		self::switchTheme(@$_GET['theme']);

		$template -> headerSeeOther('/');
		return false;
	}

	private static function switchTheme($theme) {
		$session = Session::getInstance();

		if (is_null($theme) || !in_array($theme, $GLOBALS['COLOR_THEMES']))
			$theme = false;
		$session -> persistenceSet('global_theme', $theme);
		return $theme;
	}

	public function switchAjaxAction(Application $application) {
		return self::switchTheme(@$_GET['theme']);
	}
}
