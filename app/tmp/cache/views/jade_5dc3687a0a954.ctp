<?php $this->extend('/CampaignView/layout'); ?>
<?php $_campaign = $campaign; ?>
<?php $campaign = $_campaign['Campaign']; ?>
<?php $this->Soraps->setCampaignData($campaign); ?>
<?php if($this->Soraps->isDisplay('top_image_flg') && $this->Soraps->isDisplay('top_image')): ?>
  <div id="section-header" class="section section-bg text-center" ng-show="top_image_flg">
    <div class="container container-img">
      <?php echo Jade\Dumper::_html($this->element('campaigns/top-image')); ?>
    </div>
  </div>
<?php endif; ?>
<div id="section-info" class="section-lg separator-triangle">
  <div class="container">
    <div class="row">
      <?php if($this->Soraps->isDisplay('gift_image_flg') && $this->Soraps->isDisplay('gift_image')): ?>
        <div class="col-sm-4" ng-show="gift_image_flg">
          <?php if($this->Soraps->isPreview()): ?>
            <div class="js-CampaignGiftImage-preview">
              <?php if($giftImage = $this->S3->image($campaign, 'Campaign.gift_image')): ?>
                <?php echo Jade\Dumper::_html($giftImage); ?>
              <?php else: ?>
                <img src="//placehold.it/320x320" />
              <?php endif; ?>
            </div>
          <?php else: ?>
            <?php echo Jade\Dumper::_html($this->S3->image($campaign, 'Campaign.gift_image')); ?>
          <?php endif; ?>
        </div>
      <?php endif; ?>
      <div class="col-sm-8">
        <dl class="dl-horizontal">
          <dt>開催期間</dt>
          <dd>
            <?php echo Jade\Dumper::_text($this->Soraps->date('start_date')); ?> 〜
            <span><?php echo Jade\Dumper::_text($this->Soraps->date('end_date')); ?></span>
          </dd>
          <?php if($this->Soraps->isDisplay('gift_name')): ?>
            <dt ng-show="gift_name">
              <?php echo Jade\Dumper::_text($campaign['campaign_type'] !== 'coupon' ? '景品名' : 'クーポン名'); ?>
            </dt>
            <dd ng-show="gift_name"><?php echo Jade\Dumper::_html($this->Soraps->textarea('gift_name')); ?></dd>
          <?php endif; ?>
          <?php if($this->Soraps->isDisplay('win_count')): ?>
            <dt ng-show="win_count">当選数</dt>
            <dd ng-show="win_count"><?php echo Jade\Dumper::_html($this->Soraps->textarea('win_count')); ?></dd>
          <?php endif; ?>
          <?php if($this->Soraps->isDisplay('coupon_limit')): ?>
            <dt ng-show="coupon_limit">配布枚数</dt>
            <dd ng-show="coupon_limit"><?php echo Jade\Dumper::_html($this->Soraps->textarea('coupon_limit')); ?>枚</dd>
          <?php endif; ?>
          <?php if($this->Soraps->isDisplay('announce_date_flg')): ?>
            <dt ng-show="announce_date_flg">当選発表日</dt>
            <dd ng-show="announce_date_flg"><?php echo Jade\Dumper::_text($this->Soraps->date('announce_date')); ?></dd>
          <?php endif; ?>
        </dl>
      </div>
    </div>
  </div>
</div>
<div id="section-summary" class="section-lg section-bg separator-triangle">
  <div class="container">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <h2 class="panel-title">
          <a class="visible-xs" href="#js-panel-summary" data-toggle="collapse">
            キャンペーン概要
            <?php echo Jade\Dumper::_html($this->Html->icon('plus')); ?>
            <?php echo Jade\Dumper::_html($this->Html->icon('minus')); ?>
          </a>
          <span class="hidden-xs">キャンペーン概要</span>
        </h2>
      </div>
      <div id="js-panel-summary" class="panel-body panel-body-bg in">
        <p>
          <strong><?php echo Jade\Dumper::_html($this->Soraps->text('title')); ?></strong>
        </p>
        <p><?php echo Jade\Dumper::_html($this->Soraps->textarea('summary')); ?></p>
      </div>
    </div>
  </div>
