<?php

namespace App\Test\TestCase\Controller;

use App\Model\Entity\Answer;
use App\Model\Entity\Question;
use App\Model\Entity\User;
use App\Model\Table\QuestionsTable;
use App\Model\Table\UsersTable;
use Cake\ORM\ResultSet;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;


/**
 * App\Controller\QuestionsController Test Case
 *
 * @property QuestionsTable $Questions
 * @property UsersTable $Users
 */
class QuestionsControllerTest extends IntegrationTestCase
{
  /**
   * fixtures
   *
   * @var array
   */
  public $fixtures = [
    'app.Answers',
    'app.Questions',
    'app.Users',
  ];

  /**
   * @inheritdoc
   */
  public function setUp()
  {
    /**
     * IntegrationTestCaseでは、TestCaseを継承している
     * abstract class IntegrationTestCase extends TestCase
     *  {
     *    use IntegrationTestTrait;
     *  }
     *
     * 継承元のTestCase.phpはこんな感じ
     * abstract class TestCase extends BaseTestCase
     * {
     *  parent::setUp()
     *
     *  ~~~色々処理を書き加えている~~~
     * }
     *
     * 継承元のBaseTestCaseはBaseTestCaseを継承していて、use PHPUnit\Framework\TestCase as BaseTestCase; と定義されている。PHPUnitの内部？
     *
     */
    parent::setUp();

    $this->Questions = TableRegistry::getTableLocator()->get('Questions');
    $this->Users = TableRegistry::getTableLocator()->get('Users');
  }

  /**
   * @inheritdoc
   */
  public function tearDown()
  {
    unset($this->Questions);
    unset($this->Users);

    parent::tearDown();
  }

  /**
   * 質問一覧画面のテスト
   *
   * @return void
   */
  public function testIndex()
  {
    /** Arrange, Act, Assert */

    /** Arrange：なし */


    /** Act 質問画面一覧にアクセスする */
    $this->get('/questions');


    /** Assert */

    // 正常にアクセスできるかを検査する
    $this->assertResponseOk('質問一覧画面が正常にレスポンスを返せていない');

    /** @var ResultSet $actual */
     // ビューに渡されている$questions変数を取得する（コントローラのテストとしては、ビューに渡される情報が正しければ良いのでこの変数の中身をチェックしていく）
    $actual = $this->viewVariable('questions');

    // 代表の一件をとって、内容が期待したものになっているかを検査する
    /** @var Question $sampleQuestion */
    $sampleQuestion = $actual->sample(1)->first();

    // assertInstanceOf($expected, $actual[, $message = ''])
    $this->assertInstanceOf(
      Question::class,
      $sampleQuestion,
      'ビュー変数に質問がセットされていない'
    );
    $this->assertInstanceOf(
      User::class,
      $sampleQuestion->user,
      '質問にユーザーが梱包されていない'
    );
    $this->assertInternalType(
      'integer',
      $sampleQuestion->answered_count,
      '質問に回答数がついていない'
    );
  }

  /**
   * 質問詳細画面のテスト
   *
   * @return void
   */
  public function testView()
  {
    $this->markTestIncomplete('Not implemented yet.');
  }

  /**
   * 質問投稿画面のテスト
   *
   * @return void
   */
  public function testAdd()
  {
    $this->markTestIncomplete('Not implemented yet.');
  }

  /**
   * 質問削除機能のテスト
   *
   * @return void
   */
  public function testDelete()
  {
    $this->markTestIncomplete('Not implemented yet.');
  }
}