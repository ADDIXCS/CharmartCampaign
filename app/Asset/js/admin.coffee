# フォームのクリア
$(document).on 'click', 'button[type=reset]', (e) ->
  e.preventDefault()
  $('input[type="text"], select, textarea').val('')
  $('input[type="radio"]').attr 'checked', false
  $('input[type="checkbox"]').attr 'checked', false
