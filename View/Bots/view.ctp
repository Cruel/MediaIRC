<h2><?php echo h($bot['Bot']['channel']); ?> <small><?php echo h($bot['Bot']['host']); ?></small></h2>

<div id="gallery">
<ul>
<?php 

foreach ($bot['Link'] as $link) {
	$log = MediaLog::loadModel($link);
	$author = $this->Html->tag('span', h($link['author']), array(
		'escape' => false,
		'data-toggle' => 'tooltip',
		'data-original-title' => nl2br(h($link['context'])),
	));
	$foot = $this->Html->tag('div', 'by '.$author, array(
		'class' => 'item-foot',
	));
	echo $this->Html->tag('li',
		$log->getHtml() . $foot,
		array(
			'class' => 'item item-'.$link['type'],
			'data-url' => $link['url']
		)
	);
}

?>
</ul>
</div>