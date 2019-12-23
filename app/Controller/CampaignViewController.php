<?php
class CampaignViewController extends AppController {
  public $uses = [
    'Campaign',
    'CampaignValue',
    'CampaignAddDataService',
    'Entry',
    'EntryService',
    'EntrySaveService',
    'SocialAccountService',
    'AudienceService',
  ];
  public $libs = ['Facebook' => 'Ique.IqueFacebook', 'Twitter' => 'Ique.IqueTwitter'];
  public function beforeFilter() {
    parent::beforeFilter();
    // クライアント以下のアクセスかチェック
    $this->_requireClient();
    // モデルのbind
    $this->_bindModelBefore();
    // キャンペーンデータの取得
    $this->_requireCampaign();
    // モデルのbind
    $this->_bindModelAfter();
    // オリジナルデザインを使用する場合
    if($this->campaign['Campaign']['original_design_flg']) {
      $this->theme = $this->currentAccount['Account']['account_name'] . '/' . $this->campaign['Campaign']['id'];
    }
    // 開催中でない場合はビューを切り替え
    if(!in_array(
      $this->campaign['Campaign']['status'],
      ['published', 'test', 'finished', 'stockout']
    )) {
      $this->render('closed');
      $this->response->send();
      exit;
    }
    // POSTデータのモデルへのセット（下のisEntriedで必要）
    $this->Entry->set($this->request->data);
    // 応募済みかどうか
    $entryType = null;
    if($this->action == 'vote' && $this->request->param('entryType')) {
      $entryType = $this->request->param('entryType');
    }
    $this->entried = $this->EntryService->isEntried($this->campaign, $entryType);

    $this->set('account', $this->currentAccount);
    $this->set('campaign', $this->campaign);
    $this->set('audience', $this->audience);
    $this->set('entried', $this->entried);
  }
/**
 * キャンペーントップ
 */
  public function top($accountName, $id) {
    // 複数回応募できる場合
    if(
      $this->campaign['Campaign']['campaign_type'] == 'shindan' ||
      (
        $this->campaign['Campaign']['campaign_type'] == 'contest' &&
        $this->campaign['Campaign']['entry_limit']
      )
    ) {
      $this->entried = false;
    }
    // キャンペーンの種類ごとの追加データの取得
    switch($this->campaign['Campaign']['campaign_type']) {
      // ピックアップアイテムを表示
      case 'contest':
      case 'vote':
      case 'lancers':
        $this->_mergeUses(['uses' => ['Item']]);
        if($this->campaign['Campaign']['campaign_type'] == 'lancers') {
          $this->Item->setDataSource('lancers');
        }
        $this->set('items', $this->Item->find('all', [
          'conditions' => $this->campaign['Campaign']['campaign_type'] == 'lancers'
            ? ['lancers_url' => $this->campaign['Campaign']['lancers_url']]
            : ['campaign_id' => $id, 'unpublished_flg' => false],
          'limit' => 4,
          'order' => 'rand()',
        ]));
        break;
      // スピードくじの場合、抽選結果を表示
      case 'lottery':
        if($this->entried) {
          $this->_mergeUses(['uses' => ['Gift']]);
          $this->set('gift', $this->Gift->findById($this->entried['Entry']['gift_id']));
        }
        break;
    }
  }
/**
 * 応募フォーム
 */
  public function entry($accountName, $id, $entryType) {
    // 初期処理
    $this->_entryInit($entryType);
    if($this->request->is('post')) {
      // バリデーション
      if($this->Entry->saveAll($this->request->data, ['validate' => 'only'])) {
        // クリックされたボタンの種類の判別
        $submitTypes = ['confirm', 'back', 'submit'];
        $self = $this;
        $submitType = array_reduce($submitTypes, function ($result, $next) use ($self) {
          return array_key_exists($next, $self->request->data) ? $next : $result;
        });
        // submit状態ごとの処理
        switch($submitType) {
          case 'confirm':
            // 投稿コンテストの場合リサイズしたデータをセット
            if($this->campaign['Campaign']['campaign_type'] == 'contest') {
              $this->request->data['Item']['image'] = $this->Entry->Item->data['Item']['image'];
            }
            $this->set('isConfirm', true);
            break;
          case 'submit':
            // データの整形
            $data['Entry'] =  $this->request->data['Entry'];
            $data['Entry']['account_id'] = $this->currentAccount['Account']['id'];
            $data['Entry']['campaign_id'] = $id;
            $data['Entry']['campaign_type'] = $this->campaign['Campaign']['campaign_type'];
            $data['Entry']['entry_type'] = $entryType;
            $data['Entry']['user_agent'] = env('HTTP_USER_AGENT');
            // アンケートデータ作成
            if(isset($this->campaign['Enquete']) && $this->campaign['Enquete']) {
              $data['EntryEnquete'] = [];
              foreach($this->campaign['Enquete'] as $enquete) {
                if(!$data['Entry'][$enquete['id']]) {
                  unset($data['Entry'][$enquete['id']]);
                  continue;
                }
                switch($enquete['type']) {
                  case 'text':
                  case 'textarea':
                    $data['EntryEnquete'][] = [
                      'enquete_id' => $enquete['id'],
                      'text' => $data['Entry'][$enquete['id']],
                    ];
                    break;
                  case 'select':
                  case 'radio':
                    $data['EntryEnquete'][] = [
                      'enquete_id' => $enquete['id'],
                      'enquete_option_id' => $data['Entry'][$enquete['id']],
                    ];
                    break;
                  case 'check':
                    foreach($data['Entry'][$enquete['id']] as $value) {
                      $data['EntryEnquete'][] = [
                        'enquete_id' => $enquete['id'],
                        'enquete_option_id' => $value,
                      ];
                    }
                    break;
                }
                unset($data['Entry'][$enquete['id']]);
              }
            }
            // 投稿コンテストの場合のitemデータの設定
            if($this->campaign['Campaign']['campaign_type'] == 'contest') {
              $data['Item']['image'] =  $this->request->data['Item']['image'];
              $data['Item']['nickname'] =  $this->request->data['Item']['nickname'];
              $data['Item']['title'] =  $this->request->data['Item']['title'];
              $data['Item']['description'] =  $this->request->data['Item']['description'];
              $data['Item']['campaign_id'] = $id;
              if($this->campaign['Campaign']['contest_default_unpublished_flg']) {
                $data['Item']['unpublished_flg'] = true;
              }
            }
            // 投票コンテストの場合のvoteデータの作成
            if($this->campaign['Campaign']['campaign_type'] == 'vote') {
              $data['Vote']['item_id'] = $this->item['Item']['id'];
              $data['Vote']['campaign_id'] = $id;
              $data['Vote']['entry_type'] = $entryType;
            }
            // スピードくじの場合の抽選
            if($this->campaign['Campaign']['campaign_type'] == 'lottery') {
              $this->_mergeUses(['uses' => ['Gift']]);
              $data['Entry']['gift_id'] = $this->Gift->draw($this->campaign['Campaign']['id']);
            }
            // twitterのフォロー
            if($entryType == 'twitter') {
              if($this->Twitter->follow($this->currentAccount['Account']['twitter_screen_name'])) {
                $data['Entry']['twitter_new_fan'] = true;
                $data['Like'] = [
                  'twitter_account_id' => $this->currentAccount['Account']['twitter_id'],
                  'campaign_id' => $id,
                  'account_id' => $this->currentAccount['Account']['id'],
                  'entry_type' => $entryType,
                ];
              }
            }
            if($this->campaign['Campaign']['campaign_type'] == 'coupon' && $entryType == 'email') {
              CakeSession::write('entry_email', $data['Entry']['email']);
            }
            // 保存処理
            $this->Entry->create();
            if(
              $this->campaign['Campaign']['status'] == 'test' ||
              $this->EntrySaveService->saveEntry($data)
            ) {
              // シェア
              if(array_key_exists('share_flg', $data['Entry']) && !$data['Entry']['share_flg']) {
                $shareData = [
                  'message' => $data['Entry']['share_message'],
                  'link' => Router::url([
                    'action' => 'top',
                    'accountName' => $accountName,
                    'id' => $id,
                  ], true),
                ];
                if(
                  $this->campaign['Campaign']['campaign_type'] == 'contest' &&
                  !$this->campaign['Campaign']['contest_default_unpublished_flg']
                ) {
                  $shareData['link'] = Router::url([
                    'action' => 'items',
                    'accountName' => $accountName,
                    'id' => $id,
                    'itemId' => $this->Entry->Item->getLastInsertId(),
                  ], true);
                  $shareData['name'] = sprintf(
                    '【%s】に「%s」を投稿しました。',
                    $this->campaign['Campaign']['title'],
                    $data['Item']['title']
                  );
                }
                if($this->SocialAccountService->share($entryType, $shareData)) {
                  $this->Entry->save([
                    $entryType . '_shared' => true,
                    $entryType . '_shared_message' => $data['Entry']['share_message'],
                  ]);
                }
              }
              if($this->campaign['Campaign']['campaign_type'] == 'shindan' && $entryType == 'email') {
                CakeSession::write('shindan_entry_id', $this->Entry->getLastInsertId());
                // 診断ページへリダイレクト
                $this->redirect([
                  'action' => 'questions',
                  'accountName' => $accountName,
                  'id' => $id,
                  'entryType' => $entryType,
                ]);
                exit();
              }
              // 結果ページへリダイレクト
              $this->redirect([
                'action' => 'finish',
                'accountName' => $accountName,
                'id' => $id,
              ]);
            } else {
              $this->Session->setDangerFlash('応募できませんでした');
              $this->set('isConfirm', true);
            }
            break;
        }
      }
    } else {
      // Facebookでの応募でアプリ認証済みの場合、ユーザデータをデフォルトデータとして表示
      if($entryType == 'facebook') {
        $this->request->data['Entry'] = $this->SocialAccountService->getFacebookProfile();
      }
    }
    // いいね、フォロー済みかどうか
    $liked = $this->SocialAccountService->isLiked($entryType, $this->currentAccount);
    $this->set('liked', $liked);
  }
/**
 * 診断フォーム
 */
  public function questions($accountName, $id, $entryType) {
    // 初期処理
    $this->_entryInit($entryType);
    if($this->request->is('post')) {
      // 設問のバリデーション追加
      foreach($this->campaign['Question'] as $question) {
        $this->Entry->addNotEmpty($question['id']);
      }
      // バリデーション
      if($this->Entry->validates()) {
        // データの整形
        if ($entryType == 'email' && $this->campaign['Campaign']['status'] != 'test') {
          $data['Entry'] = $this->request->data['Entry'] + $this->entried['Entry'];
        } else {
          $data['Entry'] = $this->request->data['Entry']; 
        }
        $data['Entry']['account_id'] = $this->currentAccount['Account']['id'];
        $data['Entry']['campaign_id'] = $id;
        $data['Entry']['campaign_type'] = $this->campaign['Campaign']['campaign_type'];
        $data['Entry']['entry_type'] = $entryType;
        $data['Entry']['user_agent'] = env('HTTP_USER_AGENT');
        // twitterのフォロー
        if($entryType == 'twitter') {
          if($this->Twitter->follow($this->currentAccount['Account']['twitter_screen_name'])) {
            $data['Entry']['twitter_new_fan'] = true;
            $data['Like'] = [
              'twitter_account_id' => $this->currentAccount['Account']['twitter_id'],
              'campaign_id' => $id,
              'account_id' => $this->currentAccount['Account']['id'],
              'entry_type' => $entryType,
            ];
          }
        }
        // 診断関連のデータ処理
        $data['EntryAnswer'] = [];
        $resultScore = 0;
        foreach($this->campaign['Question'] as $question) {
          // 結果算出用の点数をカウント
          foreach($question['Answer'] as $answer) {
            if($data['Entry'][$question['id']] == $answer['id']) {
              $resultScore += $answer['point'];
              break;
            }
          }
          // DB保存用のデータを作成
          $data['EntryAnswer'][] = [
            'answer_id' => $data['Entry'][$question['id']],
            'question_id' => $question['id'],
          ];
          unset($data['Entry'][$question['id']]);
        }
        // 結果を判別
        foreach($this->campaign['Result'] as $result) {
          if(
            $result['point_min'] <= $resultScore &&
            $resultScore <= $result['point_max']
          ) {
            $data['Entry']['result_id'] = $result['id'];
            break;
          }
        }
        if(empty($data['Entry']['result_id'])) {
          $data['Entry']['result_id'] = $this->campaign['Result'][0]['id'];
        }
        // 保存処理
        $this->Entry->create();
        if(
          $this->campaign['Campaign']['status'] == 'test' ||
          $this->EntrySaveService->saveEntry($data)
        ) {
          CakeSession::delete('shindan_entry_id');
          $this->redirect([
            'action' => 'result',
            'accountName' => $accountName,
            'id' => $id,
            'resultId' => $data['Entry']['result_id'],
          ]);
        } else {
          $this->Session->setDangerFlash('失敗しました');
          $this->set('isConfirm', true);
        }
      }
    }
    // いいね、フォロー済みかどうか
    $liked = $this->SocialAccountService->isLiked($entryType, $this->currentAccount);
    $this->set('liked', $liked);
  }
/**
 * キャンペーン応募完了
 */
  public function finish($accountName, $id) {
    // 投稿コンテストの場合、投稿したアイテム
    if($this->campaign['Campaign']['campaign_type'] == 'contest') {
      $this->_mergeUses(['uses' => ['Item']]);
      $item = '';
      if($this->entried) {
        $item = $this->Item->find('first', ['conditions' => [
          'entry_id' => $this->entried['Entry']['id'],
          'campaign_id' => $id,
          'unpublished_flg' => false,
        ]]);
      }
      $this->set('item', $item);
    }
    // スピードくじの場合、抽選結果
    if($this->campaign['Campaign']['campaign_type'] == 'lottery') {
      $this->_mergeUses(['uses' => ['Gift']]);
      $gift = '';
      if($this->entried) {
        $gift = $this->Gift->findById($this->entried['Entry']['gift_id']);
      }
      if($this->campaign['Campaign']['status'] == 'test') {
        $gift = $this->Gift->findById($this->Gift->draw($this->campaign['Campaign']['id']));
      }
      $this->set('gift', $gift);
    }
  }
/**
 * 診断結果
 */
  public function result($accountName, $id, $resultId) {
    // 結果を取得
    $this->_mergeUses(['uses' => ['Result']]);
    $result = $this->Result->requireData('first', ['conditions' => [
      'id' => $resultId,
      'campaign_id' => $id
    ]]);
    // 応募結果のページではない場合、応募情報をクリアする
    if($this->entried && $this->entried['Entry']['result_id'] != $resultId) {
      $this->entried = false;
    }
    $this->set('result', $result);
  }
/**
 * アイテム
 */
  public function items($accountName, $id, $itemId = null) {
    $campaign = $this->campaign['Campaign'];
    // コンテストで複数回応募できる場合
    if(
      $campaign['campaign_type'] == 'contest' &&
      $campaign['entry_limit']
    ) {
      $this->entried = false;
    }
    // 個別ページ
    if($itemId) {
      if($campaign['campaign_type'] == 'contest') {
        $this->Item->bindModel(['belongsTo' => ['Entry']]);
      }
      // メインアイテム
      $this->set('item', $this->Item->requireData('first', [
        'conditions' => $campaign['campaign_type'] == 'lancers'
          ? [
            'id' => (int) $itemId,
            'campaign_id' => $id,
            'lancers_url' => $campaign['lancers_url'],
          ]
          : ['Item.id' => $itemId, 'Item.campaign_id' => $id, 'unpublished_flg' => false],
      ]));
      // 不要モデルのunbind
      $this->Item->unbindModel([
        'hasMany' => ['Voted'],
        'belongsTo' => ['Entry'],
      ], false);
      // ピックアップ
      $this->set('items', $this->Item->find('all', [
        'conditions' => $campaign['campaign_type'] == 'lancers'
          ? ['lancers_url' => $campaign['lancers_url']]
          : ['campaign_id' => $id, 'unpublished_flg' => false],
        'limit' => 4,
        'order' => 'rand()',
      ]));
      // 前後アイテム
      if($campaign['campaign_type'] == 'contest') {
        $this->set('neighbors', $this->Item->requireData('neighbors', [
          'field' => 'id',
          'value' => $itemId,
          'conditions' => [
            'Item.campaign_id' => $id,
            'unpublished_flg' => false
          ],
        ]));
      }
      $this->render('items-single');
    }
    // 一覧ページ
    else {
      if($campaign['campaign_type'] == 'lancers') {
        if(!$campaign['vote_count_hidden_flg']) {
          $this->Item->bindModel(['hasMany' => ['Vote']]);
        }
        $this->Paginator->settings['limit'] = $campaign['item_per_page'];
        $this->Paginator->settings['order'] = 'rand()';
        $this->set('items', $this->Paginator->paginate(
          'Item',
          ['lancers_url' => $campaign['lancers_url']]
        ));
      } else {
        if($campaign['campaign_type'] == 'contest') {
          $this->Item->bindModel(['belongsTo' => ['Entry']]);
        }
        $this->Paginator->settings['limit'] = $campaign['item_per_page'];
        // デフォルトの並び順（ビューのボタンに反映させるためにqueryに設定する）
        if(!$this->request->query('sort')) {
          if(in_array($campaign['item_sort_default'], ['created', 'vote_count'])) {
            $this->request->query['sort'] = $campaign['item_sort_default'];
            $this->request->query['direction'] = 'desc';
          } else {
            $this->request->query['sort'] = 'rand';
          }
        }
        $this->set('items', $this->Paginator->paginate(
          'Item',
          ['Item.campaign_id' => $id, 'Item.unpublished_flg' => false]
        ));
      }
    }
  }
/**
 * クーポン
 *
 * アプリ認証してfinishページに遷移する
 */
  public function coupon($accountName, $id, $entryType) {
    $finishUrl = [
      'action' => 'finish',
      'accountName' => $accountName,
      'id' => $id,
    ];
    if($entryType == 'email' || $this->{ucfirst($entryType)}->getUser()) {
      $this->redirect($finishUrl);
    } else {
      switch($entryType) {
        case 'facebook':
          $this->redirect([
            'controller' => 'oauth',
            'action' => 'facebookLogin',
            '?' => ['redirect_uri' => Router::url($finishUrl)]
          ]);
          break;
        case 'twitter':
          $this->redirect([
            'controller' => 'oauth',
            'action' => 'twitterLogin',
            '?' => ['redirect_uri' => Router::url($finishUrl)]
          ]);
          break;
      }
    }
  }
/**
 * 固定ページ
 */
  public function pages($accountName, $id, $pageName) {
    // オリジナルテーマの設定
    $this->theme = $this->currentAccount['Account']['account_name'] . '/' . $this->campaign['Campaign']['id'];
    $this->render('pages-' . $pageName);
  }
/**
 * シェア
 */
  public function share($accountName, $id, $shareType) {
    $this->_ajaxInit();
    if($this->request->is('post')) {
      // シェアの保存
      $dataSource = $this->Entry->getDataSource();
      $dataSource->begin();
      if($this->entried) {
        $entried = $this->entried['Entry'];
        $this->Entry->id = $entried['id'];
        $this->Entry->setCounterCache();
        if(!$this->Entry->save([
          $entried['entry_type'] . '_shared' => true,
          $entried['entry_type'] . '_shared_message' => $this->request->data['message'],
        ])) {
          $dataSource->rollback();
          throw new Exception;
        }
      }
      // シェアの処理
      if(!$this->SocialAccountService->share($shareType, $this->request->data)) {
        $dataSource->rollback();
        throw new Exception;
      }
      $dataSource->commit();
    }
  }
/**
 * いいね
 */
  public function like($accountName, $id) {
    $this->_ajaxInit();
    if($this->request->is('post')) {
      $this->_mergeUses(['uses' => ['Like']]);
      // データの存在チェック
      if($this->Like->find('first', ['conditions' => [
        'facebook_id' => $this->request->data['facebook_id'],
        'facebook_page_id' => $this->currentAccount['Account']['facebook_id'],
        'campaign_id' => $id,
      ]])) {
        throw new Exception;
      }
      // 保存データの作成
      $data = [
        'facebook_id' => $this->request->data['facebook_id'],
        'facebook_page_id' => $this->currentAccount['Account']['facebook_id'],
        'campaign_id' => $id,
        'account_id' => $this->currentAccount['Account']['id'],
        'entry_type' => 'facebook',
      ];
      // 保存
      $this->Like->create();
      $this->Like->save($data);
    }
  }
/**
 * 投票
 */
  public function vote($accountName, $id, $entryType = null) {
    // Ajaxリクエストの場合
    if($this->request->is('ajax')) {
      return $this->_voteAjax($accountName, $id);
    }
    // キャンペーンが開催中かどうか
    if(!in_array($this->campaign['Campaign']['status'], ['published', 'test'])) {
      throw new SorapsException;
    }
    // 投票機能を無効化している場合
    if($this->campaign['Campaign']['contest_vote_disabled_flg']) {
      throw new SorapsException;
    }
    // 投票方法が有効かどうか
    if(!in_array($entryType, Entry::$entryTypes)) {
      throw new SorapsException('BadEntryType');
    }
    if(!$this->campaign['Campaign'][$entryType . '_entry_flg']) {
      throw new SorapsException('BadEntryType');
    }
    // アプリ認証していない場合はトップへリダイレクト
    if(
      ($entryType == 'facebook' && !$this->Facebook->getUser()) ||
      ($entryType == 'twitter' && !$this->Twitter->getUser())
    ) {
      throw new SorapsException('RequireSocialLogin');
    }
    // アイテムの存在と投票済みチェック
    $item = $this->Item->requireData('first', [
      'conditions' => $this->campaign['Campaign']['campaign_type'] == 'lancers'
        ? [
          'id' => (int) $this->request->query('item'),
          'campaign_id' => $id,
          'lancers_url' => $this->campaign['Campaign']['lancers_url'],
        ]
        : [
          'id' => $this->request->query('item'),
          'campaign_id' => $id,
          'unpublished_flg' => false
        ],
    ]);
    if($item['Voted']) {
      $this->Session->setDangerFlash('投票済みです');
      $this->redirect([
        'action' => 'items',
        'accountName' => $accountName,
        'id' => $id,
        'itemId' => $this->request->query('item'),
      ]);
    }
    // エラー処理終わり
    $this->set('item', $item);
    $this->_mergeUses(['uses' => ['Vote']]);
    // 入力項目のバリデーション
    foreach(CampaignValue::$inputs as $key => $label) {
      if($this->campaign['Campaign']['input_vote_' . $key] == 2) {
        if(in_array($key, ['name', 'kana'])) {
          $this->Vote->addNotEmpty('last_' . $key);
          $this->Vote->addNotEmpty('first_' . $key);
        } else {
          $this->Vote->addNotEmpty($key);
        }
      }
    }
    // 保存処理
    if($this->request->is('post')) {
      // バリデーション
      $this->Vote->set($this->request->data);
      if(!$this->Vote->validates()) {
        return;
      }
      // クリックされたボタンの種類の判別
      $submitTypes = ['confirm', 'back', 'submit'];
      $self = $this;
      $submitType = array_reduce($submitTypes, function ($result, $next) use ($self) {
        return array_key_exists($next, $self->request->data) ? $next : $result;
      });
      // 確認画面
      if($submitType == 'confirm') {
        $this->set('isConfirm', true);
        return;
      }
      // 保存でない場合（基本的にbackのはず）
      if($submitType != 'submit') {
        return;
      }
      // 投票データ
      $data['Vote'] =  Hash::filter($this->request->data['Vote']);
      $data['Vote']['item_id'] = $this->request->query('item');
      $data['Vote']['campaign_id'] = $id;
      $data['Vote']['entry_type'] = $entryType;
      $data['Vote']['user_agent'] = env('HTTP_USER_AGENT');
      // ソーシャルアカウントでの投票の場合
      if(in_array($entryType, Entry::$socialEntryTypes)) {
        // オーディエンスデータ
        if($this->audience) {
          $data['Vote']['audience_id'] = $this->audience['Audience']['id'];
          $data['Vote'] += Hash::filter($this->audience['Audience']);
          $data['Audience'] = $data['Vote'];
          // オーディエンスのidがセットされるのでunset
          unset($data['Vote']['id']);
        } else {
          $socialProfile = $this->SocialAccountService->{'get' . ucfirst($entryType) . 'Profile'}();
          $data['Vote'] += $socialProfile;
          $data['Audience'] = $data['Vote'];
          $data['Audience']['account_id'] = $this->currentAccount['Account']['id'];
          $data['Audience']['entry_type'] = $entryType;
          $data['Audience']['user_agent'] = env('HTTP_USER_AGENT');
        }
        // 応募データ
        if($this->entried) {
          $data['Vote']['entry_id'] = $this->entried['Entry']['id'];
          $data['Vote']['entrant_id'] = $this->entried['Entry']['entrant_id'];
        }
        // シェア
        if(!$this->request->data['Vote']['share_flg']) {
          $shareData = [
            'message' => $this->request->data['Vote']['share_message'],
            'link' => Router::url([
              'action' => 'items',
              'accountName' => $accountName,
              'id' => $id,
              'itemId' => $this->request->query('item'),
            ], true),
            'name' => $this->campaign['Campaign']['campaign_type'] == 'lancers'
              ? '私は' . $item['Item']['title'] . 'さんの、この作品に投票しました！'
              : $this->campaign['Campaign']['title'] . 'で' . $item['Item']['title'] . 'に投票しました！',
            'description' => $this->campaign['Campaign']['campaign_type'] == 'lancers'
              ? 'みなさんもこの作品に投票をして応援をお願いします！'
              : null,
          ];
          if($this->SocialAccountService->share($entryType, $shareData)) {
            $data['Vote'][$entryType . '_shared'] = true;
            $data['Vote'][$entryType . '_shared_message'] = $this->request->data['Vote']['share_message'];
          }
        }
      }
      // 保存
      $this->Vote->create();
      if(
        $this->campaign['Campaign']['status'] == 'test' ||
        $this->Vote->saveAssociated($data)
      ) {
        $this->Session->setSuccessFlash('投票が完了しました');
        $this->redirect([
          'action' => 'items',
          'accountName' => $accountName,
          'id' => $id,
          'itemId' => $this->request->query('item'),
        ]);
      }
    } else {
      // Facebookでの応募でアプリ認証済みの場合、ユーザデータをデフォルトデータとして表示
      if($entryType == 'facebook') {
        $this->request->data['Vote'] = $this->SocialAccountService->getFacebookProfile();
      }
    }
  }
  protected function _voteAjax($accountName, $id) {
    $this->_ajaxInit();
    if($this->request->is('post')) {
      // アイテムの存在と投票済みチェック
      $item = $this->Item->find('first', [
        'conditions' => [
          'id' => $this->request->data['itemId'],
          'campaign_id' => $id,
          'unpublished_flg' => false,
        ],
      ]);
      if(!$item || $item['Voted']) {
        throw new Exception;
      }
      // 一度も投票済みでない場合は例外を投げる
      if(!$this->entried) {
        throw new Exception;
      }
      // 保存データの作成
      $entried = $this->entried['Entry'];
      $data = [
        'item_id' => $this->request->data['itemId'],
        'campaign_id' => $this->campaign['Campaign']['id'],
        'entry_id' => $entried['id'],
        'entrant_id' => $entried['entrant_id'],
        'audience_id' => $entried['audience_id'],
        'entry_type' => $entried['entry_type'],
        'user_agent' => env('HTTP_USER_AGENT'),
      ];
      // ソーシャル
      if($entried['entry_type'] == 'facebook') {
        $data['facebook_id'] = $entried['facebook_id'];
        $data['facebook_username'] = $entried['facebook_username'];
      }
      if($entried['entry_type'] == 'twitter') {
        $data['twitter_id'] = $entried['twitter_id'];
        $data['twitter_screen_name'] = $entried['twitter_screen_name'];
      }
      // 保存
      $this->_mergeUses(['uses' => ['Vote']]);
      $this->Vote->create();
      if(
        $this->campaign['Campaign']['status'] == 'test' ||
        $this->Vote->save($data)
      ) {
        return true;
      } else {
        throw new Exception;
      }
    }
  }
/**
 * クーポンの使用
 */
  public function uses($accountName, $id) {
    $this->_ajaxInit();
    if($this->request->is('post')) {
      if($this->campaign['Campaign']['campaign_type'] != 'coupon') {
        throw new Exception;
      }
      if(!$this->entried) {
        throw new Exception;
      }
      if($this->entried['Entry']['coupon_used']) {
        throw new Exception;
      }
      $this->Entry->id = $this->entried['Entry']['id'];
      $this->Entry->setCounterCache();
      if(!$this->Entry->save([
        'coupon_used' => true,
      ])) {
        throw new Exception;
      }
    }
  }
/**
 * モデルのbind
 */
  protected function _bindModelBefore() {
    switch($this->action) {
      case 'questions':
        $this->_mergeUses(['uses' => ['Question', 'Answer', 'Result']]);
        $this->Campaign->bindModel(['hasMany' => ['Question', 'Result']], false);
        $this->Question->bindModel(['hasMany' => ['Answer']], false);
        $this->Campaign->recursive = 2;
        break;
    }
  }
  protected function _bindModelAfter() {
    // オーディエンスの取得
    $entryType = null;
    if($this->action == 'vote' && $this->request->param('entryType')) {
        $entryType = $this->request->param('entryType');
    }
    $this->audience = $this->AudienceService->hasAudience($this->currentAccount, $entryType);
    switch($this->action) {
      case 'items':
      case 'vote':
        // アイテムモデルの使用
        $this->_mergeUses(['uses' => ['Item']]);
        if($this->campaign['Campaign']['campaign_type'] == 'lancers') {
          $this->Item->setDataSource('lancers');
        }
        if($this->campaign['Campaign']['status'] != 'test') {
          $this->Item->Facebook = $this->Facebook;
          $this->Item->Twitter = $this->Twitter;
          $this->Item->bindVoted($this->request->data);
        }
        break;
    }
  }
/**
 * キャンペーン応募時の初期処理
 */
  protected function _entryInit($entryType) {
    // キャンペーンが開催中かどうか
    if(!in_array($this->campaign['Campaign']['status'], ['published', 'test'])) {
      throw new SorapsException;
    }
    // 応募方法が有効かどうか
    if(!in_array($entryType, Entry::$entryTypes)) {
      throw new SorapsException('BadEntryType');
    }
    if(!$this->campaign['Campaign'][$entryType . '_entry_flg']) {
      throw new SorapsException('BadEntryType');
    }
    // ソーシャルアカウントでの応募でアプリ認証していない場合はトップへリダイレクト
    if(in_array($entryType, ['facebook', 'twitter'])) {
      if(
        ($entryType == 'facebook' && !$this->Facebook->getUser()) ||
        ($entryType == 'twitter' && !$this->Twitter->getUser())
      ) {
        throw new SorapsException('RequireSocialLogin');
      }
    }
    // 応募済みでないかどうか
    if($this->entried) {
      switch($this->campaign['Campaign']['campaign_type']) {
        case 'shindan':
        case $this->campaign['Campaign']['campaign_type'] == 'contest' &&
             $this->campaign['Campaign']['entry_limit']:
          // 診断は何回でもできる
          break;
        case 'vote':
          // アイテム詳細へリダイレクト
          $this->redirect([
            'action' => 'items',
            'accountName' => $this->request->param('accountName'),
            'id' => $this->request->param('id'),
            'itemId' => $this->request->query('item'),
          ]);
          break;
        case 'coupon':
          if($this->campaign['Campaign']['campaign_type'] == 'coupon' && $entryType == 'email') {
            CakeSession::write('entry_email', $this->entried['Entry']['email']);
          }
          // 完了ページへリダイレクト
          $this->redirect([
            'action' => 'finish',
            'accountName' => $this->request->param('accountName'),
            'id' => $this->request->param('id'),
          ]);
          break;
        default:
          throw new SorapsException('Entried');
      }
    }
    // デフォルト入力項目のバリデーション追加
    foreach(CampaignValue::$inputs as $key => $label) {
      if($key == 'email' || $this->campaign['Campaign']['input_' . $key] == 2) {
        if(in_array($key, ['name', 'kana'])) {
          $this->Entry->addNotEmpty('last_' . $key);
          $this->Entry->addNotEmpty('first_' . $key);
        } else {
          $this->Entry->addNotEmpty($key);
        }
      }
    }
    // アンケート
    if($this->campaign['Campaign']['campaign_type'] != 'shindan') {
      $this->Campaign->bindModel(['hasMany' => ['Enquete']], false);
      $this->Campaign->Enquete->bindModel(['hasMany' => ['EnqueteOption']], false);
      $this->Campaign->recursive = 2;
      $this->_requireCampaign();
      // アンケート項目のバリデーション追加
      foreach($this->campaign['Enquete'] as $enquete) {
        if($enquete['required_flg']) {
          if($enquete['type'] == 'check') {
            $this->Entry->addNotEmptyMultiple($enquete['id']);
          } else {
            $this->Entry->addNotEmpty($enquete['id']);
          }
        }
      }
    }
    // キャンペーンの種類ごとの処理
    switch($this->campaign['Campaign']['campaign_type']) {
      case 'contest':
        $this->Entry->bindModel(['hasOne' => ['Item']], false);
        break;
      case 'vote':
        // 投票コンテストの場合は投票対象のアイテムを表示
        $this->_mergeUses(['uses' => ['Item']]);
        $this->item = $this->Item->requireData('first', ['conditions' => [
          'id' => $this->request->query('item'),
          'campaign_id' => $this->request->param('id'),
          'unpublished_flg' => false,
        ]]);
        $this->set('item', $this->item);
        break;
    }
  }
/**
 * Ajax処理の初期処理
 */
  protected function _ajaxInit() {
    $this->autoRender = false;
    // キャンペーンが開催中かどうか
    if(!in_array($this->campaign['Campaign']['status'], ['published', 'test', 'stockout'])) {
      throw new Exception;
    }
  }
}
