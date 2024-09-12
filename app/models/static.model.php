<?php
/**
 * Модель статических страниц:
 */
class StaticModel
{
	/**
	 * Проверка существования страницы:
	 */
	public static function isExists($page)
	{
		$cache = KVS::getInstance();
		$page  = trim($page, '/');

		$pages = (array)unserialize($cache -> get(__CLASS__, null, 'page_list'));
		return array_key_exists($page, $pages);
	}

	/**
	 * Получение списка всех страниц:
	 */
	public static function GetPages()
	{
		$cache = KVS::getInstance();
		return unserialize($cache -> get(__CLASS__, null, 'page_list'));
	}

	/**
	 * Получение страницы:
	 */
	public static function GetPage($page)
	{
		$cache = KVS::getInstance();
		$page  = trim($page, '/');

		$pages = (array)unserialize($cache -> get(__CLASS__, null, 'page_list'));
		if (array_key_exists($page, $pages))
		{
			return $pages[$page];
		}

		return false;
	}

	/**
	 * Создание (редактирование) страницы:
	 */
	public static function SetPage($page, $title, $content, $published)
	{
		$cache = KVS::getInstance();
		$page  = trim($page, '/');
		$pages = (array)unserialize($cache -> get(__CLASS__, null, 'page_list'));


		if (array_key_exists($page, $pages))
			$name = $pages[$page]['name'];
		else
			$name = uniqid(time());

		if (file_put_contents(VIEWS_DIR .'/static/'. $name .'.php', $content))
		{
			$pages[$page]['title']     = $title;
			$pages[$page]['name']      = $name;
			$pages[$page]['published'] = $published;
		}

		$cache -> set(__CLASS__, null, 'page_list', serialize($pages));
	}

	/**
	 * Удаление страницы:
	 */
	public static function RemovePage($page)
	{
		$cache = KVS::getInstance();
		$page  = trim($page, '/');
		$pages = (array)unserialize($cache -> get(__CLASS__, null, 'page_list'));

		if (array_key_exists($page, $pages))
		{
			@unlink(VIEWS_DIR .'/static/'. $pages[$page]['name'] .'.php');
			unset($pages[$page]);

		}

		$cache -> set(__CLASS__, null, 'page_list', serialize($pages));
		return true;
	}

	/**
	 * Получение списка файлов:
	 */
	public static function GetFiles()
	{
		$cache = KVS::getInstance();
		return unserialize($cache -> get(__CLASS__, null, 'file_list'));
	}

	/**
	 * Добавление файла
	 */
	public static function CreateFile($file, $dir="/uploads/", $name=null) {
		if (!is_uploaded_file($file['tmp_name'])) return false;

		$cache = KVS::getInstance();
		$files_record = unserialize($cache -> get(__CLASS__, null, 'file_list'));
		$files = $files_record ? (array)$files_record : array();

		if ($name) $file['name'] = $name;
		$upload_result = move_uploaded_file($file['tmp_name'], WEB_DIR . $dir . $file['name'] );
		if (!$upload_result) return false;
		
		array_unshift($files, $file);
		$cache -> set(__CLASS__, null, 'file_list', serialize($files));
		
		return true;
	}

	/**
	 * Удаление файла
	 */
	public static function RemoveFile($name)
	{
		$cache = KVS::getInstance();
		$files = (array)unserialize($cache -> get(__CLASS__, null, 'file_list'));

		foreach($files as $k => $file) {
			if ($file['name'] == $name)
				unset($files[$k]);
		}
		@unlink(WEB_DIR .'/uploads/'. $file['name']);

		$cache -> set(__CLASS__, null, 'file_list', serialize($files));
	}
}
