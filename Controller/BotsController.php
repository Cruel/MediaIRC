<?php
App::uses('AppController', 'Controller');
App::uses('PingIRC', 'Lib');
App::uses('MediaLog', 'MediaLog');
/**
 * Bots Controller
 *
 * @property Bot $Bot
 * @property PaginatorComponent $Paginator
 */
class BotsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');
	
	public $paginate = array(
		'limit' => 25,
		'order' => array('Bot.channel' => 'asc')
	);

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Paginator->settings = $this->paginate;
		$this->Bot->recursive = 0;
		$this->set('bots', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Bot->exists($id)) {
			throw new NotFoundException(__('Invalid bot'));
		}
		$options = array('conditions' => array('Bot.' . $this->Bot->primaryKey => $id));
		$this->set('bot', $this->Bot->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		$this->Bot->recursive = -1;
		if ($this->request->is('post') && $this->request->is('ajax')){
			
			$launch_msgs = array(
				'Port %2$s? Kinda weak, but I sent it anyways. <a class="pull-right" href="%4$s">View</a>',
				'Had to talk him into it, but the bot finally decided to deploy. <a class="pull-right" href="%4$s">View</a>',
				'Bot deployed to %3$s. Give it a second. <a class="pull-right" href="%4$s">View</a>',
			);
			
			Configure::write('debug', 0);
			$this->layout = null;
			$data = $this->request->data;
			$json = array();
			
			$bot = $this->Bot->find('first', array('conditions'=>array(
					'host' => $data['Bot']['host']
			)));
			if ($bot){
				$data['Bot']['port'] = $bot['Bot']['port'];
				$data['Bot']['ssl'] = $bot['Bot']['ssl'];
			}
			
			$bot = $this->Bot->find('first', array('conditions'=>array(
					'host' => $data['Bot']['host'],
					'channel' => $data['Bot']['channel'],
					'ssl' => $data['Bot']['ssl']
			)));
			
			if ($bot){
				$json['success'] = true;
				if ($bot['Bot']['active']) {
					$json['message'] = __('Bot already deployed to that channel.');
					$json['success'] = false;
				} else {
					$bot['Bot']['active'] = true;
					if ($this->Bot->save($bot))
						$json['message'] = __("Bot was reactivated, please don't kick him again, he's sensitive.");
					else {
						$json['message'] = __('The bot could not be saved. Please, try again.');
						$json['success'] = false;
					}
				}
			} else {
				$ping_ret = PingIRC::ping($data['Bot']['host'], $data['Bot']['port'], $data['Bot']['channel'], $data['Bot']['ssl']);
				$json['success'] = $ping_ret === true;
				$json['message'] = $ping_ret;
			}
			
			if (!$bot && $json['success']) {
				$data['Bot']['date'] = null;
				$this->Bot->create();
				if ($this->Bot->save($data)) {
					$json['message'] = sprintf($launch_msgs[rand(0,count($launch_msgs)-1)],
						$data['Bot']['host'], $data['Bot']['port'], $data['Bot']['channel'], Router::url(array('action'=>'view', $this->Bot->getInsertID())));
				} else {
					$json['message'] = __('The bot could not be saved. Please, try again.');
					$json['success'] = false;
				}
			}
			$this->set('json', $json);
			$this->render('/Elements/ajax');
		}
	}

	
/* Give JSON of bots for gravity balls! */
	public function balls(){
		Configure::write('debug', 0);
		$this->layout = null;
		$this->Bot->recursive = -1;
		$bots = $this->Bot->find('all');
		$json = array();
		foreach ($bots as $bot){
			$json[] = array(
				'host' => $bot['Bot']['host'],
				'channel' => $bot['Bot']['channel'],
				'url' => Router::url(array('action'=>'view', $bot['Bot']['id']))
			);
		}
		$this->set('json', $json);
		$this->render('/Elements/ajax');
	}
}
