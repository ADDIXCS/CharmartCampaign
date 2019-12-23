<?php $this->extend('/CampaignView/layout'); ?>
<?php $_campaign = $campaign; ?>
<?php $campaign = $_campaign['Campaign']; ?>
<?php $this->Soraps->setCampaignData($campaign); ?>
<?php $entried = !empty($entried) ? $entried['Entry'] : false; ?>
<?php echo Jade\Dumper::_html($this->element('campaigns/content-finish')); ?>
<div id="section-btn-top" class="section text-center">
  <?php if(in_array($campaign['campaign_type'], ['contest', 'vote'])): ?>
    <?php echo Jade\Dumper::_html($this->element('campaigns/btn-items')); ?>
  <?php endif; ?>
  <?php echo Jade\Dumper::_html($this->element('campaigns/btn-top')); ?>
</div>
<!-- クーポンの使用のjs -->
<?php if($campaign['campaign_type'] == 'coupon'): ?>
  <?php echo Jade\Dumper::_html($this->element('campaigns/js-coupon')); ?>
<?php endif; ?>