# widgets.jsの読み込み
window.twttr = ((d, s, id) ->
  t = undefined
  js = undefined
  fjs = d.getElementsByTagName(s)[0]
  return  if d.getElementById(id)
  js = d.createElement(s)
  js.id = id
  js.src = "https://platform.twitter.com/widgets.js"
  fjs.parentNode.insertBefore js, fjs
  window.twttr or (t =
    _e: []
    ready: (f) ->
      t._e.push f
      return
  )
)(document, "script", "twitter-wjs")
# twitterオブジェクトの定義
twitter =
  # twttrオブジェクトが生成されてからコールバックを実行する
  ready: (callback) ->
    twttr.ready (twttr) ->
      callback(twttr)
