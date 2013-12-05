<div class="bots index">
	<h2 class="text-center"><?php echo __('Bots'); ?></h2>
	
	<table class="table table-striped">
		<thead><tr>
			<th><?php echo __('Channel') ?></th>
			<th><?php echo __('Server') ?></th>
		</tr></thead>
		<tbody>
		<?php foreach ($bots as $bot): ?>
			<tr>
				<td><?php echo $this->Html->link($bot['Bot']['channel'], array('action' => 'view', $bot['Bot']['id'])); ?>&nbsp;</td>
				<td><?php echo h($bot['Bot']['server']); ?>&nbsp;</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	
	<?php echo $this->element('pagination') ?>
</div>
