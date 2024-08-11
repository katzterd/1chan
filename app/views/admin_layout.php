<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="https://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>1chan | Администраторская</title>
<link href="/admin_style/css/transdmin.css" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript" src="/js/jquery-3.7.1.min.js"></script>
</head>

<body>
	<div id="wrapper">
		<?php include(dirname(__FILE__) .'/admin/chunks/main_menu.php'); ?>

        <div id="containerHolder">
			<div id="container">
        		<div id="sidebar">
                	<?php include(dirname(__FILE__) .'/admin/chunks/'. $this -> getParameter('menu') .'_menu.php'); ?>
                </div>

				<?php echo $content; ?>

                <div class="clear"></div>
            </div>
        </div>
    </div>
</body>
</html>
