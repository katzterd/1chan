<h2><a href="#">Первый канал</a> &raquo; <a href="#" class="active">Ссылки</a></h2>
<div id="main">
	<form action="/admin/favicon" method="post" class="jsNice">
		<h3>Установка фавикона:</h3>
		<fieldset>
			<p>
				<label>Код для &lt;head&gt;:</label>
				<textarea name="favicon" rows="5" style="width: 100%"><?= $this -> getParameter('favicon') ?></textarea>
			</p>
		</fieldset>
		<fieldset>
			<input type="submit" value="Отредактировать" />
		</fieldset>
	</form>
</div>
<?php if (@$form_submitted) : ?><script>
	if ( window.history.replaceState ) {
		window.history.replaceState( null, null, window.location.href )
	}
</script><?php endif; ?>