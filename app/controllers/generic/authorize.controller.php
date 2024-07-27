<?php
/**
 * Контроллер авторизации:
 */
class Generic_AuthorizeController extends BaseController
{
	/**
	 * Метод авторизации:
	 */
	public function authorizeSimpleFuckingAction(Application $application)
	{
		$session = Session::getInstance();
		$authorized = ($_SERVER['REQUEST_METHOD'] == 'POST' && $session -> isModerator($_POST['name'], $_POST['key']));
		header('Location: /admin', true, 302);
	}
}