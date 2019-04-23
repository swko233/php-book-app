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
    $targetQuestionId = 1;

    $this->get("/questions/view/{$targetQuestionId}");

    // 正常にアクセスできるかのテスト
    $this->assertResponseOk('質問詳細画面が正常にレスポンスを返せていない');

    $actualQuestion = $this->viewVariable('question');
    $this->assertInstanceOf(
      Question::class,
      $actualQuestion,
      '対象の質問がビュー変数にセットされていない'
    );
    $this->assertInstanceOf(
      User::class,
      $actualQuestion->user,
      '対象の質問にユーザーがcontainされていない'
    );
    // URIで渡したidの質問がDBから取得できているかのテスト（idが含まれるURIについてのテストにはこれが必要）
    $this->assertSame(
      $targetQuestionId,
      $actualQuestion->id,
      '指定した質問が取得されていない'
    );

    /** @var ResultSet $actualAnswers */
    $actualAnswers = $this->viewVariable('answers');
    $this->assertContainsOnlyInstancesOf(
      Answer::class,
      $actualAnswers->toList(),
      '回答一覧が正しくビュー変数にセットされていない'
    );
    $this->assertInstanceOf(
      User::class,
      $actualAnswers->sample(1)->first()->user,
      '回答者情報がセットされていない'
    );

    /** @var ResultSet $actualAnswer */
    $actualAnswer = $this->viewVariable('newAnswer');
    $this->assertInstanceOf(
      Answer::class,
      $actualAnswer,
      '回答情報が正しくセットされていない'
    );
  }

  /**
   * 質問詳細画面のテスト(異常系) / 存在しない質問を表示しようとした時の確認
   *
   * @return void
   */
  public function testViewNotExists()
  {
    $targetQuestionId = 100;
    $this->get("/questions/view/{$targetQuestionId}");

    $this->assertResponseCode(404, '存在しない質問を表示しようとした時のレスポンスが正しくない');
  }

  /**
   * 質問投稿画面のテスト
   *
   * @return void
   */
  // ...Warning Error: Method App\View\Helper\FromHelper::end does not exist in [/var/www/html/app/vendor/cakephp/cakephp/src/View/Helper.php, line 139]???
  public function testAdd()
  {
    $this->login(); // 未実装。こんなメソッドを用意する

    $this->get('/questions/add');

    $this->assertResponseOk('質問投稿画面を開けていない');

    /** @var Question $actual */
    $actual = $this->viewVariable('question');
    $this->assertInstanceOf(
      Question::class,
      $actual,
      '質問のオブジェクトが正しくセットされていない'
    );
    $this->assertTrue(
      $actual->isNew(),
      'セットされている質問が新規データになっていない'
    );
  }

  /**
   * 質問投稿画面のテスト / 新規投稿時
   *
   * @return void
   */
  public function testAddPostSuccess()
  {
    $this->enableCsrfToken();
    $this->enableRetainFlashMessages(); // flashメッセージのテストのために必要。assertSession()と併用する。
    $this->login();

    $postData = [
      'body' => '質問があります!'
    ];
    $this->post('/questions/add', $postData);

    // $this->assertResponseOk('') ではなく、、
    $this->assertRedirect(
      ['controller' => 'Questions', 'action' => 'index'],
      '質問投稿完了時にリダイレクトが正しくかかっていない'
    );
    $this->assertSession(
      '質問を投稿しました',
      'Flash.flash.0.message',
      '投稿成功時のメッセージが正しくセットされていない'
    );
    // DBが増えることのテスト、、、は別のメソッドでテスト
    // 上記のメッセージが正しく表示されていれば、外から見た振る舞いとしてのコントローラの責務の範囲ではひとまず保存成功とみなしてよいため?
  }

  /**
   * 質問投稿画面のテスト / 作成されるコンテンツの確認
   *
   * @return void
   */
  public function testAddCreateContent()
  {
    $this->enableCsrfToken();
    $this->enableRetainFlashMessages();
    $auth = $this->login();

    $postData = [
      'body' => '質問があります!'
    ];
    $this->post('/questions/add', $postData);

    $actual = $this->Questions->find()->last(); // 最新のデータを取得して、中身を検証する
    $this->assertSame(
      ['body' => $postData['body'], 'user_id' => $auth['Auth']['User']['id']],
      $actual->extract(['body', 'user_id']),
      '投稿した内容通りに質問が作成されていない'
    );
  }

  /**
   * 質問投稿画面の検査 / 投稿エラー時
   *
   * @return void
   */
  public function testAddPostError()
  {
    $this->enableCsrfToken();
    $this->enableRetainFlashMessages();
    $this->login();

    $this->post('/questions/add', []);

    $this->assertResponseOk('成功のレスポンスが返ってこない');
    $this->assertSession(
      '質問の投稿に失敗しました',
      'Flash.flash.0.message',
      '投稿失敗時のメッセージが正しくセットされていない'
    );
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

  /**
   * 認証情報のセットを行うヘルパー
   *
   * @return array 認証情報
   */
  private function login()
  {
    $auth = ['Auth' => ['User' => $this->Users->find()->first()]];
    $this->session($auth);

    return $auth;
  }
}