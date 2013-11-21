<?php
App::uses('AppController', 'Controller');
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

/**
 * index method
 *
 * @return void
 */
	public function index() {
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
		if ($this->request->is('post')) {
			$this->Bot->create();
			if ($this->Bot->save($this->request->data)) {
				$this->Session->setFlash(__('The bot has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The bot could not be saved. Please, try again.'));
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Bot->exists($id)) {
			throw new NotFoundException(__('Invalid bot'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Bot->save($this->request->data)) {
				$this->Session->setFlash(__('The bot has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The bot could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Bot.' . $this->Bot->primaryKey => $id));
			$this->request->data = $this->Bot->find('first', $options);
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Bot->id = $id;
		if (!$this->Bot->exists()) {
			throw new NotFoundException(__('Invalid bot'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Bot->delete()) {
			$this->Session->setFlash(__('Bot deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Bot was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
