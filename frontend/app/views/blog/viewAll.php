			<?php include(dirname(__FILE__) .'/chunks/blog_control.php'); ?>

				<div class="b-post-added-notify" id="post_notify">
					Доступны новые записи, <a href="/news/<?php if ($this -> getParameter('section') == 'all'): ?>all/<?php endif; ?>">перезагрузите страницу</a>, чтобы увидеть их.
				</div>

			<?php if (empty($posts)): ?>

				<div class="b-static">
					<h1>На этой странице нет постов</h1>
					<p>Либо их действительно нет, либо опять все сломалось.</p>
				</div>
			<?php else: ?>
				<?php foreach($posts as $post): ?>
				<div class="b-blog-entry<?php if ($post['hidden']): ?> m-hidden<?php endif; ?>" id="post_<?php echo($post['id']); ?>">
					<div class="b-blog-entry_b-header">
						<img src="<?php echo($post['link'] ? TemplateHelper::getIcon($post['link']) : '/ico/favicons/'.HOME_FAVICON.ICONS_VERSION); ?>" width="16" height="16" alt="" />
					<?php if($post['pinned']): ?>
						<img src="/ico/pinned.png" width="16" height="16" alt="Прикрепленный пост" class="b-blog-entry_b-header_b-pinned-icon" />
					<?php endif; ?>

					<?php if($post['category']): ?>
						<a href="/news/cat/<?php echo(TemplateHelper::BlogCategory($post['category'], 'name')); ?>/" class="b-blog-entry_b-header_m-category">
							<?php echo(TemplateHelper::BlogCategory($post['category'], 'title')); ?></a>
						&rarr;
					<?php endif; ?>

						<a href="<?php echo($post['link'] ? $post['link'] : '/news/res/'. $post['id'] .'/'); ?>" <?php if(!empty($post['link'])): ?>class="m-external" rel="nofollow noopener noreferrer"<?php endif;?>>
							<?php echo($post['title']); ?>

						</a>

					</div>
					<?php $postlength = substr_count($post['text'], "<p>") + substr_count($post['text'], "<br") + (substr_count($post['text'], "<h1>") * 2); ?>
					<div class="b-blog-entry_b-body g-clearfix <?php if ($postlength > 10): ?>fold<?php endif; ?>">
							<?php echo($post['text']); ?>
						<?php if (strlen($post['text_full']) > 0): ?><p><a href="/news/res/<?php echo($post['id']); ?>/">Читать дальше</a></p><?php endif; ?>
                        <?php if ($this -> getParameter('section') == 'hidden'): ?><p><em><?php echo($post['special_comment']); ?></em></p><?php endif; ?>

					</div>
					<div class="b-blog-entry_b-info" id="post_<?php echo($post['id']); ?>_info">
						<span class="b-blog-entry_b-info_b-control">
							<a href="/news/fav/toggle/<?php echo($post['id']); ?>/" class="js-favorite-button">
								<img src="/ico/favorites<?php if(!Blog_BlogPostsModel::IsFavoritePost($post['id'])):?>-false<?php endif; ?>.png" width="16" height="16" alt="" id="post_<?php echo($post['id']); ?>_favorite" />
							</a>
							<a href="#" class="js-moderator-button g-hidden">
								<img src="/ico/settings.png" width="16" height="16" alt="<?php echo($post['id']); ?>" />
							</a>
							<a href="javascript:;" class="js-hide-link g-hidden"><img src="/ico/oh-my-eyes.png" width="16" height="16" alt="<?php echo($post['id']); ?>" /></a>
							<span>|</span>
						<?php if($post['rateable']): ?>
							<?php $rate_order = explode('|', RATE_BUTTON_ORDER); ?>
							<a href="/news/res/<?php echo($post['id']); ?>/rate_post/<?= $rate_order[0] ?>/" class="js-rate-<?= $rate_order[0] ?>-button">
								<img src="/ico/<?= $rate_order[0]=='up' ? 'plus' : 'minus' ?>_grey.gif" width="9" height="11" alt="" />
							</a>
						<?php if($post['rate'] >= 0): ?>

							<strong class="g-green js-rate"><?php echo($post['rate']); ?></strong>
						<?php else: ?>

							<strong class="g-red js-rate"><?php echo($post['rate']); ?></strong>
						<?php endif; ?>

							<a href="/news/res/<?php echo($post['id']); ?>/rate_post/<?= $rate_order[1] ?>/" class="js-rate-<?= $rate_order[1] ?>-button">
								<img src="/ico/<?= $rate_order[1]=='up' ? 'plus' : 'minus' ?>_grey.gif" width="9" height="11" alt="" />
							</a>
							<span>|</span>
						<?php endif; ?>

							<span><?php echo(TemplateHelper::date('d M Y @ H:i:s', $post['created_at'])); ?></span>
							<span>|</span>
							<span>
								<a href="/news/res/<?php echo($post['id']); ?>/" class="g-disabled">
									№<?php echo($post['id']); ?></a>
							</span>
							<?php if($post['author'] != 'anonymous'): $author = HomeBoardHelper::getBoard($post['author']); ?>
							<span>|</span>
							<a href="https://<?php echo($post['author']); ?>/" class="b-comment_b-homeboard" title="Аноним выбрал принадлежность «<?php echo($author[1]); ?>»">
								<img src="/ico/homeboards/<?php echo($author[0]); ?>" width="16" height="16" alt="" />
							</a>
							<?php endif; ?>
						</span>
						<span class="b-blog-entry_b-info_b-link">
							<a href="/news/res/<?php echo($post['id']); ?>/">
								<?php if ($post['closed']): ?>Обсуждение закрыто (<?php echo($post['comments']); ?>)<?php else: ?>Обсуждение: <span class="js-comments<?php TemplateHelper::isPostUpdated($post) and print(' g-bold'); ?>"><?php echo($post['comments']); ?></span><?php endif; ?>
							</a>
						</span>
					</div>
				</div>
				<?php endforeach; ?>
				<?php include(dirname(__FILE__) .'/chunks/blog_pager.php'); ?>
				<?php if ($this -> getParameter('section') == 'all'): ?><p style="text-align:center;font-size:0.6em;position:relative;"><a href="/news/all/reset/">отметить все, как прочитанное</a> <a href="/service/modlog/" style="position:absolute;right:40px;">[модлог]</a></p><?php endif; ?>
			<?php endif; ?>
