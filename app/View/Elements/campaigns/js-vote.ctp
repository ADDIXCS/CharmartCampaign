<?php
$apiUrl = $this->Html->url([
  'action' => 'vote',
  'accountName' => $currentAccount['Account']['account_name'],
  'id' => $campaign['Campaign']['id'],
]);
?>
<?php $this->start('script'); ?>
<script>
$('.js-btn-vote').on('click', function(e) {
  e.preventDefault();
  $('#loading').show();
  self = this;
  $.ajax({
    type: 'POST',
    url: '<?php echo $apiUrl; ?>',
    data: {
      itemId: $(this).attr('id').replace('item-', '')
    }
  }).done(function(res) {
    // 投票ボタン
    $(self).fadeOut('slow', function() {
      $(this).text('投票済み');
      $(this).removeAttr('id').removeClass('js-btn-vote').addClass('disabled').fadeIn('slow');
      $(this).off('click');
    });
    // 投票数
    voteCount = $(self).closest('.js-vote').find('.js-vote-count');
    $(voteCount).fadeOut('slow', function() {
      $(this).text(parseInt($(this).text()) + 1).fadeIn('slow');
    });
  }).fail(function(res) {
    alert('投票に失敗しました');
  }).always(function(res) {
    $('#loading').fadeOut()
  });
});
</script>
<?php $this->end(); ?>
