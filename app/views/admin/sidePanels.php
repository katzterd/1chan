<h2><a href="#">Первый канал</a> &raquo; <a href="#" class="active">Боковые панели</a></h2>
<div id="main">
	<form action="/admin/sidePanels" method="post" class="jsNice">
		<h3>Содержимое боковых панелей:</h3>
		<fieldset>
			<p>
				<label>Левая панель:</label>
				<textarea name="left" rows="5" style="width: 100%"><?= $this -> getParameter('left-side-bar') ?></textarea>
				<br><br>
				<label>Правая панель:</label>
				<textarea name="right" rows="5" style="width: 100%"><?= $this -> getParameter('right-side-bar') ?></textarea>
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