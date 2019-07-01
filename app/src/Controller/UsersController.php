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

  /**
   * ユーザー編集画面/ユーザー情報更新処理
   *
   * @return \Cake\Http\Response|null ユーザー情報更新後に質問一覧画面へ遷移する
   */
  public function edit()
  {
    // フォームの初期値としてユーザー情報をセットする必要があるので、userをgetしてくる
    $user = $this->Users->get($this->Auth->user('id')); // get()やfind()で返されたEntityはisNew()の返り値がfalseになる

    // 更新処理
    if ($this->request->is('put')) {
      $user = $this->Users->patchEntity($user, $this->request->getData());

      // ここでは、EntityのisNewメソッドの戻り値がfalseになるため($userをget()で取得しているから > ドキュメント参照）、更新処理が行われる
      if ($this->Users->save($user)) {
        $this->Auth->setUser($user->toArray()); // セッションのユーザー情報を更新

        $this->Flash->success('ユーザー情報を更新しました');

        return $this->redirect(['controller' => 'Questions', 'action' => 'index']);
      }
      $this->Flash->error('ユーザー情報の更新に失敗しました');
    }
    // ユーザー情報をViewにセットして編集フォームを表示
    $this->set(compact('user'));
  }

  /**
   * パスワード更新画面/パスワード更新処理
   */
  public function password()
  {
    // パスワード更新画面では現状のユーザーの情報をセットする必要がないのでnewEntity()を使う
    $user = $this->Users->newEntity();

    // 更新処理
    // ビューに渡されたユーザーエンティティのisNew()の値がtrueのため、formからsubmitした時のリクエストメソッドがPOSTになる
    if ($this->request->is('post')) {

      // 現在ログインしているユーザー情報とフォームから渡ってきた情報を結合
      $user = $this->Users->get($this->Auth->user('id'));
      $user = $this->Users->patchEntity($user, $this->request->getData());

      if ($this->Users->save($user)) {
        $this->Auth->setUser($user->toArray()); // セッション情報をいまDBに保存した値に合わせる
        $this->Flash->success('パスワードを更新しました');

        return $this->redirect(['action' => 'edit']);
      }
      // 失敗したらもう一度password()のページを表示
      $this->Flash->error('パスワードの更新に失敗しました');
    }
    $this->set(compact('user'));
  }
}