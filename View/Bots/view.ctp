<h2><?php echo h($bot['Bot']['channel']); ?> <small><?php echo h($bot['Bot']['host']); ?></small></h2>


<?php
	foreach ($bot['Link'] as $link): ?>
	<div><?php echo $this->Html->link($link['url']); ?></div>
<?php endforeach; ?>