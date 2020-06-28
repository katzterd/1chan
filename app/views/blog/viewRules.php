			<?php include(dirname(__FILE__) .'/chunks/blog_control.php'); ?>

				<div class="b-blog-form">
					<div class="b-static m-justify">
						<h1>Раздел в процессе заполнения</h1>
						<p>
							В скором времени данный раздел сайта будет доступен.
						</p>
						<?php if($this -> getParameter('confirm')): ?>
						<form action="" method="post">
							<p>
								<label><input type="checkbox" name="accept" /> <em>Я ознакомился с вышеперечисленным и предупрежден о возможности
								того, что мои посты могут быть скрыты модератором, в случае нарушения этих правил.</em></label>
							</p>
							<input type="submit" value="Я согласен, перейти к созданию записи" />
						</form>
						<?php endif; ?>
					</div>
				</div>
