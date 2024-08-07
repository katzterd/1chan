<h2><a href="#">Первый канал</a> &raquo; <a href="#" class="active">Доски</a></h2>
<style>
	.board-options {
		display: none;
	}
	.options-reveal + .board-options {
		display: table-row;
	}
	.options-reveal td {
		border: none!important;
	}
	.board-list-btn {
		float: none;
		display: inline-block;
		background: #eee;
		height: 1.66em;
		padding: 0 6px;
		border: 1px solid #b5b5b5;
		border-radius: 2px;
	}
	.board-list-btn:hover {
		border-color: #707070;
	}
	.blb-delete {
		background: #ffdbdb;
		border-color: #e75252;
		color: #d10000;
		font-weight: bold;
	}
	.blb-delete:hover {
		border-color: #6b0000;
		background: #d10000;
		color: #fff;
	}
</style>
<div id="main">
	<?php if (isset($errors)): ?>
		<?php foreach($errors as $err): ?>
			<p class="popup-msg pm-error"><?php echo $err; ?></p>
		<?php endforeach; ?>
	<?php endif; ?>
	<?php if (isset($success)): ?>
		<p class="popup-msg pm-succ"><?php echo $success; ?></p>
	<?php endif; ?>
	<h3>Список досок:</h3>
	<table cellpadding="0" cellspacing="0" style="margin-bottom: 1em">
		<?php if (empty($boards)) : ?>
			<td>Нет досок для отображения. <a href="/admin/boardAdd">Добавить</a></td>
		<?php else: ?>
			<?php foreach($boards as $i => $board): ?>
				<tr<?php if(++$i % 2): ?> class="odd"<?php endif; ?>>
					<td>
						<b>/<?php echo $board["title"] ?>/</b> – 
						<?php echo $board["description"] ?>
						<?php if ($board["hidden"]): ?><span class="label">Скрытая</span><?php endif; ?>
					</td>
					<td class="action">
						<a href="/<?php echo $board["title"] ?> ?>/" target="_blank" class="view">View</a>
						<a href="#" class="edit">Edit/Delete...</a>
					</td>
				</tr>
				<tr class="board-options">
					<td>
						<input form="board-<?php echo $i ?>" name="description" type="text" class="text-long" value="<?php echo $board["description"] ?>" required pattern="[\S ]{1,20}" minlength="1" maxlength="20"/>
					</td>
					<td style="text-align: right;">
						<form id="board-<?php echo $i ?>" action="/admin/boards" method="post" style="display: contents">
							<input name="title" type="hidden" value="<?php echo $board["title"] ?>">
							<label style="display: inline-block; line-height: 12px; margin-right: 3em;"><input name="hidden" type="checkbox" value="1" class="jNiceCheckbox" <?php echo $board["hidden"] ? "checked" : "" ?>/> Скрытая</label>
							<button type="submit" name="action" value="edit" class="board-list-btn">Отредактировать</button>
							<button type="submit" name="action" value="delete" class="board-list-btn blb-delete">Удалить</button>
						</form>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</table>
</div>
<script>
	$('a.edit').click(function(ev) {
		ev.preventDefault()
		$(this).parents('tr').toggleClass('options-reveal')
	})
</script>
<?php if (@$form_submitted) : ?><script>
	if ( window.history.replaceState ) {
		window.history.replaceState( null, null, window.location.href )
	}
</script><?php endif; ?>