<div class="bots index">
	<h2><?php echo __('Bots'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('server'); ?></th>
			<th><?php echo $this->Paginator->sort('channel'); ?></th>
			<th><?php echo $this->Paginator->sort('active'); ?></th>
			<th><?php echo $this->Paginator->sort('date'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($bots as $bot): ?>
	<tr>
		<td><?php echo h($bot['Bot']['id']); ?>&nbsp;</td>
		<td><?php echo h($bot['Bot']['server']); ?>&nbsp;</td>
		<td><?php echo h($bot['Bot']['channel']); ?>&nbsp;</td>
		<td><?php echo h($bot['Bot']['active']); ?>&nbsp;</td>
		<td><?php echo h($bot['Bot']['date']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $bot['Bot']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $bot['Bot']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $bot['Bot']['id']), null, __('Are you sure you want to delete # %s?', $bot['Bot']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Bot'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Links'), array('controller' => 'links', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Link'), array('controller' => 'links', 'action' => 'add')); ?> </li>
	</ul>
</div>
