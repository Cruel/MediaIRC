<?php
App::uses('AppModel', 'Model');
/**
 * Link Model
 *
 * @property Bot $Bot
 */
class Link extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'url';
	
	public $actsAs = array(
		'Upload.Upload' => array(
			'image' => array(
				'fields' => array('type' => null),
				'thumbnailSizes' => array(
					'thumb' => '300l'
				)
			)
		)
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'bot_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'url' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'author' => array(
				'notempty' => array(
						'rule' => array('notempty'),
						//'message' => 'Your custom message here',
						//'allowEmpty' => false,
						//'required' => false,
						//'last' => false, // Stop validation after this rule
						//'on' => 'create', // Limit validation to 'create' or 'update' operations
				),
		),
		'type' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'context' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Bot' => array(
			'className' => 'Bot',
			'foreignKey' => 'bot_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
