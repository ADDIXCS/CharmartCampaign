<?php
class Lancers extends Mysql {
/**
 * Mysqlの設定で初期化
 * Goutteのインスタンスを生成
 */
  public function __construct($config = null, $autoConnect = true) {
    parent::__construct(ConnectionManager::$config->default, $autoConnect);
    $this->client = new Goutte\Client();
  }
/**
 * queryDataの退避
 */
  public function read(Model $model, $queryData = [], $recursive = null) {
    // findFirstの場合
    if(
      array_key_exists('id', $queryData['conditions']) &&
      array_key_exists('lancers_url', $queryData['conditions'])
    ) {
      // MysqlのfetchAllが使われるように
      $this->queryData = $queryData;
      unset($this->queryData['conditions']['lancers_url']);
      // スクレイピングデータの取得
      $crawler = $this->client->request('GET', $this->_getUrl($queryData));
      if(!$item = $this->_getSingle($crawler, $queryData)) {
        return [];
      }
      // 保存データ作成
      $item = current($item);
      $item['Item']['campaign_id'] = $queryData['conditions']['campaign_id'];
      $item['Item']['image'] = implode(',', $item['Item']['image']);
      // モデルの設定変更
      $model->Behaviors->unload('IqueS3');
      unset($model->validate['image']);
      // 保存処理
      $model->create();
      if($model->save($item)) {
        unset($queryData['conditions']['lancers_url']);
      }
    }
    $this->queryData = $queryData;
    return parent::read($model, $queryData, $recursive);
  }
/**
 * データの取得
 */
  public function fetchAll($sql, $params = [], $options = []) {
    $queryData = $this->queryData;
    // lancers_urlがない場合はDBのデータを返す
    if(!array_key_exists('lancers_url', $queryData['conditions'])) {
      return parent::fetchAll($sql, $params, $options);
    }
    if($queryData['limit'] == 1) {
      $queryData['limit'] = 40;
    } elseif($queryData['limit'] > 100) {
      $queryData['limit'] = 100;
    }
    $crawler = $this->client->request('GET', $this->_getUrl($queryData));
    if($queryData['fields'] == 'COUNT(*) AS `count`') {
      $count = trim($crawler->filter('.indication')->text());
      $count = strstr($count, '件', true);
      return [[['count' => $count]]];
    } elseif(array_key_exists('id', $queryData['conditions'])) {
      // ここにくるのは保存に失敗したとき
      return $this->_getSingle($crawler, $queryData);
    } else {
      return $this->_getList($crawler, $queryData);
    }
  }
/**
 * ランサーズのページのURLを返す
 */
  protected function _getUrl($queryData = []) {
    if(array_key_exists('id', $queryData['conditions'])) {
      $url = [
        'https://www.lancers.jp/work/proposal',
        $queryData['conditions']['id'],
      ];
    } else {
      $url[] = 'http://www.lancers.jp/work/proposals';
      // ランサーズURLからプロジェクトのidを抽出
      preg_match('/[0-9]+/', $queryData['conditions']['lancers_url'], $match);
      $url[] = $match[0];
      // id順に表示
      $url[] = 'sort:Proposal.id/direction:desc';
      // ランサーズページの表示件数
      if($queryData['order'][0] == 'rand()' && $queryData['limit'] < 10) {
        $url[] = 'limit:100';
      } else {
        $url[] = 'limit:' . $queryData['limit'];
      }
      if($queryData['page'] > 1) {
        $url[] = 'page:' . $queryData['page'];
      }
    }
    return implode('/', $url);
  }
/**
 * 一覧ページのデータを返す
 */
  protected function _getList(Symfony\Component\DomCrawler\Crawler $crawler, $queryData = []) {
    // 各提案のオブジェクトを配列化
    $nodes = $crawler->filter('.suggest_list_img > .suggestion_box')->each(function ($node) {
      return $node;
    });
    // 表示順がランダムの場合
    if($queryData['order'][0] == 'rand()') {
      shuffle($nodes);
      $nodes = array_slice($nodes, 0, $queryData['limit']);
    }
    // 配列データを生成
    $results = [];
    foreach($nodes as $node) {
      $results[] = ['Item' => [
        'id'     => str_replace('/work/proposal/', '', $node->filter('.img_box a')->attr('href')),
        'image'  => $node->filter('.img_box img')->attr('src'),
        'title'  => $node->filter('.lancer')->text(),
      ]];
    }
    return $results;
  }
/**
 * 個別ページのデータを返す
 */
  protected function _getSingle(Symfony\Component\DomCrawler\Crawler $crawler, $queryData = []) {
    try {
      // 正しい案件かチェック
      $requiredId = end(explode('/', $queryData['conditions']['lancers_url']));
      $requestedId = end(explode('/', $crawler->filter('.now a')->attr('href')));
      if($requiredId != $requestedId) {
        return [];
      }
      $result = ['Item' => [
        'id' => $queryData['conditions']['id'],
        'title' => $crawler->filter('#right_menu .lancer')->text(),
        'description' => trim($crawler->filter('.suggest_detail .comment')->text()),
        'image' => $crawler->filter('.suggest_detail > img')->each(function ($node) {
          return $node->attr('src');
        }),
      ]];
    } catch(InvalidArgumentException $e) {
      return [];
    }
    // 提案画像が一枚の場合
    if(!$result['Item']['image']) {
      $result['Item']['image'][] = $crawler->filter('.attachment_images img')->attr('src');
    }
    return [$result];
  }
}
