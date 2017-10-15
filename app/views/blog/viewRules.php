			<?php include(dirname(__FILE__) .'/chunks/blog_control.php'); ?>

				<div class="b-blog-form">
					<div class="b-static m-justify">
						<h1>Правила и советы для постеров Одинчана</h1>
						<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/PfYnvDL0Qcw" frameborder="0" allowfullscreen></iframe>
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
