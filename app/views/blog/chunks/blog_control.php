			<div class="b-blog-panel g-clearfix">
					<ul>
						<li class="b-blog-panel_b-add-entry<?php if ($this -> getParameter('section') == 'add'): ?> b-blog-panel_m-active<?php endif; ?>">
							<a href="/news/add/">
								<img src="/ico/add-entry.png" width="16" height="16" alt="" />
								<span>Добавить запись</span>
							</a>
						</li>
						<li class="b-blog-panel_b-favorites<?php if ($this -> getParameter('section') == 'favorite'): ?> b-blog-panel_m-active<?php endif; ?>">
							<a href="/news/fav/">
								<img src="/ico/favorites.png" width="16" height="16" alt="" />
								<span>Избранные</span>
							</a>
						</li>
						<li class="b-blog-panel_b-approved<?php if ($this -> getParameter('section') == 'rated'): ?> b-blog-panel_m-active<?php endif; ?>">
							<a href="/news/">
								<img src="/ico/tick.png" width="16" height="16" alt="" />
								<span>Одобренные</span>
							</a>
						</li>
						<li class="b-blog-panel_b-all<?php if ($this -> getParameter('section') == 'all'): ?> b-blog-panel_m-active<?php endif; ?>">
							<a href="/news/all/">
								<span>Все</span>
							</a>
						</li>
					</ul>
					<div class="b-blog-panel_b-searchmenu">
						<img src="/ico/search.png" width="16" height="16" alt="" /> <a href="/news/search/">Поиск записей</a> |
						<a href="/news/cat/">Категории</a> |
						<a href="/news/last_comments/">Последние комментарии</a>
					</div>
					<?php
						switch($this -> getParameter('section')):
					 	case ('entry'):
					 ?>

					<div class="b-blog-panel_b-submenu">
						<img src="/ico/new.png" width="16" height="16" alt="" />
						<a href="javascript://" class="g-disabled" id="new_comments_link">К непрочитанным комментариям</a>
					</div>
					<?php break; ?>

					<?php
						case ('all'):
						case ('favorite'):
						case ('rated'):
						case ('category'):
						case ('hidden'):
					 ?>

					<div class="b-blog-panel_b-submenu">
						<img src="/ico/sort.png" width="16" height="16" alt="Сортировать" />
						<span>По дате:</span>
						<?php if($this -> getParameter('sortby') == "created_at"): ?>
							<span>создания</span>
						<?php else: ?>
							<a href="/news/sort/created_at/" class="g-dynamic">создания</a>
						<?php endif; ?>

						<span>|</span>
						<?php if($this -> getParameter('sortby') == "updated_at"): ?>
							<span>обновления</span>
						<?php else: ?>
							<a href="/news/sort/updated_at/" class="g-dynamic">обновления</a>
						<?php endif; ?>

					</div>
					<?php break; ?>
					<?php endswitch; ?>
				</div>
