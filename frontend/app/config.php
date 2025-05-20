<?php
$board_dirs = Board_BoardModel::getSimpleBoardList();
if ($board_dirs) {
	$board_dirs = implode('|', $board_dirs);
	$board_routes = [
		"/($board_dirs)" => array(
			'board',
			'controller' => 'board',
			'action' => 'viewThreads'
		),
		"/($board_dirs)/(\d+)" => array(
			'board', 'page',
			'controller' => 'board',
			'action' => 'viewThreads'
		),
		"/($board_dirs)/res/(\d+)" => array(
			'board', 'thread_id',
			'controller' => 'board',
			'action' => 'viewThread'
		),
		"/($board_dirs)/res/(\d+)/stats" => array(
			'board', 'thread_id',
			'controller' => 'board',
			'action' => 'postStats'
		),
		"/($board_dirs)/(create|createAjaxForm|createPost|createPostAjaxForm|get|remove|changeTitle)" => array(
			'board', 'action',
			'controller' => 'board'
		),
		"/service/subscribeBoard/($board_dirs)" => array(
			'board',
			'controller' => 'board',
			'action' => 'subscribeBoard'
		),
		"/service/unsubscribeBoard/($board_dirs)" => array(
			'board',
			'controller' => 'board',
			'action' => 'unsubscribeBoard'
		),
		"/service/notifyCheck/($board_dirs)/(\d+)" => array(
			'board', 'id',
			'controller' => 'board',
			'action' => 'notifyCheck'
		),
		"/fav/toggle/($board_dirs)/(\d+)" => array(
			'board', 'id',
			'controller' => 'board',
			'action' => 'toggleFavorite'
		)
	];
}
else $board_routes = [];

$poo_route = ENABLE_POO=="true" ? [
	'/service/poo-chan' => array(
		'controller' => 'static',
		'action' => 'poo'
	)
] : [];

