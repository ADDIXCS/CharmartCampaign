<?php
$apiUrl = $this->Html->url([
  'action' => 'uses',
  'accountName' => $currentAccount['Account']['account_name'],
  'id' => $campaign['Campaign']['id'],
]);
?>
<?php $this->start('script'); ?>
<script>
$('#js-btn-coupon').on('click', function(e) {
  e.preventDefault();
  if(confirm('クーポンを使用します')) {
    $('#loading').show();
    self = this;
    $.ajax({
      type: 'POST',
      url: '<?php echo $apiUrl; ?>'
    }).done(function(res) {
      $(self).fadeOut('slow', function() {
        $(this).text('使用済み');
        $(this).removeAttr('id').addClass('disabled').fadeIn('slow');
        $(this).off('click');
      });
    }).fail(function(res) {
      alert('失敗しました');
    }).always(function(res) {
      $('#loading').fadeOut()
    });
  }
});
</script>
<?php $this->end(); ?>
