<?php

namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * Users Model
 */
Class UsersTable extends Table
{
  /**
   * @inheritdoc
   */
  public function initialize(array $config)
  {
    // initializeはTableクラスのstaticメソッド？
    parent::initialize($config);

    $this->setTable('users');
    $this->setDisplayField('id');
    $this->setPrimaryKey('id');

    $this->addBehavior('Timestamp');
  }
}