<?php
$campaign = $campaign['Campaign'];
$entried = !empty($entried) ? $entried['Entry'] : false;
if($entried && in_array($entried['entry_type'], ['facebook', 'twitter'])):
  // ajaxでPOSTする先
  if ($entried['entry_type'] != 'facebook') {
    $apiUrl = $this->Html->url([
      'action' => 'share',
      'accountName' => $currentAccount['Account']['account_name'],
      'id' => $campaign['id'],
      'shareType' => $entried['entry_type'],
    ]);
  }
  // シェアするURL（キャンペーントップ)
  $shareLink = $this->Html->url([
    'action' => 'top',
    'accountName' => $currentAccount['Account']['account_name'],
    'id' => $campaign['id'],
  ], true);
  // シェアコンテンツ
  $sharePicture = $this->S3->url($campaign, 'Campaign.top_image');
  $shareName = $campaign['title'];
  $shareDescription = $campaign['summary'];
  // キャンペーンごとに異なる部分
  switch($campaign['campaign_type']) {
    case $campaign['campaign_type'] == 'contest' && $item:
      $item = $item['Item'];
      $shareLink = $this->Html->url([
        'action' => 'items',
        'accountName' => $currentAccount['Account']['account_name'],
        'id' => $campaign['id'],
        'itemId' => $item['id'],
      ], true);
      $sharePicture = $this->S3->url($item, 'Item.image');
      $shareName = sprintf(
        '【%s】に「%s」を投稿しました。',
        $campaign['title'],
        $item['title']
      );
      if($item['description']) {
        $shareDescription = $item['description'];
      }
      break;
    case 'shindan':
      $result = $result['Result'];
      if($result['image']) {
        $sharePicture = $this->S3->url($result, 'Result.image');
      }
      if($result['title']) {
        $shareName = sprintf(
          '【%s】 診断結果は「%s」でした。',
          $campaign['title'],
          $result['title']
        );
        if($result['description']) {
          $shareDescription = $result['description'];
        }
      }
      break;
  }
  $this->start('script');
  ?>
  <?php if($entried['entry_type'] == 'facebook'): ?>
    <div id="fb-root"></div>
  <?php endif; ?>
  <script>
  $(function() {
    <?php if($entried['entry_type'] == 'facebook'): ?>
    window.fbAsyncInit = function() {
      FB.init({
        appId      : "<?php echo Configure::read('facebook.appId'); ?>",
        xfbml      : false,
        version    : 'v2.4'
      });
    <?php endif; ?>

    $('#js-btn-share').on('click', function(e) {
      e.preventDefault();
      $('#loading').show();

      <?php if($entried['entry_type'] == 'facebook'): ?>
        FB.ui({
          method:      'feed',
          link:        '<?php echo $shareLink; ?>',
          picture:     '<?php echo $sharePicture; ?>',
          name:        '<?php echo $shareName; ?>',
          description: '<?php echo strip_tags(str_replace(["\r\n", "\r", "\n"], '', $shareDescription)); ?>'
        }, function(res){
          if (res) {
            alert('シェアしました');
          } else {
            alert('シェアに失敗しました');
          }
          $('#loading').fadeOut();
        });
      <?php else: ?>
      $.ajax({
        type: 'POST',
        url: '<?php echo $apiUrl; ?>',
        data: {
          'message'    : $('#js-share-message-<?php echo $entried['entry_type']; ?>').val(),
          'link'       : '<?php echo $shareLink; ?>',
          'picture'    : '<?php echo $sharePicture; ?>',
          'name'       : '<?php echo $shareName; ?>',
          'description': '<?php echo strip_tags(str_replace(["\r\n", "\r", "\n"], '', $shareDescription)); ?>',
        }
      }).done(function(res) {
        alert('シェアしました');
      }).fail(function(res) {
        alert('シェアに失敗しました');
      }).always(function(res) {
        $('#loading').fadeOut()
      });
      <?php endif; ?>
    });

    <?php if($entried['entry_type'] == 'facebook'): ?>
    };

    (function(d, s, id){
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) {return;}
      js = d.createElement(s); js.id = id;
      js.src = "https://connect.facebook.net/ja_JP/sdk.js";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
    <?php endif; ?>
  });
  </script>
  <?php
  $this->end();
endif;
?>
