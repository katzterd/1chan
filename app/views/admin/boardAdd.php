<h2><a href="#">Первый канал</a> &raquo; <a href="#" class="active">Добавить доску</a></h2>
<div id="main">
	<?php if (isset($board_add_errors)): ?>
		<?php if (count($board_add_errors)): ?>
			<?php foreach($board_add_errors as $err): ?>
				<p class="popup-msg pm-error"><?php echo $err; ?></p>
			<?php endforeach; ?>
		<?php else: ?>
			<p class="popup-msg pm-succ">Доска /<?php echo $this->getParameter('added_board') ?>/ успешно добавлена</p>
		<?php endif; ?>
	<?php endif; ?>
	<form action="/admin/boardAdd" method="post" class="jNice">
		<h3>Свойства доски:</h3>
		<fieldset>
			<p><label>Директория:</label><input name="title" type="text" class="text-long" required pattern="[a-z_0-9]{1,10}" minlength="1" maxlength="10"/></p>
			<p><label>Описание:</label><input name="description" type="text" class="text-long" value="" required pattern="[\S ]{1,20}" minlength="1" maxlength="20"/></p>
			<p><label><input name="hidden" type="checkbox" value="1" class="jNiceCheckbox" /> Скрытая</label></p>
		</fieldset>

		<fieldset>
			<input type="submit" value="Добавить доску" />
		</fieldset>
	</form>
</div>
<?php if (@$form_submitted) : ?><script>
	if ( window.history.replaceState ) {
		window.history.replaceState( null, null, window.location.href )
	}
</script><?php endif; ?>