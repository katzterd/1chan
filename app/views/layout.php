<!DOCTYPE html>
<html lang="ru">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="description" content="<?php echo META_DESCRIPTION ?>" />
		<meta name="keywords" content="<?php echo META_KEYWORDS ?>" />
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1">
		<title><?php echo $this -> getParameter('title'); ?> | <?php echo TemplateHelper::getSiteUrl(); ?></title>
		<?= $this -> getParameter('favicon') ?>
		<link rel="stylesheet" type="text/css"  href="/css/common.css<?php echo CSS_VERSION ?>" media="all" />
		<?php $theme = $this -> getParameter('global_theme'); ?>
		<?php if ($theme): ?>
			<link id="color-theme" rel="stylesheet" type="text/css" href="/css/themes/<?= $theme ?>.css<?= CSS_VERSION ?>" media="all" />
			<link id="color-theme-custom" rel="stylesheet" type="text/css" href="/css/themes/<?= $theme ?>.custom.css<?= CSS_VERSION ?>" media="all" />
		<?php else: ?>
			<style id="default-themes">
				<?php foreach($this -> getParameter('global_themes') as $i => $theme): ?>
					<?php if ($i==0): ?>
						/* Дефолтная светлая тема */
						@import url("/css/themes/<?= $theme ?>.css<?= CSS_VERSION ?>") (prefers-color-scheme: light);
						@import url("/css/themes/<?= $theme ?>.custom.css<?= CSS_VERSION ?>") (prefers-color-scheme: light);
					<?php endif; ?>
					<?php if ($i==1): ?>
						/* Дефолтная тёмная тема */
						@import url("/css/themes/<?= $theme ?>.css<?= CSS_VERSION ?>") (prefers-color-scheme: dark);
						@import url("/css/themes/<?= $theme ?>.custom.css<?= CSS_VERSION ?>") (prefers-color-scheme: dark);
					<?php endif; ?>
				<?php endforeach; ?>
			</style>
		<?php endif; ?>
		<script>const COLOR_THEMES = "<?= COLOR_THEMES ?>".split("|")</script>

		<link rel="stylesheet" type="text/css" href="/js/jquery-ui/jquery-ui.min.css" media="all" />

		<script>const IS_BOARD = <?= ($this -> getParameter('is_board')) ? 'true' : 'false' ?></script>

		<script type="text/javascript" src="/js/jquery-3.7.1.min.js"></script>
		<script type="text/javascript" src="/js/jquery-cookie.js"></script>
		<script type="text/javascript" src="/js/jquery.scrollTo.min.js"></script>
		<script type="text/javascript" src="/js/jquery-ui/jquery-ui.min.js"></script>
		<script type="text/javascript" src="/socket.io/socket.io.js"></script>
		<script type="text/javascript" src="/js/production.js<?php echo JS_VERSION ?>"></script>
	</head>

	<body id="<?php echo(Session::getInstance() -> getKey()); ?>">
	<div class="b-notifiers js-notifiers"></div>
	<div class="b-mod-toolbar g-hidden">
		<a href="#" id="mod_category" title="Категория"><img src="/ico/settings2.png" width="16" height="16" alt="" /></a>
		<a href="#" id="mod_pinned" title="Прикреплена"><img src="/ico/pinned.png" width="16" height="16" alt="" /></a>
		<a href="#" id="mod_rated" title="Одобрена"><img src="/ico/<?php echo APPROVED_ICON.ICONS_VERSION ?>" width="16" height="16" alt="" /></a>
		<a href="#" id="mod_rateable" title="Оцениваема"><img src="/ico/rate_on.png" width="16" height="16" alt="" /></a>
		<a href="#" id="mod_closed" title="Закрыта"><img src="/ico/block.png" width="16" height="16" alt="" /></a>
		<a href="#" id="mod_remove" title="Удалить"><img src="/ico/remove.gif" width="16" height="16" alt="" /></a>
	</div>
	<?php if ($message = ControlModel::isGlobalMessage()): ?>
		<div class="b-global-message-panel">
			<div class="l-wrap">
				<img src="/ico/warning.png" width="16" height="16" alt="" /> <?php echo($message); ?>
			</div>
		</div>
	<?php endif; ?>
		<div class="l-wrap">
			<?php if (ENABLE_POO == "true"): ?>
				<div class="js-poo-wrapper">
					<div class="js-poo-target">
						<img src="/img/poo.png" width="64" height="65">
					</div>
					  <a href="javascript://" class="g-dynamic js-poo-toggle">Включить каку</a>
				</div>
			<?php endif; ?> 

			<div class="b-top-panel"><ul>
				<?php foreach($this -> getParameter('global_top_panel') as $s => $section): ?>
					<?php $right = $s==1 ? ' class="b-top-panel_m-right"' : '' ?>
					<?php foreach($section as $i => $link): ?>
						<?php $total = sizeof($section); ?>
						<?php if (@$link['html']): ?>
							<li<?= $right ?>><?= $link['html'] ?></li>
						<?php else: ?>
							<?php $className = @$link['class'] ? " class='" . $link['class'] . "'" : ''; ?>
							<li<?= $right ?>>
								<a<?= $className ?> href="<?= $link["href"] ?>"><?= $link["text"] ?></a>
							</li>
						<?php endif; ?>
						<?php if ($total > 1 && $i+1 < $total): ?><li>|</li><?php endif; ?>
					<?php endforeach; ?>
				<?php endforeach; ?>
			</ul></div>

			<div class="b-header-block m-mascot m-mascot-<?php echo($this -> getParameter('board_id', 'news')); ?><?= ($this -> getParameter('mascot', null)) ? " m-mascot-".($this -> getParameter('mascot', null)) : "" ?> <?php if (date('j n') === '1 4') { echo 'clickme'; } ?>">
				<div class="b-header-block_b-logotype">
					<a href="/news/all/">
						<div id="logo"></div>
					</a>
				</div>
				<div class="b-header-block_b-stats" id="stats_block">
					<p>
						Вижу <strong id="stats_online"><?php echo($this -> getParameter('global_online', 1)); ?></strong>! Всего сегодня было <strong id="stats_hosts"><?php echo($this -> getParameter('global_unique', 1)); ?></strong>.<br />
						За день постов — <strong id="stats_posts"><?php echo($this -> getParameter('global_posts', 0)); ?></strong>, скорость ~<strong id="stats_speed"><?php echo($this -> getParameter('global_speed', 0)); ?></strong> п/ч.
						<span class="g-hidden" id="stats_unique_posters"><?php echo($this -> getParameter('global_unique_posters', 1)); ?></span>
					</p>
				</div>
			</div>

			<?php if($this -> getParameter('right_panel')): ?>
				<div class="l-right-panel-wrap">
					<div class="b-links-panel js-links-panel">
						<div class="b-links-panel_b-title">
							<h2>Онлайн ссылки</h2>
						</div>
						<div class="b-links-panel_b-links">
							<div id="placeholder_link_panel">
								<?php $links = $this -> getParameter('online_links', array()); ?>
								<?php if (!empty($links)): ?>
									<?php foreach($links as $link): ?>
										<div class="b-live-entry">
											<a target="_blank" href="/live/redirect/<?php echo($link['id']) ?>?to=<?php echo($link['link']); ?>" class="b-live-entry_b-description"><?php echo($link['description']); ?></a> &larr; <a href="#" class="b-live-entry_b-board"><?php echo($link['category']['title']); ?></a>
										</div>
									<?php endforeach; ?>

								<?php else: ?>
									<em>Нет активных ссылок</em>
								<?php endif; ?>
							</div>
							<textarea id="template_link_panel" style="display:none">
								<div class="b-live-entry">
									<a href="/live/redirect/<%=id%>?to=<%=link%>" class="b-live-entry_b-description"><%=description%></a> &larr; <a href="#" class="b-live-entry_b-board"><%=category['title']%></a>
								</div>
							</textarea>
						</div>
						<div class="b-links-panel_b-footer">
							<a href="/live/">К подробному списку &rarr;</a>
						</div>
						<div class="b-links-panel_b-hide">
							<a href="/live/linksPanel/?status=off" class="b-links-panel_b-title_b-close g-dynamic js-close-right-panel g-hidden">Скрыть панель</a>
						</div>
					</div>

					<?php if ($this -> getParameter('right-side-bar')): ?>
						<div class="b-menu-panel"><?= $this -> getParameter('right-side-bar') ?></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="l-left-panel-wrap">
				<div class="b-menu-panel">
					<div class="b-menu-panel_b-title">
						<h2>Первый канал</h2>
					</div>
					<div class="b-menu-panel_b-links">
						<ul>
							<li<?php if($this -> getParameter('board_id') == 'news'): ?> class="m-active"<?php endif; ?>><a href="/news/">Одобренные</a> | <a href="/news/all/">Все</a></li>
							<li class="b-menu-panel_b-footer">
								<a class="hidden" href="/news/hidden/">Скрытые</a>
							</li>
						</ul>
					</div>
				</div>

				<div class="b-menu-panel">
					<div class="b-menu-panel_b-title">
						<h2>Общение</h2>
					</div>
					<div class="b-menu-panel_b-links">
						<ul>
							<?php if (empty($this -> getParameter('boards'))): ?>
								<li>Нет активных разделов</li>
							<?php else: ?>
								<?php foreach($this -> getParameter('boards') as $board): ?>
									<?php if (!@$board['hidden']): ?>
									<li <?= $this -> getParameter('board_id') == $board['title'] ? 'class="m-active"' : '' ?>>
										<a href="/<?= $board['title'] ?>/">/<?= $board['title'] ?>/ - <?= $board['description'] ?></a>
									</li>
									<?php endif; ?>
								<?php endforeach; ?>
							<?php endif; ?>
							<li<?php if($this -> getParameter('board_id') == 'fav'): ?> class="m-active"<?php endif; ?>><img src="/ico/favorites-false.png" width="16" height="16" alt="" /> <a href="/fav/">Избранные треды</a></li>
							<li class="b-menu-panel_b-footer">
									<a href="/service/last_board_posts/">&larr; последние посты</a>
							</li>
						</ul>
					</div>
				</div>

				<?php if ($this -> getParameter('left-side-bar')): ?>
					<div class="b-menu-panel"><?= $this -> getParameter('left-side-bar') ?></div>
				<?php endif; ?>
			</div>

			<div class="l-content-wrap">
				<?php echo $content; ?>

			</div>
			<div class="l-footer-wrap m-mascot m-mascot-<?php echo($this -> getParameter('board_id', 'news')); ?>">
				<div class="b-underlinks">
					<img src="/ico/rss.png" width="16" height="16" alt="" />
					<a href="/news/rss.xml">Одобренные</a> |
					<img src="/ico/rss2.png" width="16" height="16" alt="" />
					<a href="/news/all/rss.xml">Все</a>
				</div>
				<?php $_footer_links = Blog_BlogLinksModel::GetLinks(); ?>
				<div class="b-footer-imgboards">
					<h2>Имиджборды:</h2>
					<ul><?php foreach($_footer_links['imgboards'] as $link): ?>
						<li>
							<?php if(@$link['offline']): ?>
								<img src="/ico/offline.png" width="16" height="16" alt="Сайт недоступен" />
								<a class="g-strike" href="<?php echo($link['href']); ?>"><?php echo($link['title']); ?></a>
							<?php else: ?>
								<img src="<?php echo(TemplateHelper::getIcon($link['href'])); ?>" width="16" height="16" alt="" />
								<a href="<?php echo($link['href']); ?>"><?php echo($link['title']); ?></a>
							<?php endif; ?>
						</li>
					<?php endforeach; ?></ul>
				</div>
				<div class="b-footer-services">
					<h2>Другие ссылки:</h2>
					<ul><?php foreach($_footer_links['services'] as $link): ?>
						<li>
							<?php if(@$link['offline']): ?>
								<img src="/ico/offline.png" width="16" height="16" alt="Сайт недоступен" />
								<a class="g-strike" href="<?php echo($link['href']); ?>"><?php echo($link['title']); ?></a>
							<?php else: ?>
								<img src="<?php echo(TemplateHelper::getIcon($link['href'])); ?>" width="16" height="16" alt="" />
								<a href="<?php echo($link['href']); ?>"><?php echo($link['title']); ?></a>
							<?php endif; ?>
						</li>
					<?php endforeach; ?></ul>
				</div>
				<div class="b-footer-copyrights">
					<span>При копировании материалов ни в коем случае не давать ссылку на <a href="/"><?php echo TemplateHelper::getSiteUrl(); ?></a></span>
				</div>
			</div>
		</div>
	</body>
</html>
