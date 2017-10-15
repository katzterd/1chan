        <ul id="mainNav">
        	<li><a href="/admin/posts"<?php if ($this -> getParameter('menu') == "posts"): ?> class="active"<?php endif; ?>>ПЕРВЫЙ КАНАЛ</a></li>
        	<li><a href="/admin/chats"<?php if ($this -> getParameter('menu') == "chats"): ?> class="active"<?php endif; ?>>ЧАТЫ</a></li>
        	<li><a href="/admin/staticPages"<?php if ($this -> getParameter('menu') == "static"): ?> class="active"<?php endif; ?>>СТАТИЧЕСКИЕ СТРАНИЦЫ</a></li>
        	<li class="logout"><a href="/admin/logout">ВЫХОД</a></li>
        </ul>
