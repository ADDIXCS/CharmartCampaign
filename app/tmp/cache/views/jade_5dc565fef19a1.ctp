<?php $this->extend('admin_edit_preview'); ?>
<?php
  echo $this->Form->create('Campaign', [
    'inputDefaults' => [
      'label' => ['class' => false],
      'wrapInput' => false,
    ],
    'class' => false,
    'angular' => true,
  ]);
  echo $this->Form->input('title', [
    'type' => 'hidden',
  ]);
?>
<fieldset>
  <?php
    // $entryTypes = "";
    // if(!in_array($campaign['Campaign']['campaign_type'], ['lottery'])){
    // $entryTypes .= $this->Form->input('email_entry_flg', [
    //     'type' => 'checkbox',
    //     'label' => 'ソーシャルアカウントなし',
    //     'div' => false,
    //     'class' => false,
    //   ]);
    // }
    |
    // if($currentAccount['Account']['facebook_id']) {
    //   $entryTypes .= $this->Form->input('facebook_entry_flg', [
    //     'type' => 'checkbox',
    //     'label' => 'Facebook',
    //     'div' => false,
    //     'class' => false,
    //   ]);
    // }
    // if($currentAccount['Account']['twitter_id']) {
    //   $entryTypes .= $this->Form->input('twitter_entry_flg', [
    //     'type' => 'checkbox',
    //     'label' => 'Twitter',
    //     'div' => false,
    //     'class' => false,
    //   ]);
    // }
    // echo $this->Form->input('validate_entry_types', [
    //   'type' => 'text',
    //   'label' => '応募方法',
    //   'class' => 'hide',
    //   'afterInput' => $entryTypes,
    //   'required' => false, //ブラウザのバリデーションを無効化する
    // ]);
    // if(in_array($campaign['Campaign']['campaign_type'], ['lottery']) && $currentAccount['Account']['twitter_id'] == '' && $currentAccount['Account']['facebook_id'] == '')
    //   echo '<p class="alert alert-warning js-no-sns">このキャンペーンは、SNSアカウントからしか応募できません。お客様のFacebookページか、Twitterのアカウント登録が必要です。</p>';
  ?>
</fieldset>
<fieldset ng-show="facebook_entry_flg || twitter_entry_flg">
  <?php
    echo $this->Form->input('twitter_fangate_flg', [
      'type' => 'checkbox',
      'label' => 'Twitterファンゲートを有効にする',
      'class' => false,
      'default' => true,
      'div' => ['class' => 'form-group', 'ng-show' => 'twitter_entry_flg'],
    ]);
  ?>
</fieldset>
<!--
  fieldset(ng-show="facebook_entry_flg || twitter_entry_flg")
  <?php
    echo $this->Form->input('entry_share_flg', [
      'type' => 'checkbox',
      'label' => '応募時シェアを有効にする',
      'class' => false,
    ]);
  ?>
-->
<fieldset>
  <?php echo Jade\Dumper::_html($this->element('admin/inputs-input')); ?>
</fieldset>
<?php echo Jade\Dumper::_html($this->element('admin/btn-campaigns-edit')); ?>
<?php echo Jade\Dumper::_html($this->Form->end()); ?>
<?php
  echo "<script>window.onload=function(){if($('.js-no-sns').length>0)$('[type=submit]').attr('disabled','disabled')}</script>";
?>