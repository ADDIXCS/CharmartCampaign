# ページトップへ戻るボタン
$('.pagetop').click (e) ->
  e.preventDefault()
  $('html, body').animate {scrollTop: 0}, 'slow', 'swing'
# 応募ボタンを押した際にローディングを表示
$('.js-btn-entry, #js-btn-submit').click (e) ->
  setTimeout( ->
    $('#loading').show()
  , 100)
# モーダルの位置を調整
$('.modal').on 'shown.bs.modal', (e) ->
  btn = $(e.relatedTarget)
  modal = $(e.currentTarget).find('.modal-dialog')
  marginTop = btn.offset().top - $(window).scrollTop() - modal.outerHeight() - 20
  if marginTop < 10
    marginTop = 10
    modal.find('.modal-content').removeClass('modal-content-triangle')
  else
    modal.find('.modal-content').addClass('modal-content-triangle')
  if not btn.hasClass('btn-lg')
    modal.find('.modal-content').removeClass('modal-content-triangle')
  modal.css 'margin-top', marginTop
  modal.find('.modal-content').addClass 'in'
$('.modal').on 'hide.bs.modal', (e) ->
  $(e.currentTarget).find('.modal-content').removeClass 'in'
# シェア
$('.js-btn-share').on 'click', (e) ->
  e.preventDefault()
  if $(this).find('i').hasClass('fa-facebook-square')
    width = 670
    height = 450
  else if $(this).find('i').hasClass('fa-twitter-square')
    width = 550
    height = 420
  else if $(this).find('i').hasClass('fa-google-plus-square')
    width = 510
    height = 360
  window.open(
    $(this).attr('href'),
    null,
    'width=' + width + ', height=' + height + ', menubar=no, toolbar=no, scrollbars=yes'
  )
# シェアのテキストエリアの文字数制限
# URLの正規表現は
# http://testcording.com/?p=2013
# を参考に少しカスタマイズ
shareMessageCount = ->
  val = $('#js-share-message-twitter').val()
  # twitterのメッセージボックスがない場合return
  if typeof(val) is 'undefined'
    return
  pattern = /https?:\/\/[a-zA-Z0-9\-]+\.[a-zA-Z]+[a-zA-Z0-9\-_\.:@!~*'\(¥);\/?&=\+$,%#]+/g
  # URLを抽出
  match = val.match(pattern)
  # URL以外の文字列の長さ
  length = val.replace(pattern, '').length
  # URLを文字数としてカウント
  if match
    for url in match
      if url.match(/^https/)
        length += 23
      else if url.match(/^http/)
        length += 22
  # 文字数に応じた処理
  if length > 140
    $('#js-share-left span').html '<span class="text-danger">' + length + '</span>'
    $('#js-btn-share').addClass 'disabled'
  else
    $('#js-share-left span').html length
    $('#js-btn-share').removeClass 'disabled'
$ ->
  shareMessageCount()
  $('#js-share-message-twitter').on 'keyup', ->
    shareMessageCount()
