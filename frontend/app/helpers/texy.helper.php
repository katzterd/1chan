<?php
/**
 * Фильтр сообщений (разметка):
 */
class TexyHelper
{
	/**
	 * Регулярные выражения для видео:
	 */
	const YOUTUBE_REGEXP = 'https:\/\/(?:www\.|)youtube\.com\/watch\?v=([a-zA-Z0-9_\-]+)';

	/**
	 * Чтение списка смайликов:
	 */
	public static function getSmilies() {
		static $smilies;
		if ($smilies == null) {
			$cache = KVS::getInstance();
			if ($cache -> exists('Smilies', null, 'list')) {
				$smilies = @unserialize($cache -> get('Smilies', null, 'list')) ?? [];
			}
			else {
				$smilies = [];
			}
		}
		return $smilies;
	}

	/**
	 * Метод создания объекта Texy:
	 */
	private static function createTexyObject($post_link = true, $board = null)
	{
		$texy = new Texy();
		$texy -> mergeLines = false;
		$texy -> htmlOutputModule -> baseIndent  = 6;
		$texy -> typographyModule -> locale = 'fr';

		self::registerSmilies($texy);
		
		$texy->registerLinePattern(
			array('TexyHelper', 'spoiler'),
			'/%%(([^%]|%[^%])+)%%/',
			'TexyHelper_spoiler'
		);

		$texy->registerLinePattern(
			array('TexyHelper', 'tts'),
			'/#%(([^%]|%[^%])+)%#/',
			'TexyHelper_tts'
		);
		
		$texy->registerLinePattern(
			array('TexyHelper', 'coincidence'),
			'/\(\(\(([^\(\)]+)\)\)\)/',
			'TexyHelper_coincidence'
		);

		/*$texy->registerLinePattern(
			array('TexyHelper', 'redline'),
			'/\$\$(([^\$]|\$[^\$])+)\$\$/',
			'TexyHelper_redline'
		);*/

		if ($board == null)
		{
			$texy->registerLinePattern(
				array('TexyHelper', 'imgur'),
				'/\[i:([^\]]+):\]/',
				'TexyHelper_imgur'
			);

			$texy->registerLinePattern(
				array('TexyHelper', 'catbox'),
				'/\[c:(([^\]]+)\.((?i)jpe?g|gif|png|webp)):\]/',
				'TexyHelper_catbox'
			);

			$texy->registerLinePattern(
				array('TexyHelper', 'catboxvid'),
				'/\[c:(([^\]]+)\.((?i)webm|mp4)):\]/',
				'TexyHelper_catboxvid'
			);

			/*$texy->registerLinePattern(
				array('TexyHelper', 'youtube'),
				'/\[youtube:([^\]]+)\]/',
				'TexyHelper__youtube'
			);*/

			$texy->registerLinePattern(
				array('TexyHelper', 'images'),
				'/\[([^\]]+)\]/',
				'TexyHelper__images'
			);

			if ($post_link)
			{
				$texy->registerLinePattern(
					array('TexyHelper', 'postlink'),
					'/>>(\d+)/',
					'TexyHelper_postlink'
				);

				$texy->registerLinePattern(
					array('TexyHelper', 'boardpostlink2'),
					'/>>([^\/]+)\/(\d+)/',
					'boardlink'
				);
			}
		}
		else
		{
				$texy->registerLinePattern(
					array('TexyHelper', 'boardpostlink'),
					'/>>(\d+)/',
					'boardlink_'. $board
				);

				$texy->registerLinePattern(
					array('TexyHelper', 'boardpostlink2'),
					'/>>([^\/]+)\/(\d+)/',
					'boardlink'
				);
		}

		return $texy;
	}

	// Dynamically create methods for each smiley
	public static function createSmileyMethod($smiley) {
		return function($parser, $matches, $name) use ($smiley) {
			$el = TexyHtml::el('img');
			$el->attrs['src'] = '/img/smilies/' . $smiley['name'] . '.'. $smiley['ext'];
			$el->attrs['class'] = 'smiley';
			$el->attrs['width'] = $smiley['width'];
			$el->attrs['height'] = $smiley['height'];
			$el->attrs['alt'] = ':'.$smiley['name'].':';

			$parser->again = false;

			return $el;
		};
	}

