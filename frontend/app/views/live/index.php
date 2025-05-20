<?php $links = $this -> getParameter('online_links', array()); ?>
				<div class="b-live-panel g-clearfix">
					<form action="/live/add/" method="post" id="add_link_form">
					<ul>
						<li class="b-live-panel_b-link">
							<input type="text" name="link" value="<?php echo @$live_form['link']; ?>" />
						</li>
						<li class="b-live-panel_b-description">
							<input type="text" name="description" value="<?php echo @$live_form['description']; ?>" />
							<input type="submit" value="" />
						</li>
					</ul>
					</form>

				</div>
			
				<div class="b-live_b-error" id="live_form_error"><?php echo(implode(', ', array_values($form_errors))); ?></div>

				<textarea id="template_link" style="display:none">
					<div class="b-live-entry" id="live_link_<%=id%>">
						<a href="/live/redirect/<%=id%>?to=<%=link%>" class="b-live-entry_b-description"><%=description%></a> &larr; <a href="#" class="b-live-entry_b-board"><%=category['title']%></a>
						<span class="b-live-entry_b-clicks">Переходов: <span class="js-live-clicks"><%=clicks%></span></span>
						<a href="#" class="js-remove-button g-hidden">
							<img src="/ico/remove.gif" width="16" height="16" alt="<%=id%>" />
						</a>
					</div>
				</textarea>

				<div class="b-static <?php if (!empty($links)): ?>g-hidden<?php endif; ?>" id="no_entries">
					<h1>Нет ни одной активной ссылки</h1>
					<p>За последние 24 часа не было добавлено ни одной ссылки.</p>
				</div>

				<div id="placeholder_link">
			<?php if (!empty($links)): ?>
				<?php foreach($links as $link): ?>
				<div class="b-live-entry" id="live_link_<?php echo($link['id']); ?>">
					<a href="/live/redirect/<?php echo($link['id']) ?>?to=<?php echo($link['link']); ?>" class="b-live-entry_b-description"><?php echo($link['description']); ?></a> &larr; <a href="#" class="b-live-entry_b-board"><?php echo($link['category']['title']); ?></a>
					<span class="b-live-entry_b-clicks">Переходов: <span class="js-live-clicks"><?php echo($link['clicks']); ?></span></span>
					<a href="#" class="js-remove-button g-hidden">
						<img src="/ico/remove.gif" width="16" height="16" alt="<?php echo($link['id']); ?>" />
					</a>
				</div>
				<?php endforeach; ?>
			<?php endif; ?>
                </div>
			
			<?php if(!$this -> getParameter('right_panel')): ?>
			    <div class="b-static" style="text-align: right">
			        <a href="/live/linksPanel/?status=on" class="g-dynamic js-open-right-panel">Показывать панель онлайн ссылок</a>
			    </div>
			<?php endif; ?>
			
				<div class="b-static" style="padding-top: 35px;font-size: 0.65em">
				    <p>
					    <em>Добавьте этот букмарклет в закладки и используйте на страницах, чтобы добавлять ссылки на них в этот раздел (перетащите на панель закладок для добавления):</em>
					    <a href="javascript:(function(){f=document.getElementsByTagName('frame');with((f.length && f[f.length-1].contentWindow)||window){d=prompt('%D0%92%D0%B2%D0%B5%D0%B4%D0%B8%D1%82%D0%B5 %D0%BE%D0%BF%D0%B8%D1%81%D0%B0%D0%BD%D0%B8%D0%B5 %D0%B4%D0%BB%D1%8F %D1%8D%D1%82%D0%BE%D0%B9 %D1%81%D1%81%D1%8B%D0%BB%D0%BA%D0%B8','');if (d){i=new Image(); i.onload=function(){document.body.appendChild(i);i.style.position='fixed';i.style.top='0px';i.style.left='0px';i.style.zIndex=99999;setTimeout(function(){i.parentNode.removeChild(i);},2000)};i.src='<?php echo WEB_DOMAIN ?>/live/addXS/?link='+encodeURIComponent(location.href)+'&description='+encodeURIComponent(d);}}})();" class="g-dynamic">Добавить ссылку в «Онлайн»</a><br />
					</p>
				</div>
