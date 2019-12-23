<?php $this->start('script'); ?>
<script>
$('[name="data[Entry][postcode]"]').on('change', function() {
  $.ajax({
    url: 'https://api.zipaddress.net/?zipcode='+$(this).val(),
    dataType: 'json',
    success: function(response) {
      if (response.code == 200) {
        $('[name="data[Entry][state]"]').val(response.data.pref);
        $('[name="data[Entry][city]').val(response.data.city);
        $('[name="data[Entry][street]').val(response.data.town);
      }
    }
  });
});
</script>
<?php $this->end(); ?>