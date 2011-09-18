<ul id="subnavi">
	<li><a href="<?php e($this->url(null, 'admin', true)); ?>" <?php if($this->getRouter()->getCurrentRoute()=='admin' AND $this->getRouter()->get('controller')=='index'){ echo 'class="active"'; } ?>>Admin index</a></li>
	<li><a href="<?php e($this->url(array('controller' => 'users'), 'admin', true)); ?>" <?php if($this->getRouter()->getCurrentRoute()=='admin' AND $this->getRouter()->get('controller')=='users'){ echo 'class="active"'; } ?>>Admin user list</a></li>
</ul>