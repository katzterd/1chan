<style>
	#main table tr td {
		padding: 0px 8px;
		vertical-align: middle;
	}
	#main table tr td input {
		width: 15ch;
	}
	.type-selector {
		width: 20ch;
	}
	.position-selector {
		width: 10ch;
	}
	.board-list-btn {
		float: none;
		display: inline-block;
		background: #eee;
		height: 21px;
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
	.template-entry {
		display: none;
	}
	.input-disabled {
		opacity: 0;
		pointer-events: none;
	}
</style>
<?php 
$template_entry = [
	"text" => "",
	"href" => "",
	"class" => "",
	"template" => true
];
if (!is_array($top_panel)) {
	$top_panel = [[$template_entry], []];
}
else {
	$top_panel[0] []= $template_entry;
}
?>
<div id="main">
	<?php if (isset($errors)): ?>
		<?php foreach($errors as $err): ?>
			<p class="popup-msg pm-error"><?php echo $err; ?></p>
		<?php endforeach; ?>
	<?php endif; ?>
	<?php if (isset($success)): ?>
		<p class="popup-msg pm-succ"><?php echo $success; ?></p>
	<?php endif; ?>
	<h3>Верхняя панель:</h3>
	<table cellpadding="0" cellspacing="0">
		<thead>
			<td>Тип</td>
			<td>Текст</td>
			<td>URL</td>
			<td>Доп. класс(ы)</td>
			<td>Расположен</td>
			<td>Уд.</td>
		</thead>
		<tbody>
			<?php foreach($top_panel as $s => $section): ?>
				<?php $right = $s==1; ?>
				<?php foreach($section as $link): ?>
					<?php
						if (is_array($link)) {
							$tpl = @$link['template'] ?? false;
							$form = $tpl ? "" : " form='top-panel-form'" ;
							$special = false;
							$disabled = "";
							$required = " required";
						}
						else {
							$special = $link;
							$link = [
								"text" => "",
								"href" => "",
								"class" => ""
							];
							$form = " form='top-panel-form'";
							$disabled = " class='input-disabled'";
							$tpl = false;
							$required = "";
						}
					?>
					<tr class="entry<?= $tpl ? " template-entry" : ""?>">
						<td><select<?= $form ?> name="type[]" class="type-selector">
							<option value="link"<?= !$special ? ' selected' : '' ?>>Ссылка</option>
							<?php foreach (TemplateHelper::SPIECIAL_LINKS as $spl): ?>
								<option value="<?= $spl['id'] ?>"<?= ($special == $spl['id']) ? ' selected' : '' ?>><?= $spl['name'] ?></option>
							<?php endforeach; ?>
						</select></td>
						<td><input<?= $form ?> 
							type="text" 
							name="text[]" 
							placeholder="Текст" 
							class="required-if-enabled<?= $disabled ? ' input-disabled' : '' ?>" 
							value="<?= $link['text'] ?? "" ?>"
							<?= $required ?>></td>
						<td><input<?= $form ?> 
							type="url" 
							name="href[]" 
							placeholder="URL" 
							class="required-if-enabled<?= $disabled ? ' input-disabled' : '' ?>" 
							value="<?= $link['href'] ?? "" ?>"
							<?= $required ?>></td>
						<td><input<?= $form ?> 
							type="text" 
							name="class[]" 
							placeholder="+ Класс(ы)" 
							value="<?= $link['class'] ?? "" ?>"
							<?= $disabled ?>></td>
						<td><select<?= $form ?> name="lr[]" class="position-selector">
							<option value="l"<?= !$right ? 'selected' : ''?>>Cлева</option>
							<option value="r"<?= $right ? 'selected' : ''?>>Справа</option>
						</select></td>
						<td><button class="board-list-btn blb-delete" title="Удалить">✘</button></td>
					</tr>
				<?php endforeach; ?>
			<?php endforeach; ?>
		</tbody>
	</table>
	<form id="top-panel-form" action="/admin/topPanel" method="POST" style="display:contents">
		<fieldset>
			<input id="add-entry" type="submit" value="Добавить ссылку" />
			<input type="submit" value="Сохранить" />
		</fieldset>
	</form>
</div>
<?php if (@$form_submitted) : ?><script>
	if ( window.history.replaceState ) {
		window.history.replaceState( null, null, window.location.href )
	}
</script><?php endif; ?>
<script>
$('tbody')
.on('click', '.blb-delete', function(ev) {
	ev.preventDefault()
	$(this).parents('.entry').remove()
})
.on('change', 'select[name="type[]"]', function() {
	var $tr = $(this).parents('tr')
	var special = !($(this).val() == 'link')
	$tr.find('input').each(function() {
		$(this).toggleClass('input-disabled', special)
	})
	$tr.find('.required-if-enabled').each(function() {
		$(this).attr('required', special ? null : true)
	})
})
$('#add-entry').click(function(ev) {
	ev.preventDefault()
	$('.template-entry')
		.clone()
		.removeClass('template-entry')
		.appendTo('tbody')
		.show()
		.find('input, select')
		.each(function() {
			$(this).attr('form', 'top-panel-form')
		})
})
</script>