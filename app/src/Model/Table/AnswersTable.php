<?php


namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * Answers Model
 */
class AnswersTable extends Table
{
  /**
   * @inheritdoc
   */
  public function initialize(array $config)
  {
    parent::initialize($config);

    $this->setTable('answers');
    $this->setDisplayField('id');
    $this->setPrimaryKey('id');

    $this->addBehavior('Timestamp');

    $this->belongsTo('Questions', [
      'foreignKey' => 'question_id',
      'joinType' => 'INNER'
    ]);
  }
}