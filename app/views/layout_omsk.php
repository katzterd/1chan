<!DOCTYPE html>
<html lang="ru">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="description" content="Первый канал интернетов" />
		<meta name="keywords" content="крокодил, залупа, сыр" />

		<title><?php echo $this -> getParameter('title'); ?> | <?php echo TemplateHelper::getSiteUrl(); ?></title>

		<link rel="icon"       type="image/png" href="/ico/favicon.png" />
		<link rel="stylesheet" type="text/css"     href="/css/production-omsk.css?23" media="all" />
		<link rel="stylesheet" type="text/css"     href="/css/jquery_style/jquery-ui.css" media="all" />

		<script type="text/javascript" src="/js/jquery.js"></script>
		<script type="text/javascript" src="/js/realplexor.js"></script>
		<script type="text/javascript" src="/js/production.js"></script>
		<script type="text/javascript" src="/js/youtube.js"></script>
	</head>

	<body id="<?php echo(Session::getInstance() -> getKey()); ?>">
	<div class="b-notifiers js-notifiers"></div>

	<div class="b-mod-toolbar g-hidden">
	    <a href="#" id="mod_category" title="Категория"><img src="/ico/settings2.png" width="16" height="16" alt="" /></a>
		<a href="#" id="mod_pinned"   title="Прикреплена"><img src="/ico/pinned.png" width="16" height="16" alt="" /></a>
		<a href="#" id="mod_rated"    title="Одобрена"><img src="/ico/tick.png" width="16" height="16" alt="" /></a>
		<a href="#" id="mod_rateable" title="Оцениваема"><img src="/ico/rate_on.png" width="16" height="16" alt="" /></a>
		<a href="#" id="mod_closed"   title="Закрыта"><img src="/ico/block.png" width="16" height="16" alt="" /></a>
		<a href="#" id="mod_remove"   title="Удалить"><img src="/ico/remove.gif" width="16" height="16" alt="" /></a>
	</div>
	<?php if ($message = ControlModel::isGlobalMessage()): ?>
		<div class="b-global-message-panel">
			<div class="l-wrap">
				<img src="/ico/warning.png" width="16" height="16" alt="" /> <?php echo($message); ?>
			</div>
		</div>
	<?php endif; ?>
		<div class="l-wrap">
			<?php /*<div style="
				position: fixed;
				background: black;
				border: 1px solid #aaa;
				width: 100px;
				height: 100px;
				border-radius: 8px;
				box-shadow: 0px 0px 1px #888;
				margin-left: 700px;
				bottom: 0;
				z-index: 999;
				font-size: 11px;
				padding: 5px;
				text-align: center;
			"><div class="js-poo-target" style="text-align: center; padding-top: 3px; padding-right: 0px; padding-bottom: 3px; padding-left: 0px;"><img src="/img/poo.png" width="64" height="65"></div><a href="javascript://" class="g-dynamic js-poo-toggle">Включить каку</a></div> */ ?>

			<div class="b-top-panel">
				<ul>
					<li>
						<a href="/live/" class="b-top-panel_b-online-link<?php if(!TemplateHelper::isLiveUpdated()): ?> m-disactive<?php endif; ?>">Онлайн ссылки</a>
					</li>
					<li>|</li>
					<li>
						<a href="/chat/">Анонимные чаты</a>
					</li>

					<li class="b-top-panel_m-right">
						<a href="http://kolchpl6sf4t7yjf57an3gxyprqqtjm2gtvatzkcsx27uu3psssnmyad.onion">Tor-зеркало</a>
					</li>
				</ul>
			</div>

			<div class="b-header-block m-mascot-<?php echo($this -> getParameter('board_id', 'news')); ?>">
				<div class="b-header-block_b-logotype">
					<a href="/news/all/">
						<img src="/img/ogol.png" width="250" height="80" alt="1chan.pl" />
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
							<li>Нет активных разделов</li>
			            	<?php /* <li<?php if($this -> getParameter('board_id') == 'operate'): ?> class="m-active"<?php endif; ?>><a href="/operate/">/operate/ - 1chan Discussion</a></li> */ ?>
			            	<li<?php if($this -> getParameter('board_id') == 'fav'): ?> class="m-active"<?php endif; ?>><img src="/ico/favorites-false.png" width="16" height="16" alt="" /> <a href="/fav/">Избранные треды</a></li>
				        <li class="b-menu-panel_b-footer">
				            <a href="/service/last_board_posts/">&larr; последние посты</a>
				        </li>
				    </ul>
				    </div>
				</div>
			</div>

			<div class="l-content-wrap">
				<?php echo $content; ?>

			</div>
			<div class="l-footer-wrap m-mascot-<?php echo($this -> getParameter('board_id', 'news')); ?>">
				<div class="b-underlinks">
					<img src="/ico/rss.png" width="16" height="16" alt="" />
					<a href="/news/rss.xml">Одобренные</a> |
					<img src="/ico/rss2.png" width="16" height="16" alt="" />
					<a href="/news/all/rss.xml">Все</a>
				</div>
				<?php $_footer_links = Blog_BlogLinksModel::GetLinks(); ?>
				<div class="b-footer-imgboards">
					<h2>Имиджборды:</h2>
					<ul>
					<?php foreach($_footer_links['imgboards'] as $link): ?>

						<li>
						<?php if(@$link['offline']): ?>

							<img src="/ico/offline.png" width="16" height="16" alt="Сайт недоступен" />
							<a class="g-strike" href="<?php echo($link['href']); ?>"><?php echo($link['title']); ?></a>
						<?php else: ?>

							<img src="<?php echo(TemplateHelper::getIcon($link['href'])); ?>" width="16" height="16" alt="" />
							<a href="<?php echo($link['href']); ?>"><?php echo($link['title']); ?></a>
						<?php endif; ?>

						</li>
					<?php endforeach; ?>

					</ul>

				</div>
				<div class="b-footer-services">
					<h2>Другие ссылки:</h2>
					<ul>
					<?php foreach($_footer_links['services'] as $link): ?>

						<li>
						<?php if(@$link['offline']): ?>

							<img src="/ico/offline.png" width="16" height="16" alt="Сайт недоступен" />
							<a class="g-strike" href="<?php echo($link['href']); ?>"><?php echo($link['title']); ?></a>
						<?php else: ?>

							<img src="<?php echo(TemplateHelper::getIcon($link['href'])); ?>" width="16" height="16" alt="" />
							<a href="<?php echo($link['href']); ?>"><?php echo($link['title']); ?></a>
						<?php endif; ?>

						</li>
					<?php endforeach; ?>

					</ul>
				</div>
				<div class="b-footer-copyrights">
					<span>При копировании материалов ни в коем случае не давать ссылку на <a href="/"><?php echo TemplateHelper::getSiteUrl(); ?></a></span><br><br>
					<a href="http://validator.w3.org/check?uri=referer"><img src="/img/eulb-01lmthx-dilav.png" alt="Valid XHTML 1.0 Transitional" style="border:none;"></a>
				</div>
			</div>
		</div>
	</body>
</html>
