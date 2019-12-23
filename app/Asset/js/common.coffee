# ファイル選択ボタンをクリックした際に本来のinputをクリック
$(document).on 'click', '.js-btn-file', (e) ->
  e.preventDefault()
  $(this).closest('.form-inline').find('input[type=file]').click()
# ファイル削除
$(document).on 'click', '.js-btn-file-delete', (e) ->
  e.preventDefault()
  wrapInput = $(this).closest('.form-inline')
  # プレビュー用の要素
  previewId = wrapInput.find('input[type=file]').attr('id')
  preview = $('.js-' + previewId + '-preview')
  # プレビューを削除
  if preview.length
    preview.empty()
  else
    wrapInput.find('p').remove()
  # フォームのデータを削除
  wrapInput.find('input[type=file]').replaceWith wrapInput.find('input[type=file]').clone(true)
  # 削除フラグをtrueに
  wrapInput.find('input[type=checkbox]').attr 'checked', true
  # 削除ボタンを隠す
  $(this).addClass('hide')
# ファイルを変更した際の処理
$(document).on 'change', 'input[type=file]', (e) ->
  # プレビュー用の要素
  preview = $('.js-' + $(this).attr('id') + '-preview')
  # File APIが使えるかの判別
  return  unless window.File
  # Fileがない場合
  unless @files.length
    if preview.length
      preview.empty()
    else
      $(this).prev('p').remove()
    return
  # 画像ファイルかどうかの判別
  file = @files[0]
  unless file.type.match("image.*")
    if $(this).attr('accept') is 'image/*'
      alert "画像ファイルを選択してください"
      $(this).replaceWith $(this).clone(true)
    if preview.length
      preview.empty()
    else
      $(this).prev('p').remove()
    return
  # 画像ファイルの挿入
  self = this
  fileReader = new FileReader()
  fileReader.onload = (e) ->
    img = $('<img>').attr('src', e.target.result)
    if preview.length
      preview.html img
    else
      $(self).prev('p').remove()
      $(self).before $('<p class="image-preview">').append(img)
    wrapInput = $(self).closest('.form-inline')
    # 削除フラグをfalseに
    wrapInput.find('input[type=checkbox]').attr 'checked', false
    # 削除ボタンの表示
    wrapInput.find('.js-btn-file-delete').removeClass('hide')
  # ファイルの読み込み
  fileReader.readAsDataURL file

