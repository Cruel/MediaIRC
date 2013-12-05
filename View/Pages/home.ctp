
<div class="home text-center">
<?php
	echo "<h2>".__('Welcome!')."</h2>";
	echo $this->Html->link(__("Find Bot"), array('controller' => 'bots', 'action' => 'index'), array(
		'class' => 'btn btn-lg btn-danger disabled'
	));
	echo $this->Html->link(__("Browse Bots"), array('controller' => 'bots', 'action' => 'index'), array(
		'class' => 'btn btn-lg btn-primary',
	));
	echo "<br>";
	echo $this->Html->link(__("Make Bot"), array('controller' => 'bots', 'action' => 'add'), array(
			'class' => 'btn btn-lg btn-success'
	));
?>
</div>