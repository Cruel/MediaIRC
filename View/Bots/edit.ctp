<div class="bots form">
<?php echo $this->Form->create('Bot'); ?>
	<fieldset>
		<legend><?php echo __('Edit Bot'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('server');
		echo $this->Form->input('channel');
		echo $this->Form->input('active');
		echo $this->Form->input('date');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Bot.id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('Bot.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Bots'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Links'), array('controller' => 'links', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Link'), array('controller' => 'links', 'action' => 'add')); ?> </li>
	</ul>
</div>
