<?php
/**
 * Хелпер по домашней борде пользователя:
 */
class HomeBoardHelper {
	static private $boards = null;

	static public function getBoards() {
		if (self::$boards == null) {
			$cache = KVS::getInstance();
			if ($cache -> exists('Homeboards', null, 'list')) {
				self::$boards = @unserialize($cache -> get('Homeboards', null, 'list')) ?? [];
			}
			else {
				$boards = [];
				$boards_json = @file_get_contents(WEB_DIR . '/ico/homeboards/homeboards.json');
				if ($boards_json) {
					$boards = @json_decode($boards_json, true) ?? [];
				}
				else {
					$boards_json = @file_get_contents(WEB_DIR . '/ico/homeboards/homeboards.example.json');
					if ($boards_json) {
						$boards = @json_decode($boards_json, true) ?? [];
					}
				}
				self::$boards = $boards;
				self::saveList(true);
			}
		}

		return self::$boards;
	}

	static public function existsBoard($id) {
		return array_key_exists($id, self::getBoards());
	}

	static public function getBoard($id) {
		if (self::existsBoard($id))
			return self::$boards[$id];
		return self::$boards['anonymous'];
	}

	static public function listFiles() {
		$files = scandir(WEB_DIR . '/ico/homeboards');
		return array_filter($files, function($f) {
			return preg_match('/(.+)\.(ico|gif|jpe?g|png|webp)/i', $f);
		});
	}

	static public function saveBoard($old_domain, $new_domain, $icon, $name) {
		if ($old_domain != $new_domain) {
			self::deleteBoard($old_domain, true);
		}
		self::$boards[$new_domain] = [$icon, $name];
		self::saveList();
	}

	static public function deleteBoard($domain, $no_save=false) {
		unset(self::$boards[$domain]);
		if (! $no_save)
			self::saveList();
	}

	static private function saveList($only_kvs = false) {
		$cache = KVS::getInstance();
		$cache -> set('Homeboards', null, 'list', serialize(self::$boards));
		if (! $only_kvs)
			file_put_contents(WEB_DIR . '/ico/homeboards/homeboards.json', json_encode(self::$boards));
	}
}
