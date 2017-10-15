					<ul class="sideNav">
                    	<li><a href="/admin/staticAdd"<?php if ($this -> getParameter('submenu') == "static_add"): ?> class="active"<?php endif; ?>>Добавить страницу</a></li>
                    	<li><a href="/admin/staticPages"<?php if ($this -> getParameter('submenu') == "static_pages"): ?> class="active"<?php endif; ?>>Список страниц</a></li>
                    	<li><a href="/admin/staticFiles"<?php if ($this -> getParameter('submenu') == "static_files"): ?> class="active"<?php endif; ?>>Управление файлами</a></li>
                    </ul>
