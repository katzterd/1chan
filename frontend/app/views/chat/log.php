				<div class="b-chat-panel g-clearfix">
					<ul>
						<li onclick="window.location='/chat/add/'" class="b-chat-panel_b-add-room<?php if ($this -> getParameter('section') == 'add'): ?> b-chat-panel_m-active<?php endif; ?>">
							<img src="/ico/add-chat.png" width="16" height="16" alt="" />
							<a href="/chat/add/">Добавить комнату</a>
						</li>
						<li onclick="window.location='/chat/'" class="b-chat-panel_b-all<?php if ($this -> getParameter('section') == 'all'): ?> b-chat-panel_m-active<?php endif; ?>">
							<img src="/ico/chat-rooms.png" width="16" height="16" alt="" />
							<a href="/chat/">Тематические чаты</a>
						</li>
						<li onclick="window.location='/chat/common/'" class="b-chat-panel_b-common<?php if ($this -> getParameter('section') == 'common'): ?> b-chat-panel_m-active<?php endif; ?>">
							<img src="/ico/chat-common.png" width="16" height="16" alt="" />
							<a href="/chat/common/">Общий чат</a>
						</li>
					</ul>
				</div>

                <?php if($room): ?>
				<div class="b-chat" id="room_<?php echo($room['room_id']); ?>">
					<div class="b-chat_b-header">
						<div class="b-chat_b-header_b-title"><?php echo($room['title']); ?></div>
						<div class="b-chat_b-header_b-statistics">Лог комнаты чата</div>
					</div>
					<div class="b-chat_b-messages m-log g-clearfix">
					<?php if($channel == false): ?>
						<div class="b-chat_b-message m-info">
							<div class="b-chat_b-message_b-body">
							<form action="" method="post">
								<p>
									Для просмотра логов нужно ввести пароль: <input type="text" name="password" value="" /> <input type="submit" value="Вход" />
								</p>
							</form>
							</div>
						</div>
				    <?php else: ?>
				        <?php echo($log); ?>
					<?php endif; ?>
					</div>
				</div>
				<?php else: ?>
					<div class="b-static"> 
						<h1>Запрашиваемая комната не найдена</h1> 
						<p> 
							Либо она не существовала, либо была удалена модератором.
						</p> 
					</div>
				
			    <?php endif; ?>
