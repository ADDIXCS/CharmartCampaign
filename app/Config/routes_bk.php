<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 */

/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
  // Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
  // Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

/**
 * OAuth認証
 */
Router::connect('/oauth/facebook/callback',
    ['controller' => 'oauth', 'action' => 'facebookCallback']);
Router::connect('/oauth/facebook/login',
    ['controller' => 'oauth', 'action' => 'facebookLogin']);
Router::connect('/oauth/twitter/callback',
    ['controller' => 'oauth', 'action' => 'twitterCallback']);
Router::connect('/oauth/twitter/login',
    ['controller' => 'oauth', 'action' => 'twitterLogin']);

/**
 * ルート、ログイン
 */
  Router::connect('/admin',
    ['controller' => 'admin', 'action' => 'index', 'admin' => true]);
  Router::connect('/admin/login',
    ['controller' => 'login', 'action' => 'login', 'admin' => true]);
  Router::connect('/admin/logout',
    ['controller' => 'login', 'action' => 'logout', 'admin' => true]);
  Router::connect('/admin/:action',
    ['controller' => 'admin', 'admin' => true],
    ['action' => 'campaigns|changelog']);
/**
 * アカウント
 */
  Router::connect('/admin/:accountName/accounts',
    ['controller' => 'accounts', 'admin' => true],
    ['pass' => ['accountName']]);
  Router::connect('/admin/:accountName/accounts/add',
    ['controller' => 'accounts', 'action' => 'add', 'admin' => true],
    ['pass' => ['accountName']]);
  Router::connect('/admin/:accountName/edit',
    ['controller' => 'accounts', 'action' => 'edit', 'admin' => true],
    ['pass' => ['accountName']]);
  Router::connect('/admin/:accountName/delete',
    ['controller' => 'accounts', 'action' => 'delete', 'admin' => true],
    ['pass' => ['accountName']]);
/**
 * ダッシュボード
 */
  Router::connect('/admin/:accountName/dashboard',
    ['controller' => 'dashboard', 'admin' => true],
    ['pass' => ['accountName']]);
/**
 * オーディエンス
 */
  Router::connect('/admin/:accountName/audiences',
    ['controller' => 'audiences', 'admin' => true],
    ['pass' => ['accountName']]);
  Router::connect('/admin/:accountName/audiences/lists/add',
    ['controller' => 'audiences', 'action' => 'add_list', 'admin' => true],
    ['pass' => ['accountName']]);
  Router::connect('/admin/:accountName/audiences/lists/:listId',
    ['controller' => 'audiences', 'action' => 'lists', 'admin' => true],
    ['pass' => ['accountName', 'listId']]);
  Router::connect('/admin/:accountName/audiences/lists/:listId/edit',
    ['controller' => 'audiences', 'action' => 'edit_list', 'admin' => true],
    ['pass' => ['accountName', 'listId']]);
  Router::connect('/admin/:accountName/audiences/csv',
    ['controller' => 'audiences', 'action' => 'csv', 'admin' => true],
    ['pass' => ['accountName']]);
  Router::connect('/admin/:accountName/audiences/:audienceId',
    ['controller' => 'audiences', 'action' => 'view', 'admin' => true],
    ['pass' => ['accountName', 'audienceId']]);
/**
 * キャンペーン
 */
  // 一覧
  Router::connect('/admin/:accountName/campaigns',
    ['controller' => 'campaigns', 'admin' => true],
    ['pass' => ['accountName']]);
  // 追加
  Router::connect('/admin/:accountName/campaigns/add/:campaignType',
    ['controller' => 'campaigns', 'action' => 'add', 'admin' => true],
    ['pass' => ['accountName', 'campaignType']]);
  // 個別トップ（インサイト）
  Router::connect('/admin/:accountName/campaigns/:id',
    ['controller' => 'campaigns', 'action' => 'view', 'admin' => true],
    ['pass' => ['accountName', 'id']]);
  // 編集
  Router::connect('/admin/:accountName/campaigns/:id/edit/:editType',
    ['controller' => 'campaigns', 'action' => 'edit', 'admin' => true],
    ['pass' => ['accountName', 'id', 'editType']]);
  // アイテム編集
  Router::connect('/admin/:accountName/campaigns/:id/edit/:editType/:objectId',
    ['controller' => 'campaigns', 'action' => 'edit', 'admin' => true],
    ['pass' => ['accountName', 'id', 'editType', 'objectId']]);
  // 公開フラグ変更
  Router::connect('/admin/:accountName/campaigns/:id/publish',
    ['controller' => 'campaigns', 'action' => 'publish', 'admin' => true],
    ['pass' => ['accountName', 'id']]);
  // アイテムの公開フラグ変更
  Router::connect('/admin/:accountName/campaigns/:id/publish/items/:itemId',
    ['controller' => 'campaigns', 'action' => 'publish_item', 'admin' => true],
    ['pass' => ['accountName', 'id', 'itemId']]);
  // アイテム削除
  Router::connect('/admin/:accountName/campaigns/:id/delete/items/:itemId',
    ['controller' => 'campaigns', 'action' => 'delete_item', 'admin' => true],
    ['pass' => ['accountName', 'id', 'itemId']]);
  // 診断結果、投票数、アンケート結果
  Router::connect('/admin/:accountName/campaigns/:id/:action',
    ['controller' => 'campaigns', 'admin' => true],
    ['pass' => ['accountName', 'id'], 'action' => 'results|votes|enquetes']);
