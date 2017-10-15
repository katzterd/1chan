<?php
/**
 * Хелпер по домашней борде пользователя:
 */
class HomeBoardHelper {
	static private $boards = array(
		'anonymous'    => array('anonymous.png', 'Аноним'),
	);

	static public function getBoards() {
		return self::$boards;
	}

	static public function existsBoard($id) {
		return array_key_exists($id, self::$boards);
	}

	static public function getBoard($id) {
		if (self::existsBoard($id))
			return self::$boards[$id];
		return self::$boards['anonymous'];
	}
}