	// Register all smilies
	public static function registerSmilies($texy) {
		foreach (self::getSmilies() as $smiley) {
			$pattern = '/:' . $smiley['name'] . ':/';
			$method = self::createSmileyMethod($smiley);
			$texy->registerLinePattern($method, $pattern, 'TexyHelper_' . $smiley['name']);
		}
	}

	/**
	 * Обработать строку (только типография):
	 */
	public static function typo($text)
	{
		$texy = self::createTexyObject();
		TexyConfigurator::safeMode($texy);
		TexyConfigurator::disableImages($texy);
		TexyConfigurator::disableLinks($texy);
		return $texy -> processTypo(htmlspecialchars($text));
	}

	/**
	 * Обработать строку (вся разметка):
	 */
	public static function markup($text, $safeMode = true, $links = true, $board = null)
	{
		$texy = self::createTexyObject($links, $board);

		if ($safeMode)
		{
			TexyConfigurator::safeMode($texy);
			TexyConfigurator::disableImages($texy);
		}
		return $texy -> process($text);
	}

	/**
	* Вставка картинок:
	*/

	static function images($parser, $matches, $name) {
		list(, $mContent) = $matches;
		if (!isset($GLOBALS['post_image_count'])) {
			$GLOBALS['post_image_count']= 0;
		}
		if (preg_match(self::URL_REGEXP, $mContent))
		{
			if ($GLOBALS['post_image_count']++) {
				$parser -> again = false;
				return '['. $mContent .']';
			}
			try {
				if (($u = PreviewHelper::upload($mContent)) !== false) {
					$img = TexyHtml::el('img');
					$img -> attrs['src']    = $u;
					$img -> attrs['alt']    = '';
					$link = TexyHtml::el('a');
					$link -> attrs['target'] = '_blank';
					$link -> attrs['class']  = 'b-image-link';
					$link -> href($mContent);
					$link -> add($img);
					$parser -> again = false;
					$GLOBALS['post_image_count'] = true;
					return $link;
				}
			}catch (\Exception $exception)
			{
				return $exception->getMessage();
			}

		}
		return '['. $mContent .']';
	}


	/**
	 * Имгур:
	 */
	static function imgur($parser, $matches, $name) {
		list(, $mContent, $mMod) = $matches;

		if (isset($GLOBALS['texy_test']) && $GLOBALS['texy_test']) {
			if ($GLOBALS['post_image_count']++) {
				$parser -> again = false;
				return '[i:'. $mContent .':]';
			}
		}

		$img = TexyHtml::el('img');
		$img -> attrs['src']    = 'https://i.imgur.com/'.$mContent.'.jpg';
		$img -> attrs['alt']    = '';

		$link = TexyHtml::el('a');
		$link -> attrs['target'] = '_blank';
		$link -> attrs['class'] = 'b-image-link';
		$link -> attrs['rel'] = 'nofollow noopener noreferrer';
		$link -> href('https://i.imgur.com/'.$mContent.'.jpg');
		$link -> add($img);

		$parser -> again = false;
		return $link;
	}

	/**
	 * Кятбокс:
	 */
	static function catbox($parser, $matches, $name) {
		list(, $mContent, $mMod) = $matches;

		if (isset($GLOBALS['texy_test']) && $GLOBALS['texy_test']) {
			if ($GLOBALS['post_image_count']++) {
				$parser -> again = false;
				return '[i:'. $mContent .':]';
			}
		}

		$img = TexyHtml::el('img');
		$img -> attrs['src']    = 'https://files.catbox.moe/'.$mContent;
		$img -> attrs['alt']    = '';

		$link = TexyHtml::el('a');
		$link -> attrs['target'] = '_blank';
		$link -> attrs['class'] = 'b-image-link';
		$link -> attrs['rel'] = 'nofollow noopener noreferrer';
		$link -> href('https://files.catbox.moe/'.$mContent);
		$link -> add($img);

		$parser -> again = false;
		return $link;
	}

