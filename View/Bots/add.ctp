<h2 class="text-center"><?php echo __('Make Bot'); ?></h2>

	<?php
	echo $this->Form->create('Bot', array(
		'inputDefaults' => array(
			'class' => 'form-control',
			'label' => false,
			'div'=>'col-lg-8 col-lg-offset-2 form-group'
		),
	));
	echo $this->Form->input('host', array('placeholder' => 'Hostname (e.g. chat.freenode.net)'));
	echo $this->Form->input('port', array('placeholder' => 'Port (e.g. 6667)'));
	echo $this->Form->input('channel', array('placeholder' => 'Channel (e.g. #chat)'));
	echo $this->Form->input('ssl', array('type' => 'checkbox', 'label' =>  __('SSL'), 'class'=>'', 'div'=>'col-lg-4 col-lg-offset-3 checkbox'));
	
	echo '<div class="col-lg-6 col-lg-offset-3">';
	echo $this->Form->button(__('Launch').' <span class="glyphicon glyphicon-plane"></span>', array(
		'id' => 'btnLaunch',
		'type' => 'submit',
		'data-loading-text' => 'Could take a minute...',
		'class' => 'btn btn-lg btn-success btn-block',
// 		'div' => array('class' => 'col-lg-6 col-lg-offset-3'),
		'escape' => false
	));
	echo "</div>";
	
	echo $this->Form->end();
	
	
	?>