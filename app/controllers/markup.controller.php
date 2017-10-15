<?php
/**
 * Контроллер правил разметки:
 */
class MarkupController extends BaseController
{
	/**
	 * Просмотр главной страницы раздела:
	 */
	public function indexAction(Application $application, Template $template)
	{
		$template -> setParameter('title', 'Правила разметки');

		return true;
	}
}
