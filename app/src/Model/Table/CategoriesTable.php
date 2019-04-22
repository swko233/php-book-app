<?php

namespace App\Model\Table;

/**
 * Tableクラスのエイリアスを作成(CakePHPのsrc/ORM/Table.phpを参照している、はず)
 * これにより、Tableと記述すればTableクラスにアクセスできる ※use Cake\ORM\Table; は use Cake\ORM\Table as Table; と同義なので
 *
 *
 * 試しにこんな風に書いても動作した ↓↓
 *
 * use Cake\ORM\Table as Tableuu; // TableクラスのインスタンスをTableuuという名前で定義
 * use Cake\Validation\Validator;
 *
 * class QuestionsTable extends Tableuu {}
 *
 */
use Cake\ORM\Table;

/**
 * Categories Model
 */
class CategoriesTable extends Table
{
  /**
   * @inheritdoc
   */
  public function initialize(array $config)
  {
    parent::initialize($config);

    $this->setTable('categories');
    $this->setDisplayField('id');
    $this->setPrimaryKey('id');

    $this->addBehavior('Timestamp');

    $this->belongsToMany('Questions', [
      'foreignKey' => 'question_id'
    ]);

  }
}
