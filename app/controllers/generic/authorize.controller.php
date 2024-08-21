<?php
/**
 * Контроллер авторизации:
 */
class Generic_AuthorizeController extends BaseController
{
	/**
	 * Метод авторизации через форму входа
	 */
	public function authorizeSimpleFuckingAction() {
		Session::getInstance() -> authorize($_POST['name'], $_POST['key']);
		header('Location: /admin', true, 302);
	}

	/**
	 * Метод авторизации через AJAX
	 */
	public function authorizeAjaxAction() {
		return ["auth" => Session::getInstance() -> checkSessionClass()];
	}
}