<?php
/**
 * Обработчик ошибки 401:
 */
class Errors_Error401Controller extends BaseController
{
	public function indexAction(Application $application, Template $template)
	{
		$template -> setParameter('title', 'Требуется авторизация');
		return true;
	}
}