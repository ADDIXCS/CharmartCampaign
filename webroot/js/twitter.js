var twitter;

window.twttr = (function(d, s, id) {
  var fjs, js, t;
  t = void 0;
  js = void 0;
  fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) {
    return;
  }
  js = d.createElement(s);
  js.id = id;
  js.src = "https://platform.twitter.com/widgets.js";
  fjs.parentNode.insertBefore(js, fjs);
  return window.twttr || (t = {
    _e: [],
    ready: function(f) {
      t._e.push(f);
    }
  });
})(document, "script", "twitter-wjs");

twitter = {
  ready: function(callback) {
    return twttr.ready(function(twttr) {
      return callback(twttr);
    });
  }
};
