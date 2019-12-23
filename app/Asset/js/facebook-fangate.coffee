# いいね！ボタンを押した際にデータベースにデータを保存する
facebook.ready ->
  FB.Event.subscribe 'edge.create', ->
    FB.getLoginStatus (res) ->
      if res.authResponse
        $.ajax
          type: 'POST'
          url: apiUrl
          data:
            facebook_id: res.authResponse.userID