/**
 * 応募
 */
  Router::connect('/admin/:accountName/campaigns/:id/entries',
    ['controller' => 'entries', 'admin' => true],
    ['pass' => ['accountName', 'id']]);
  Router::connect('/admin/:accountName/campaigns/:id/entries/:entryId/delete',
    ['controller' => 'entries', 'action' => 'delete', 'admin' => true],
    ['pass' => ['accountName', 'id', 'entryId']]);
  Router::connect('/admin/:accountName/campaigns/:id/entries/csv',
    ['controller' => 'entries', 'action' => 'csv', 'admin' => true],
    ['pass' => ['accountName', 'id']]);

/**
 * 表面
 */
  Router::connect('/',
    ['controller' => 'root', 'action' => 'index']);
  // 静的ページ
  Router::connect('/:pageName',
    ['controller' => 'root', 'action' => 'pages'],
    ['pass' => ['pageName'], 'pageName' => 'terms|privacy']);
  // twitter認証ページへリダイレクト
  Router::connect('/twitteroauth',
    ['controller' => 'root', 'action' => 'twitteroauth']);
/**
 * キャンペーン表面
 */
  // トップ
  Router::connect('/:accountName/campaigns/:id',
    ['controller' => 'campaign_view', 'action' => 'top'],
    ['pass' => ['accountName', 'id']]);
  // メールアドレスでの応募
  Router::connect('/:accountName/campaigns/:id/:action',
    ['controller' => 'campaign_view', 'entryType' => 'email'],
    ['pass' => ['accountName', 'id', 'entryType'], 'action' => 'entry|questions|vote']);
  // ソーシャルアカウントでの応募
  Router::connect('/:accountName/campaigns/:id/:action/:entryType',
    ['controller' => 'campaign_view'],
    ['pass' => ['accountName', 'id', 'entryType'], 'action' => 'entry|questions|vote']);
  // 応募完了
  Router::connect('/:accountName/campaigns/:id/finish',
    ['controller' => 'campaign_view', 'action' => 'finish'],
    ['pass' => ['accountName', 'id']]);
  // 診断結果
  Router::connect('/:accountName/campaigns/:id/result/:resultId',
    ['controller' => 'campaign_view', 'action' => 'result'],
    ['pass' => ['accountName', 'id', 'resultId']]);
  // アイテム一覧
  Router::connect('/:accountName/campaigns/:id/items',
    ['controller' => 'campaign_view', 'action' => 'items'],
    ['pass' => ['accountName', 'id']]);
  // 個別アイテム
  Router::connect('/:accountName/campaigns/:id/items/:itemId',
    ['controller' => 'campaign_view', 'action' => 'items'],
    ['pass' => ['accountName', 'id', 'itemId']]);
  // クーポン
  Router::connect('/:accountName/campaigns/:id/coupon/:entryType',
    ['controller' => 'campaign_view', 'action' => 'coupon'],
    ['pass' => ['accountName', 'id', 'entryType']]);
  // 固定ページ
  Router::connect('/:accountName/campaigns/:id/pages/:pageName',
    ['controller' => 'campaign_view', 'action' => 'pages'],
    ['pass' => ['accountName', 'id', 'pageName']]);
/**
 * Ajax
 */
  // シェア
  Router::connect('/:accountName/campaigns/:id/share/:shareType',
    ['controller' => 'campaign_view', 'action' => 'share'],
    ['pass' => ['accountName', 'id', 'shareType']]);
  // いいね、投票、クーポン使用
  Router::connect('/:accountName/campaigns/:id/:action',
    ['controller' => 'campaign_view'],
    ['pass' => ['accountName', 'id'], 'action' => 'like|vote|uses']);

  // 開発時テスト用
  Router::connect('/test/:action',
    ['controller' => 'test']);

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
  CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
  // require CAKE . 'Config' . DS . 'routes.php';
