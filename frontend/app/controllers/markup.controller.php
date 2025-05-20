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
		$smilies = '';
		foreach (TexyHelper::getSmilies() as $smile) {
			$smilies.= ':'.$smile['name'].': ';
		}
		$template -> setParameter('title', 'Правила разметки');
		$template -> setParameter('smilies', $smilies);

		return true;
	}
}
