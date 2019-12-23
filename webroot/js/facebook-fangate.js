facebook.ready(function() {
  return FB.Event.subscribe('edge.create', function() {
    return FB.getLoginStatus(function(res) {
      if (res.authResponse) {
        return $.ajax({
          type: 'POST',
          url: apiUrl,
          data: {
            facebook_id: res.authResponse.userID
          }
        });
      }
    });
  });
});
