# SDKの読み込みとFBオブジェクトの初期化
$ ->
  $.getScript '//connect.facebook.net/ja_JP/all.js', ->
    FB.init
      appId: facebookAppId
      cookie: true
      xfbml: true
# facebookオブジェクトの定義
facebook =
  # FBオブジェクトが生成されてからコールバックを実行する
  ready: (callback, count) ->
    if not count
      count = 0
    if typeof(FB) isnt 'undefined'
      callback()
    else if count >= 200
      alert 'Facebookの初期化に失敗しました。ブラウザを再読み込みしてください'
    else
      self = this
      setTimeout (-> self.ready(callback, count + 1)), 50
  # アプリ認証してコールバックを実行する
  requireLogin: (callback, options) ->
    this.ready ->
      FB.getLoginStatus (res) ->
        if res.authResponse
          callback(res)
        else
          location.reload()
  # publish_actionsのパーミッションを取得してコールバックを実行する
  requirePublishActions: (callback, loginUrl) ->
    this.ready ->
      FB.api 'me/permissions', (res) ->
        if res.data? and res.data[0].publish_actions?
          callback()
        else
          location.href = loginUrl

