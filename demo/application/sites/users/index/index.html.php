user list:

<table>
	<tr>
		<th>Id</th>
		<th>Name</th>
		<th>&nbsp;</th>
	</tr>
	<?php foreach($this->users as $user): ?>
		<tr>
			<td><?php e($user->getId()); ?></td>
			<td><?php e($user->getName()); ?></td>
			<td><a href="<?php e($this->url(array('userId' => $user->getId(), 'userName' => $user->getName()), 'user')); ?>">Show</a></td>
		</tr>
	<?php endforeach; ?>
</table>