<div class="links form">
<?php echo $this->Form->create('Link'); ?>
	<fieldset>
		<legend><?php echo __('Edit Link'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('bot_id');
		echo $this->Form->input('url');
		echo $this->Form->input('image');
		echo $this->Form->input('type');
		echo $this->Form->input('context');
		echo $this->Form->input('date');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Link.id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('Link.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Links'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Bots'), array('controller' => 'bots', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Bot'), array('controller' => 'bots', 'action' => 'add')); ?> </li>
	</ul>
</div>