	/**
	 * Кятбокс (видео):
	 */
	static function catboxvid($parser, $matches, $name) {
		list(, $mContent, $mMod) = $matches;

		if (isset($GLOBALS['texy_test']) && $GLOBALS['texy_test']) {
			if ($GLOBALS['post_image_count']++) {
				$parser -> again = false;
				return '[i:'. $mContent .':]';
			}
		}

		$vid = TexyHtml::el('video');
		$vid -> attrs['src'] = 'https://files.catbox.moe/'.$mContent;
		$vid -> attrs['autoplay'] = '';
		$vid -> attrs['loop'] = '';
		$vid -> attrs['muted'] = '';
		$vid -> attrs['controls'] = '';

		$link = TexyHtml::el('a');
		$link -> attrs['target'] = '_blank';
		$link -> attrs['class'] = 'b-image-link';
		$link -> attrs['rel'] = 'nofollow noopener noreferrer';
		$link -> href('https://files.catbox.moe/'.$mContent);
		$link -> add($vid);

		$parser -> again = false;
		return $link;
	}

	/**
	 * Спойлер:
	 */
	static function spoiler($parser, $matches, $name) {
		list(, $mContent, $mMod) = $matches;

		$spl = TexyHtml::el('span');
		$spl -> attrs['class'] = 'b-spoiler-text';
		$spl -> setText($mContent);

		$parser -> again = true;
		return $spl;
	}

	/**
	 * Детектор совпадений:
	 */
	static function coincidence($parser, $matches, $name) {
		list(, $mContent, $mMod) = $matches;

		$spl = TexyHtml::el('span');
		$spl -> attrs['class'] = 'b-coincidence';
		$spl -> setText( '((('.$mContent.')))' );

		$parser -> again = false;
		return $spl;
	}

	/**
	 * Розмовлялка:
	 */
	static function tts($parser, $matches, $name) {
		list(, $mContent, $mMod) = $matches;

		$spl = TexyHtml::el('audio');
		$spl -> attrs['controls'] = '';
		$spl -> attrs['src'] = 'https://tts.voicetech.yandex.net/tts?text='.$mContent.'&amp;lang=ru_RU&amp;format=mp3&amp;quality=hi&amp;platform=web&amp;application=translate&amp;chunked=0&amp;mock-ranges=1';

		$parser -> again = true;
		return $spl;
	}

	/**
	 * Реверсивная цитата:
	 */
	static function reverseblockquote($parser, $matches, $name) {
		list(, $mContent, $mMod) = $matches;

		$spl = TexyHtml::el('blockquote');
		$spl -> attrs['class'] = 'reverse';

		$parser -> again = true;
		return $spl;
	}

	/**
	 * Красный:
	 */
	static function redline($parser, $matches, $name) {
		list(, $mContent, $mMod) = $matches;

		$spl = TexyHtml::el('span');
		$spl -> attrs['style'] = 'color:red';
		$spl -> setText($mContent);

		$parser -> again = true;
		return $spl;
	}
	
	/**
	 * Ссылка на пост (комментарий):
	 */
	static function postlink($parser, $matches, $name)
	{
		list(, $id) = $matches;
		$parser -> again = false;

		if (Blog_BlogCommentsModel::CommentExists($id))
		{
			$comment = Blog_BlogCommentsModel::GetComment($id);
			$post_id = $comment['post_id'];
		}
		elseif (Blog_BlogPostsModel::PostExists($id))
			$post_id = $id;
		else
			return '&gt;&gt;'. $id;

		$link = TexyHtml::el('a');
		$link -> href('/news/res/'. $post_id .'/#'. $id );
		$link -> attrs['class'] = 'js-cross-link';
		$link -> attrs['name'] = 'news/'. $id;
		$link -> setText('&gt;&gt;'. $id);

		return $link;
	}

