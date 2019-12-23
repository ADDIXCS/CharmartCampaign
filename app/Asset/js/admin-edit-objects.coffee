# オブジェクトの追加
$(document).on 'click', '[data-toggle="add-object"]', (e) ->
  e.preventDefault()
  targetStr  = $(this).data('target')
  parent     = $(this).closest $(this).data('parent')
  object     = parent.find('.object-base .object-' + targetStr).clone()
  wrapper    = parent.find('.object-' + targetStr + '-wrapper')
  i          = wrapper.find('.object-' + targetStr).length
  # 連番の置換
  object.find('legend').each ->
    $(this).html $(this).html().replace('{{' + targetStr + '}}', i)
  object.find('input, textarea').each ->
    $(this).attr 'name', $(this).attr('name').replace('{{' + targetStr + '}}', i)
    $(this).attr 'id', $(this).attr('id').replace('{{' + targetStr + '}}', i)
  object.find('label').each ->
    if $(this).attr('for')
      $(this).attr 'for', $(this).attr('for').replace('{{' + targetStr + '}}', i)
  # 追加
  wrapper.append object
  toggleDeleteBtn wrapper, targetStr
  # 設問追加時に回答を追加する
  if targetStr is 'question'
    object.find('[data-toggle="add-object"][data-target="answer"]').click()
# オブジェクトの削除
$(document).on 'click', '[data-toggle="remove-object"]', (e) ->
  e.preventDefault()
  targetStr = $(this).data('target')
  object    = $(this).closest '.object-' + targetStr
  parent    = $(this).closest $(this).data('parent')
  wrapper   = parent.find('.object-' + targetStr + '-wrapper')
  # 非表示
  object.hide()
  object.find('.object-' + targetStr + '-delete-flg').val true
  toggleDeleteBtn wrapper, targetStr
# 削除ボタンの表示切り替え
toggleDeleteBtn = (wrapper, targetStr) ->
  # アンケートの場合はすべて削除できる
  if editType? and editType is 'enquetes'
    return
  if wrapper.find('.object-' + targetStr + ':visible').length <= 1
    wrapper.find('[data-toggle="remove-object"][data-target="' + targetStr + '"]').hide()
  else
    wrapper.find('[data-toggle="remove-object"][data-target="' + targetStr + '"]').show()

# フォームのsubmit時に雛形を削除する
$(document).on 'click', 'button[type="submit"]', (e) ->
  e.preventDefault()
  $('.object-base').remove()
  $(this).closest('form').submit()

# ページ読み込み時
$ ->
  if editType? and editType isnt 'enquetes'
    targetStr = editType.slice 0, -1
    wrapper = $('.object-' + targetStr + '-wrapper')
    if wrapper.find('.object-' + targetStr + ':visible').length is 0
      # オブジェクトがない場合に追加する
      $('[data-toggle="add-object"][data-target="' + targetStr + '"]').click()
    else
      # オブジェクトが一つの場合に削除ボタンを非表示にする
      toggleDeleteBtn wrapper, targetStr
      if targetStr is 'question'
        $('.object-answer-wrapper').each ->
          toggleDeleteBtn $(this), 'answer'
