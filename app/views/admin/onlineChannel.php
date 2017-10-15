				<h2><a href="#">Первый канал</a> &raquo; <a href="#" class="active">Каналы</a></h2>

                <div id="main">
                	<form action="/admin/onlineChannel" method="post" class="jsNice">
                		<h3>Каналы:</h3>
						<fieldset>
							<p><label>Для работы онлайн-ссылок впишите следующее:</label>
							<label>Интернеты :|: http\:\/\/([^\]]+) :|: https://<?php echo TemplateHelper::getSiteUrl(); ?>/</label>
							<label>Интернеты :|: https\:\/\/([^\]]+) :|: https://<?php echo TemplateHelper::getSiteUrl(); ?>/</label>
							<textarea name="channels" rows="5"><?php echo($channels); ?></textarea></p>
							<label>(мне было лень делать полностью независимые от адреса ссылки, поэтому довольствуйтесь этим костылём)</label>
						</fieldset>
						<fieldset>
							<input type="submit" value="Обновить список" />
						</fieldset>
					</form>
                </div>