</div>
<?php if(in_array($campaign['campaign_type'], ['contest', 'vote', 'lancers']) && $items): ?>
  <div id="section-pickupitems" class="section-lg section-items separator-triangle">
    <div class="container">
      <div class="page-header">
        <h2 class="text-center">ピックアップ</h2>
      </div>
      <div class="row">
        <?php echo Jade\Dumper::_html($this->element('campaigns/content-pickupitems')); ?>
      </div>
      <?php if($campaign['campaign_type'] == 'contest'): ?>
        <div class="section-sm text-center">
          <?php echo Jade\Dumper::_html($this->element('campaigns/btn-items')); ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>
<?php if($this->Soraps->isDisplay('notice')): ?>
  <div id="section-notice" class="section-lg section-bg separator-triangle" ng-show="notice">
    <div class="container">
      <div class="panel panel-primary">
        <div class="panel-heading">
          <h2 class="panel-title">
            <?php if($this->Soraps->isPreview()): ?>
              <a class="visible-xs" href="#js-panel-notice" data-toggle="collapse">
                注意事項
                <?php echo Jade\Dumper::_html($this->Html->icon('plus')); ?>
                <?php echo Jade\Dumper::_html($this->Html->icon('minus')); ?>
              </a>
            <?php else: ?>
              <a class="collapsed visible-xs" href="#js-panel-notice" data-toggle="collapse">
                注意事項
                <?php echo Jade\Dumper::_html($this->Html->icon('plus')); ?>
                <?php echo Jade\Dumper::_html($this->Html->icon('minus')); ?>
              </a>
            <?php endif; ?>
            <span class="hidden-xs">注意事項</span>
          </h2>
        </div>
        <?php if($this->Soraps->isPreview()): ?>
          <div id="js-panel-notice" class="panel-body panel-body-bg flap">
            <p><?php echo Jade\Dumper::_html($this->Soraps->textarea('notice')); ?></p>
          </div>
        <?php else: ?>
          <div id="js-panel-notice" class="panel-body panel-body-bg flap collapse">
            <p><?php echo Jade\Dumper::_html($this->Soraps->textarea('notice')); ?></p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
<?php endif; ?>
<?php echo Jade\Dumper::_html($this->element('campaigns/section-sponsor')); ?>
<div id="section-share-btns" class="section">
  <div class="container container-xs text-center">
    <p class="lead">友達にシェアしよう！</p>
    <?php $shareUrl = rawurlencode($this->Html->url(null, true)); ?>
    <?php $shareTitle = rawurlencode($this->Soraps->title()); ?>
    <div class="row">
      <div class="col-sm-2 col-sm-offset-3 col-xs-2 col-xs-offset-2">
        <a class="js-btn-share" href="http://www.facebook.com/share.php?u=<?php echo Jade\Dumper::_text($shareUrl); ?>">
          <?php echo Jade\Dumper::_html($this->Html->icon('facebook-square')); ?>
        </a>
      </div>
      <div class="col-sm-2 col-xs-2">
        <a class="js-btn-share" href="https://twitter.com/share?text=<?php echo Jade\Dumper::_text($shareTitle); ?>&amp;url=<?php echo Jade\Dumper::_text($shareUrl); ?>">
          <?php echo Jade\Dumper::_html($this->Html->icon('twitter-square')); ?>
        </a>
      </div>
      <div class="col-sm-2 col-xs-2 visible-xs">
        <a href="line://msg/text/<?php echo Jade\Dumper::_text($shareTitle); ?>%0D%0A<?php echo Jade\Dumper::_text($shareUrl); ?>">
          <?php echo Jade\Dumper::_html($this->Html->image('campaigns/btn_line.png', ['width' => 40])); ?>
        </a>
      </div>
    </div>
  </div>
</div>
<div id="section-entry-btn-fixed" class="section-sm">
  <div class="container">
    <a class="pagetop hidden-xs" href="#top"><?php echo Jade\Dumper::_html($this->Html->icon('arrow-circle-up', '3x')); ?></a>
    <div class="btn-shadow">
      <?php if(in_array($campaign['campaign_type'], ['vote', 'lancers'])): ?>
        <?php echo Jade\Dumper::_html($this->element('campaigns/btn-items', ['block' => true])); ?>
      <?php else: ?>
        <?php echo Jade\Dumper::_html($this->element('campaigns/btn-entry')); ?>
      <?php endif; ?>
    </div>
  </div>
</div>