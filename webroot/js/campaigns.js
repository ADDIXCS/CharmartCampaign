var shareMessageCount;

$('.pagetop').click(function(e) {
  e.preventDefault();
  return $('html, body').animate({
    scrollTop: 0
  }, 'slow', 'swing');
});

$('.js-btn-entry, #js-btn-submit').click(function(e) {
  return setTimeout(function() {
    return $('#loading').show();
  }, 100);
});

$('.modal').on('shown.bs.modal', function(e) {
  var btn, marginTop, modal;
  btn = $(e.relatedTarget);
  modal = $(e.currentTarget).find('.modal-dialog');
  marginTop = btn.offset().top - $(window).scrollTop() - modal.outerHeight() - 20;
  if (marginTop < 10) {
    marginTop = 10;
    modal.find('.modal-content').removeClass('modal-content-triangle');
  } else {
    modal.find('.modal-content').addClass('modal-content-triangle');
  }
  if (!btn.hasClass('btn-lg')) {
    modal.find('.modal-content').removeClass('modal-content-triangle');
  }
  modal.css('margin-top', marginTop);
  return modal.find('.modal-content').addClass('in');
});

$('.modal').on('hide.bs.modal', function(e) {
  return $(e.currentTarget).find('.modal-content').removeClass('in');
});

$('.js-btn-share').on('click', function(e) {
  var height, width;
  e.preventDefault();
  if ($(this).find('i').hasClass('fa-facebook-square')) {
    width = 670;
    height = 450;
  } else if ($(this).find('i').hasClass('fa-twitter-square')) {
    width = 550;
    height = 420;
  } else if ($(this).find('i').hasClass('fa-google-plus-square')) {
    width = 510;
    height = 360;
  }
  return window.open($(this).attr('href'), null, 'width=' + width + ', height=' + height + ', menubar=no, toolbar=no, scrollbars=yes');
});

shareMessageCount = function() {
  var i, len, length, match, pattern, url, val;
  val = $('#js-share-message-twitter').val();
  if (typeof val === 'undefined') {
    return;
  }
  pattern = /https?:\/\/[a-zA-Z0-9\-]+\.[a-zA-Z]+[a-zA-Z0-9\-_\.:@!~*'\(¥);\/?&=\+$,%#]+/g;
  match = val.match(pattern);
  length = val.replace(pattern, '').length;
  if (match) {
    for (i = 0, len = match.length; i < len; i++) {
      url = match[i];
      if (url.match(/^https/)) {
        length += 23;
      } else if (url.match(/^http/)) {
        length += 22;
      }
    }
  }
  if (length > 140) {
    $('#js-share-left span').html('<span class="text-danger">' + length + '</span>');
    return $('#js-btn-share').addClass('disabled');
  } else {
    $('#js-share-left span').html(length);
    return $('#js-btn-share').removeClass('disabled');
  }
};

$(function() {
  shareMessageCount();
  return $('#js-share-message-twitter').on('keyup', function() {
    return shareMessageCount();
  });
});
