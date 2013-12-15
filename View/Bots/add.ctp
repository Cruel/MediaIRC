<h2 class="text-center"><?php echo __('Make Bot'); ?></h2>

	<?php
	echo $this->Form->create('Bot', array(
		'inputDefaults' => array(
			'class' => 'form-control',
			'div' => array('class' => 'form-group'),
			'between' => '<div class="col-lg-7">',
			'after' => '</div>',
		),
		'class' => 'form-horizontal'
	));
	echo $this->Form->input('host', array(
			'placeholder' => 'e.g. chat.freenode.net',
			'label' => array(
					'text' => __('Hostname'),
					'class'=> 'col-lg-2 col-lg-offset-1 control-label'
			)));
	echo $this->Form->input('port', array(
			'placeholder' => 'e.g. 6667',
			'label' => array(
					'text' => __('Port'),
					'class'=> 'col-lg-2 col-lg-offset-1 control-label'
			)));
	echo $this->Form->input('channel', array(
			'placeholder' => 'e.g. #chat',
			'label' => array(
					'text' => __('Channel'),
					'class'=> 'col-lg-2 col-lg-offset-1 control-label'
			)));
	echo $this->Form->input('ssl', array(
			'type' => 'checkbox',
			'label' =>  __('SSL'),
			'class' => '',
			'style' => 'margin-bottom:20px',
			'div' => 'col-lg-4 col-lg-offset-4 checkbox'));
	
	$launch = $this->Form->button(__('Launch').' <span class="glyphicon glyphicon-plane"></span>', array(
		'id' => 'btnLaunch',
		'type' => 'submit',
		'data-loading-text' => 'Could take a minute...',
		'class' => 'btn btn-lg btn-success btn-block',
		'escape' => false
	));
	echo $this->Html->tag('div', $launch, array(
		'class' => 'col-lg-6 col-lg-offset-3',
	));
	
	echo $this->Form->end();
	
	
	?>