$(document).on('click', 'button[type=reset]', function(e) {
  e.preventDefault();
  $('input[type="text"], select, textarea').val('');
  $('input[type="radio"]').attr('checked', false);
  return $('input[type="checkbox"]').attr('checked', false);
});
