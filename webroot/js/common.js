$(document).on('click', '.js-btn-file', function(e) {
  e.preventDefault();
  return $(this).closest('.form-inline').find('input[type=file]').click();
});

$(document).on('click', '.js-btn-file-delete', function(e) {
  var preview, previewId, wrapInput;
  e.preventDefault();
  wrapInput = $(this).closest('.form-inline');
  previewId = wrapInput.find('input[type=file]').attr('id');
  preview = $('.js-' + previewId + '-preview');
  if (preview.length) {
    preview.empty();
  } else {
    wrapInput.find('p').remove();
  }
  wrapInput.find('input[type=file]').replaceWith(wrapInput.find('input[type=file]').clone(true));
  wrapInput.find('input[type=checkbox]').attr('checked', true);
  return $(this).addClass('hide');
});

$(document).on('change', 'input[type=file]', function(e) {
  var file, fileReader, preview, self;
  preview = $('.js-' + $(this).attr('id') + '-preview');
  if (!window.File) {
    return;
  }
  if (!this.files.length) {
    if (preview.length) {
      preview.empty();
    } else {
      $(this).prev('p').remove();
    }
    return;
  }
  file = this.files[0];
  if (!file.type.match("image.*")) {
    if ($(this).attr('accept') === 'image/*') {
      alert("画像ファイルを選択してください");
      $(this).replaceWith($(this).clone(true));
    }
    if (preview.length) {
      preview.empty();
    } else {
      $(this).prev('p').remove();
    }
    return;
  }
  self = this;
  fileReader = new FileReader();
  fileReader.onload = function(e) {
    var img, wrapInput;
    img = $('<img>').attr('src', e.target.result);
    if (preview.length) {
      preview.html(img);
    } else {
      $(self).prev('p').remove();
      $(self).before($('<p class="image-preview">').append(img));
    }
    wrapInput = $(self).closest('.form-inline');
    wrapInput.find('input[type=checkbox]').attr('checked', false);
    return wrapInput.find('.js-btn-file-delete').removeClass('hide');
  };
  return fileReader.readAsDataURL(file);
});
