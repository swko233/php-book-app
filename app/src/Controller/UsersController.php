<?php

namespace App\Controller;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
  /**
   * @inheritdoc
   */
  public function initialize()
  {
    parent::initialize();

    // 認証設定
    // $this->loadComponent('Auth');  ← 継承元のAppControllerのinitialize()でloadComponentしており、 parent::initialize();で呼び出しているので不要($this->loadComponent('Flash');と同様)
    $this->Auth->allow(['add']);
  }

  /**
   * ユーザー登録画面/ユーザー登録処理
   *
   * @return \Cake\Http\Response|null ユーザー登録後にログイン画面へ遷移する
   */
  public function add()
  {
    // UserTableクラスのメソッドを利用
    $user = $this->Users->newEntity();

    // Controllerクラスのプロパティ？に$requestがある？？
    if ($this->request->is('post')) {
      $user = $this->Users->patchEntity($user, $this->request->getData());

      if ($this->Users->save($user)) {
        $this->Flash->success('ユーザーの登録が完了しました');

        return $this->redirect(['controller' => 'Login', 'action' => 'index']);
      }
      $this->Flash->error('ユーザーの登録に失敗しました');
    }
    $this->set(compact('user'));
  }
}