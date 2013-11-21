<div class="bots view">
<h2><?php echo __('Bot'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($bot['Bot']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Server'); ?></dt>
		<dd>
			<?php echo h($bot['Bot']['server']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Channel'); ?></dt>
		<dd>
			<?php echo h($bot['Bot']['channel']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Active'); ?></dt>
		<dd>
			<?php echo h($bot['Bot']['active']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Date'); ?></dt>
		<dd>
			<?php echo h($bot['Bot']['date']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Bot'), array('action' => 'edit', $bot['Bot']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Bot'), array('action' => 'delete', $bot['Bot']['id']), null, __('Are you sure you want to delete # %s?', $bot['Bot']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Bots'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Bot'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Links'), array('controller' => 'links', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Link'), array('controller' => 'links', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Links'); ?></h3>
	<?php if (!empty($bot['Link'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Bot Id'); ?></th>
		<th><?php echo __('Url'); ?></th>
		<th><?php echo __('Image'); ?></th>
		<th><?php echo __('Type'); ?></th>
		<th><?php echo __('Context'); ?></th>
		<th><?php echo __('Date'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($bot['Link'] as $link): ?>
		<tr>
			<td><?php echo $link['id']; ?></td>
			<td><?php echo $link['bot_id']; ?></td>
			<td><?php echo $link['url']; ?></td>
			<td><?php echo $link['image']; ?></td>
			<td><?php echo $link['type']; ?></td>
			<td><?php echo $link['context']; ?></td>
			<td><?php echo $link['date']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'links', 'action' => 'view', $link['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'links', 'action' => 'edit', $link['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'links', 'action' => 'delete', $link['id']), null, __('Are you sure you want to delete # %s?', $link['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Link'), array('controller' => 'links', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
