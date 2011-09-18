<!DOCTYPE html>
<html>
	<head>
		<title>demo page</title>
		<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
		
		<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.3.0/build/cssreset/reset-min.css">
		<link rel="stylesheet" type="text/css" href="<?php e($this->css('styles.css')); ?>">		
	</head>
	<body>
		<ul id="navi">
			<li><a href="<?php e($this->url(null, 'default', true)); ?>" <?php if($this->getRouter()->getCurrentRoute()=='default' AND $this->getRouter()->get('module')=='index'){ echo 'class="active"'; } ?>>index</a></li>
			<li><a href="<?php e($this->url(array('module' => 'users'), 'default', true)); ?>" <?php if($this->getRouter()->getCurrentRoute()=='default' AND $this->getRouter()->get('module')=='users'){ echo 'class="active"'; } ?>>user list</a></li>
			<li><a href="<?php e($this->url(null, 'admin', true)); ?>" <?php if($this->getRouter()->getCurrentRoute()=='admin'){ echo 'class="active"'; } ?>>admin menu</a></li>
		</ul>

		<?php echo $this->submenu; ?>
		
		<div id="main">
			<?php echo $this->content; ?>
		</div>
	</body>
</html>