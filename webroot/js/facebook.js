var facebook;

$(function() {
  return $.getScript('//connect.facebook.net/ja_JP/all.js', function() {
    return FB.init({
      appId: facebookAppId,
      cookie: true,
      xfbml: true
    });
  });
});

facebook = {
  ready: function(callback, count) {
    var self;
    if (!count) {
      count = 0;
    }
    if (typeof FB !== 'undefined') {
      return callback();
    } else if (count >= 200) {
      return alert('Facebookの初期化に失敗しました。ブラウザを再読み込みしてください');
    } else {
      self = this;
      return setTimeout((function() {
        return self.ready(callback, count + 1);
      }), 50);
    }
  },
  requireLogin: function(callback, options) {
    return this.ready(function() {
      return FB.getLoginStatus(function(res) {
        if (res.authResponse) {
          return callback(res);
        } else {
          return location.reload();
        }
      });
    });
  },
  requirePublishActions: function(callback, loginUrl) {
    return this.ready(function() {
      return FB.api('me/permissions', function(res) {
        if ((res.data != null) && (res.data[0].publish_actions != null)) {
          return callback();
        } else {
          return location.href = loginUrl;
        }
      });
    });
  }
};
