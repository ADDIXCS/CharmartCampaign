<?php $this->extend('admin_edit'); ?>
<div class="section row" ng-app="soraps">
  <div class="col-sm-4">
    <div class="well">
      <?php echo Jade\Dumper::_html($this->fetch('content')); ?>
    </div>
  </div>
  <div class="col-sm-8">
    <ul class="nav nav-pills nav-pills-right section">
      <li>
        <a href="#js-tab-sp" data-toggle="tab">スマホ</a>
      </li>
      <li class="active">
        <a href="#js-tab-pc" data-toggle="tab">PC</a>
      </li>
    </ul>
    <div class="tab-content">
      <div id="js-tab-pc" class="design-preview-pc tab-pane fade in active">
        <div class="design-preview-content design-preview-pc-content">
          <?php echo Jade\Dumper::_html($this->renderPreview()); ?>
        </div>
      </div>
      <div id="js-tab-sp" class="design-preview-sp tab-pane fade">
        <div class="design-preview-content design-preview-sp-content">
          <?php echo Jade\Dumper::_html($this->renderPreview()); ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $this->start('script'); { ?>
  <?php echo Jade\Dumper::_html($this->Html->script('//ajax.googleapis.com/ajax/libs/angularjs/1.2.13/angular.min.js')); ?>
  <?php echo Jade\Dumper::_html($this->Html->script('angular-filters')); ?>
<?php } ?>
<?php $this->end(); ?>