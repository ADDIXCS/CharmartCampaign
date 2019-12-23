<?php
/**
 * Googleアナリティクストラッキングコード
 */

// トラッキングID
$trackingId = Configure::read('google_analytics.tracking_id');
if (!$trackingId) {
    $trackingId = 'UA-69071941-8';
}

// Config
$googleAnalyticsConfig = [];

// OAuth認証からのリダイレクトを参照元にしない
$afterFacebookOauthCallback = CakeSession::read('Oauth.Facebook.AfterCallback');
CakeSession::delete('Oauth.Facebook.AfterCallback');
if ($afterFacebookOauthCallback) {
    $googleAnalyticsConfig['page_referrer'] = '';
}

// カスタムディメンション
$googleAnalyticsConfig['custom_map'] = [
    'dimension1' => 'audience_id',
    'dimension2' => 'entrant_id',
    'dimension3' => 'entry_id',
    'dimension4' => 'account_id',
    'dimension5' => 'campaign_id'
];
$customDimensions = [];
if (isset($audience['Audience']['id'])) {
    $customDimensions['audience_id'] = $audience['Audience']['id'];
}
if (isset($entried['Entry']['entrant_id'])) {
    $customDimensions['entrant_id'] = $entried['Entry']['entrant_id'];
}
if (isset($entried['Entry']['id'])) {
    $customDimensions['entry_id'] = $entried['Entry']['id'];
}
if (isset($account['Account']['id'])) {
    $customDimensions['account_id'] = $account['Account']['id'];
}
if (isset($campaign['Campaign']['id'])) {
    $customDimensions['campaign_id'] = $campaign['Campaign']['id'];
}
?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= h($trackingId) ?>>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '<?= h($trackingId) ?>', <?= json_encode($googleAnalyticsConfig) ?>);

  <?php if (count($customDimensions) > 0) : ?>
    gtag('event', 'custom_dimension', <?= json_encode($customDimensions) ?>);
  <?php endif; ?>
</script>