return array(
	'database' => array(
		'engine' => 'mysql',
		'host'   => MARIADB_HOST,
		'port'   => MARIADB_PORT,
		'name'   => MARIADB_DATABASE,
		'user'   => MARIADB_USER,
		'pass'   => MARIADB_PASSWORD
	),
	'md5salt'=> MD5_SALT,
	'routes' => array_merge($board_routes, $poo_route, array(
		'/admin' => array(
			'controller' => 'admin',
			'action' => 'posts'
		),
		'/admin/?([^/]+)?' => array(
			'action',
			'controller' => 'admin'
		),
		'/auth' => array(
			'controller' => 'generic_authorize',
			'action' => 'authorize'
		),
		'/captcha' => array(
			'controller' => 'generic_captcha',
			'action' => 'index'
		),
		'/' => array(
		    'url' => '/news/',
		    'controller' => 'generic_redirect',
		    'action' => 'redirect'
		),
		'/b/all' => array(
		    'url' => '/news/all/',
		    'controller' => 'generic_redirect',
		    'action' => 'redirect'
		),
		'/service/notifyGet' => array(
			'controller' => 'board',
			'action' => 'notifyGet'
		),
		'/service/last_board_posts' => array(
			'controller' => 'board',
			'action'     => 'lastBoardPosts'
		),
		'/service/last_board_posts/(\d+)' => array(
			'page',
			'controller' => 'board',
			'action'     => 'lastBoardPosts'
		),
        '/service/force\-o\-meter' => array(
            'controller' => 'generic_forceometer',
            'action'     => 'index'
        ),
		'/fav' => array(
			'controller' => 'board',
			'action' => 'viewFavorites'
		),
		'/help/markup'  => array(
			'controller' => 'markup',
			'action' => 'index'
		),
		'/news' => array(
			'controller' => 'blog',
			'action' => 'viewApproved'
		),
		'/help/news' => array(
			'controller' => 'blog',
			'action' => 'viewRules'
		),
		'/news/(\d+)' => array(
			'page',
			'controller' => 'blog',
			'action' => 'viewApproved'
		),
		'/news/all' => array(
			'controller' => 'blog',
			'action' => 'viewAll'
		),
		'/news/all/reset' => array(
			'controller' => 'blog',
			'action' => 'markAsRead'
		),
		'/news/all/new' => array(
			'controller' => 'blog',
			'action' => 'viewNewPost'
		),
		'/news/all/(\d+)' => array(
			'page',
			'controller' => 'blog',
			'action' => 'viewAll'
		),
		'/news/hidden' => array(
			'controller' => 'blog',
			'action' => 'viewHidden'
		),
		'/news/hidden/(\d+)' => array(
			'page',
			'controller' => 'blog',
			'action' => 'viewHidden'
		),
		'/news/cat' => array(
			'controller' => 'blog',
			'action' => 'viewCategories'
		),
		'/news/cat/([^/]+)' => array(
			'category',
			'controller' => 'blog',
			'action' => 'viewCategory'
		),
		'/news/cat/([^/]+)/(\d+)' => array(
			'category', 'page',
			'controller' => 'blog',
			'action' => 'viewCategory'
		),
		'/news/cat/([^/]+)/rss.xml' => array(
			'category',
			'controller' => 'blog',
			'action' => 'viewCategoryRss'
		),
		'/news/sort/created_at/' => array(
			'controller' => 'blog',
			'action'   => 'sort',
			'sortby' => 'created_at'
		),
		'/news/sort/updated_at/' => array(
			'controller' => 'blog',
			'action'   => 'sort',
			'sortby' => 'updated_at'
		),
		'/news/fav' => array(
			'controller' => 'blog',
			'action' => 'viewFavorite'
		),
		'/news/fav/toggle/(\d+)' => array(
			'id',
			'controller' => 'blog',
			'action' => 'toggleFavorite'
		),
		'/news/res/(\d+)' => array(
			'id',
			'controller' => 'blog',
			'action' => 'viewPost'
		),
		'/news/res/(\d+)/rate_post/up' => array(
			'id',
			'vote' => 'up',
			'controller' => 'blog',
			'action' => 'ratePost'
		),
		'/news/res/(\d+)/rate_post/down' => array(
			'id',
			'vote' => 'down',
			'controller' => 'blog',
			'action' => 'ratePost'
		),
		'/news/res/(\d+)/add_comment' => array(
			'post_id',
			'controller' => 'blog',
			'action' => 'addComment'
		),
		'/news/res/(\d+)/stats' => array(
			'post_id',
			'controller' => 'blog',
			'action' => 'postStats'
		),
		'/news/res/(\d+)/getComment/(\d+)' => array(
			'post_id', 'id',
			'controller' => 'blog',
			'action' => 'getPostComment'
		),
		'/news/add' => array(
			'controller' => 'blog',
			'action' => 'addPost',
		),
		'/news/add/validate' => array(
			'controller' => 'blog',
			'action' => 'validatePost',
		),
		'/news/add/preview' => array(
			'controller' => 'blog',
			'action' => 'previewPost',
		),
		'/news/search' => array(
			'controller' => 'blog',
			'action' => 'search'
		),
		'/news/last_comments' => array(
			'controller' => 'mod',
			'action' => 'getLastComments'
		),
		'/news/rss.xml' => array(
			'controller' => 'blog',
			'action' => 'rssApproved'
		),
		'/news/all/rss.xml' => array(
			'controller' => 'blog',
			'action' => 'rssAll'
		),
		'/news/hidden/rss.xml' => array(
			'controller' => 'blog',
			'action' => 'rssHidden'
		),
		'/mod/getPost/(\d+)' => array(
			'id',
			'controller' => 'mod',
			'action' => 'getPost'
		),
		'/mod/categoryPost/(\d+)' => array(
			'id',
			'controller' => 'mod',
			'action' => 'categoryPost'
		),
		'/mod/pinnedPost/(\d+)' => array(
			'id',
			'controller' => 'mod',
			'action' => 'pinnedPost'
		),
		'/mod/ratedPost/(\d+)' => array(
			'id',
			'controller' => 'mod',
			'action' => 'ratedPost'
		),
		'/mod/rateablePost/(\d+)' => array(
			'id',
			'controller' => 'mod',
			'action' => 'rateablePost'
		),
		'/mod/closedPost/(\d+)' => array(
			'id',
			'controller' => 'mod',
			'action' => 'closedPost'
		),
		'/mod/hiddenPost/(\d+)' => array(
			'id',
			'controller' => 'mod',
			'action' => 'hiddenPost'
		),
		'/mod/removePostComment/(\d+)' => array(
			'id',
			'controller' => 'mod',
			'action' => 'removePostComment'
		),
		'/mod/removeOnlineLink/(\d+)' => array(
			'id',
			'controller' => 'mod',
			'action' => 'removeOnlineLink'
		),
		'/live'  => array(
			'controller' => 'live',
			'action' => 'index'
		),
		'/live/add'  => array(
			'controller' => 'live',
			'action' => 'add'
		),
		'/live/addXS'  => array(
			'controller' => 'live',
			'action' => 'addCrossSite'
		),
		'/live/redirect/(\d+)'  => array(
			'id',
			'controller' => 'live',
			'action' => 'redirect'
		),
		'/live/set_filter'  => array(
			'controller' => 'live',
			'action' => 'setFilter'
		),
		'/live/linksPanel/' => array(
		    'controller' => 'live',
		    'action' => 'toggleLinksPanel'
		),
		'/service/theme/([^/]+)' => array(
			'theme',
			'controller' => 'generic_theme',
			'action' => 'switch'
		),
		'/service/preview' => array(
			'controller' => 'static',
			'action' => 'preview'
		),
		'/service/getGlobalStats' => array(
		    'controller' => 'blog',
		    'action' => 'getGlobalStats'
		),
		'/service/modlog' => array(
		    'controller' => 'mod',
		    'action' => 'getModActions'
		),
		'/service/modlog/rss.xml' => array(
				'controller' => 'mod',
				'action' => 'getModActionsRss'
		),
		'/service/share' => array(
			'controller' => 'mod',
			'action' => 'shareLink'
		),
		'/chat' => array(
			'controller' => 'chat',
			'action' => 'index'
		),
		'/chat/add' => array(
			'controller' => 'chat',
			'action' => 'add'
		),
		'/chat/favorites' => array(
			'controller' => 'chat',
			'action' => 'setFavorites'
		),
		'/chat/common' => array(
			'controller' => 'chat',
			'action' => 'common'
		),
		'/chat/([^/]{45})' => array(
			'id',
			'controller' => 'chat',
			'action' => 'chat'
		),
		'/chat/log/([^/]{45})' => array(
			'id',
			'controller' => 'chat',
			'action' => 'log'

		),
		'/chat/([0-9A-z\-]{4,20})' => array(
			'alias',
			'controller' => 'chat',
			'action' => 'chat'
		),
		'(.*)' => array(
			'page',
			'controller' => 'static',
			'action' => 'index'
		)
	)),
	'captcha' => array(
		//'alphabet'         => '0123456789abcdefghijklmnopqrstuvwxyz',
		//'allowed_symbols'  => '23456789abcdeghkmnpqsuvxyz',
		'alphabet'         => '0123456789',
		'allowed_symbols'  => '23456789',
		'fontsdir'         => 'fonts',
		'width'            => 160,
		'height'           => 50,
		'fluctuation_amplitude' => 6,
		'no_spaces'        => false,
		'show_credits'     => false,
		'credits'          => '',
		'foreground_color' => array(mt_rand(0,100), mt_rand(0,100), mt_rand(0,100)),
		'background_color' => array(255, 255, 255),
		'jpeg_quality'     => 0
	)
);
