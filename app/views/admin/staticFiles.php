<style>
	.icon {
		width: 16px;
		height: 16px;
		vertical-align: middle;
		margin-right: 1ch;
	}
	.disclaimer {
		padding: 8px 20px;
		display: block;
	}
</style>

<h2><a href="#">Первый канал</a> &raquo; <a href="#" class="active">Загруженные файлы</a></h2>

<div id="main">
	<?php if (isset($errors)): ?>
		<?php foreach($errors as $err): ?>
			<p class="popup-msg pm-error"><?php echo $err; ?></p>
		<?php endforeach; ?>
	<?php endif; ?>
	<?php if (isset($success)): ?>
		<p class="popup-msg pm-succ"><?php echo $success; ?></p>
	<?php endif; ?>

	<form action="/admin/staticFiles" method="post" enctype="multipart/form-data" class="jsNice">
		<h3>Загрузка нового файла:</h3>
		<fieldset>
			<p>
				<label>Назначение:</label>
				<select name="dir">
					<option value="/uploads/">Загрузить в /uploads</option>
					<option value="/ico/homeboards/">Загрузить как иконку принадлежности</option>
					<option value="/img/smilies/">Загрузить как смайлик</option>
				</select>
			</p>
			<p>
				<label>Имя файла (опционально)</label>
				<input type="text" name="name" placeholder="имя без расширения" />
			</p>
			<p>
				<label>Файл:</label>
				<input type="file" name="upload" />
				<input type="submit" value="Загрузить" />
			</p>
		</fieldset>
	</form>

	<h3>Файлы:</h3>
	<table cellpadding="0" cellspacing="0">
		<?php if (empty($files)): ?>
			<td>Нет файлов для отображения.</td>
		<?php else: ?>
		<?php $i = 0; ?>
		<?php foreach($files as $file): ?>
			<tr<?php if(++$i % 2): ?> class="odd"<?php endif; ?>>
				<td><?php echo $file['name']; ?></td>
				<td class="action"><a href="/uploads/<?php echo $file['name']; ?>" class="view">Download</a><a href="/admin/staticFilesDelete?name=<?php echo $file['name']; ?>" class="delete">Delete</a></td>
			</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</table>

	<h3>Иконки принадлежности:</h3>
	<b class="disclaimer">Только список файлов. Для редактирования списка иконок принадлежности перейтите <a href="/admin/homeBoards">по ссылке</a>.</b>
	<table cellpadding="0" cellspacing="0">
		<?php if (empty($homeboards)): ?>
			<td>Нет файлов для отображения.</td>
		<?php else: ?>
		<?php $i = 0; ?>
		<?php foreach($homeboards as $i => $file): ?>
			<tr<?php if(++$i % 2): ?> class="odd"<?php endif; ?>>
				<td><img class="icon" src="/ico/homeboards/<?= $file ?>" alt="<?= $file ?>"><?= $file ?></td>
				<td class="action">
					<a href="/ico/homeboards/<?= $file ?>" class="view">View</a>
					<a href="/admin/staticFilesDelete?type=homeboard&name=<?= $file ?>" class="delete">Delete</a>
				</td>
			</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</table>

	<h3>Смайлики:</h3>
	<table cellpadding="0" cellspacing="0">
		<?php if (empty($homeboards)): ?>
			<td>Нет файлов для отображения.</td>
		<?php else: ?>
		<?php $i = 0; ?>
		<?php foreach($smilies as $s): ?>
			<tr<?php if(++$i % 2): ?> class="odd"<?php endif; ?>>
				<td><img class="icon" src="/img/smilies/<?= $s['name'] ?>.<?= $s['ext'] ?>" alt="<?= $s['name'] ?>.<?= $s['ext'] ?>"><?= $s['name'] ?>.<?= $s['ext'] ?></td>
				<td class="action">
					<a href="/img/smilies/<?= $s['name'] ?>.<?= $s['ext'] ?>" class="view">View</a>
					<a href="/admin/staticFilesDelete?type=smiley&name=<?= $s ?>" class="delete">Delete</a>
				</td>
			</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</table>

	<br /><br />
</div>
<?php if (@$form_submitted) : ?><script>
	if ( window.history.replaceState ) {
		window.history.replaceState( null, null, window.location.href )
	}
</script><?php endif; ?>