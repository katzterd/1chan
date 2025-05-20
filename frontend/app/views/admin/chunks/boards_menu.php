<ul class="sideNav">
	<li><h3>Доски:</h3></li>
	<li><a href="/admin/boardAdd"<?php if ($this -> getParameter('submenu') == "board_add"): ?> class="active"<?php endif; ?>>Добавить доску</a></li>
	<li><a href="/admin/boards"<?php if ($this -> getParameter('submenu') == "board_list"): ?> class="active"<?php endif; ?>>Список досок</a></li>
</ul>