	/**
	 * Ссылка на пост борды:
	 */
	static function boardpostlink($parser, $matches, $name)
	{
		list(, $id) = $matches;
		$parser -> again = false;

		// Получаем название текущей борды:
		$board_name = substr($name, 10);
		$board = new Board_BoardModel($board_name);

		if ($board -> existsPost($id))
		{
			$post = $board -> getPost($id);
			$href = '/'. $board_name .'/res/';

			if ($post['parent_id'] == null)
				$href .= $id .'/#top';
			else
				$href .= $post['parent_id'] .'/#'. $id;

			$link = TexyHtml::el('a');
			$link -> href($href);
			$link -> attrs['class']  = 'js-cross-link';
			$link -> attrs['name'] = $board_name .'/'. $id;
			$link -> setText('&gt;&gt;'. $id);

			return $link;
		}

		return '&gt;&gt;'. $id;
	}

	static function boardpostlink2($parser, $matches, $name)
	{
		list(, $board_name, $id) = $matches;
		$parser -> again = false;

		if ($board_name != 'news')
		{
			$board = new Board_BoardModel($board_name);

			if ($board -> existsPost($id))
			{
				$post = $board -> getPost($id);
				$href = '/'. $board_name .'/res/';

				if ($post['parent_id'] == null)
					$href .= $id .'/#top';
				else
					$href .= $post['parent_id'] .'/#'. $id;

				$link = TexyHtml::el('a');
				$link -> href($href);
				$link -> attrs['class']  = 'js-cross-link';
				$link -> attrs['name'] = $board_name .'/'. $id;
				$link -> setText('&gt;&gt;'. $board_name .'/'. $id);

				return $link;
			}

			return '&gt;&gt;'. $board_name .'/'. $id;
		}

		if (Blog_BlogCommentsModel::CommentExists($id))
		{
			$comment = Blog_BlogCommentsModel::GetComment($id);
			$post_id = $comment['post_id'];
		}
		elseif (Blog_BlogPostsModel::PostExists($id))
			$post_id = $id;
		else
			'&gt;&gt;news/'. $id;

		$link = TexyHtml::el('a');
		$link -> href('/news/res/'. $post_id .'/#'. $id );
		$link -> attrs['class'] = 'js-cross-link';
		$link -> attrs['name'] = 'news/'. $id;
		$link -> setText('&gt;&gt;news/'. $id);

		return $link;
	}

	/**
	 * Получить код вставки видео:
	 */
	public static function getVideo($url)
	{
		if (preg_match('/^'.self::YOUTUBE_REGEXP.'/i', $url))
		{
			return preg_replace('/'.self::YOUTUBE_REGEXP.'(.*)/i', '<div class="b-video"><div class="g-hidden"><object width="520" height="400"><param name="wmode" value="opaque"></param><embed src="https://www.youtube.com/v/$1&hl=en" type="application/x-shockwave-flash" wmode="opaque" width="520" height="400"></embed></param></embed></object></div></div>', $url);
		}

		return false;
	}

	const URL_REGEXP = "{
			  ^
			  (
				(https?)://[-\\w]+(\\.\\w[-\\w]*)+
			  |
				(?i: [a-z0-9] (?:[-a-z0-9]*[a-z0-9])? \\. )+
				(?-i: com\\b
			| edu\\b
			| biz\\b
			| gov\\b
			| in(?:t|fo)\\b # .int or .info
			| mil\\b
			| net\\b
			| org\\b
			| ovh\\b
			| xyz\\b
			| space\\b
			| club\\b
			| onion\\b
			| i2p\\b
			| [a-z][a-z]\\.[a-z][a-z]\\b # two-letter country code
				)
			  )
			  ( : \\d+ )?
			  (
				/
				[^.!,?;\"\\'<>()\[\]\{\}\s\x7F-\\xFF]*
				(
				  [.!,?]+ [^.!,?;\"\\'<>()\\[\\]\{\\}\s\\x7F-\\xFF]+
				)*
			  )?
			}ix";
}
