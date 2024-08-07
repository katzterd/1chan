<h2><a href="#">Первый канал</a> &raquo; <a href="#" class="active">Иконки принадлежности</a></h2>
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
	.board-options input[type="text"] {
		width: 20ch;
	}
	td img {
		vertical-align: middle;
	}
	img[src=""] { 
		isplay: none 
	}
</style>
<?php function makeForm($icon_files, $domain="", $props=array("", "")) {
	$actions = "<input type='hidden' name='old-domain' value='$domain'>" . (
		($domain == "")
		? "<button type='submit' name='action' value='add' class='board-list-btn' title='Добавить'><b>+</b></button>"
		: "<button type='submit' name='action' value='edit' class='board-list-btn' title='Сохранить'>✔</button> " .
			(($domain == 'anonymous') ? "" : "<button type='submit' name='action' value='delete' class='board-list-btn blb-delete' title='Удалить'>✘</button>") );
	$options = "";
	foreach($icon_files as $f) {
		$selected = ($f == $props[0]) ? 'selected' : '';
		$options .= "<option value='$f' style='background-image: url(/ico/homeboards/$f)' $selected>$f</option>";
	}
	$disabled = $domain == 'anonymous' ? 'disabled' : '';
	return "<tr class='board-options'>
		<td><input $disabled placeholder='Домен' name='new-domain' form='hb-$domain' type='text' value='$domain' required></td>
		<td><select placeholder='Иконка' name='icon' form='hb-$domain' required>$options</select></td>
		<td><input placeholder='Имя' name='name' form='hb-$domain' type='text' value='{$props[1]}' required></td>
		<td><form action='/admin/homeBoards' id='hb-$domain' method='post' style='display:contents'>$actions</form></td>
	</tr>";
} ?>
<div id="main">
	<?php if (isset($errors)): ?>
		<?php foreach($errors as $err): ?>
			<p class="popup-msg pm-error"><?php echo $err; ?></p>
		<?php endforeach; ?>
	<?php endif; ?>
	<?php if (isset($success)): ?>
		<p class="popup-msg pm-succ"><?php echo $success; ?></p>
	<?php endif; ?>
	<h3>Иконки принадлежности</h3>
	<table style="margin-bottom: 1em">
		<th>
			<tr>
				<td>Домен</td>
				<td>Иконка</td>
				<td>Имя</td>
				<td>Действие</td>
			</tr>
		</th>
		<tbody>
			<?php foreach($homeboards as $domain => $props): ?>
				<tr>
					<td><a href="<?= 'https://' . $domain ?>"><?= $domain ?></a></td>
					<td><img width="16" height="16" src="<?= '/ico/homeboards/' . $props[0] ?>" alt="<?= $props[0] ?>"></td>
					<td><?= $props[1] ?></td>
					<td class="action">
						<a href="#" class="edit">Edit<?= ($domain == 'anonymous') ? '' : '/Delete' ?>...</a>
					</td>
				</tr>
				<?= makeForm($icon_files, $domain, $props) ?>
			<?php endforeach; ?>
			<tr>
				<td><h4>Новая принадлежность</h4></td>
				<td colspan="2"><img src="" width="16" height="16"></td>
				<td class="action">
					<a href="#" class="edit">Add...</a>
				</td>
			</tr>
			<?= makeForm($icon_files) ?>
		</tbody>
	</table>
</div>
<script>
	$('a.edit').click(function(ev) {
		ev.preventDefault()
		$(this).parents('tr').toggleClass('options-reveal')
	})
	$('select[name=icon]').change(function(ev) {
		$(this).parents('tr').prev().find('img').attr('src', '/ico/homeboards/' + $(this).val())
	})
</script>
<?php if (@$form_submitted) : ?><script>
	if ( window.history.replaceState ) {
		window.history.replaceState( null, null, window.location.href )
	}
</script><?php endif; ?>