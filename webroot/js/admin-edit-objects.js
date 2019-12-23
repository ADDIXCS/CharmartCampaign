var toggleDeleteBtn;

$(document).on('click', '[data-toggle="add-object"]', function(e) {
  var i, object, parent, targetStr, wrapper;
  e.preventDefault();
  targetStr = $(this).data('target');
  parent = $(this).closest($(this).data('parent'));
  object = parent.find('.object-base .object-' + targetStr).clone();
  wrapper = parent.find('.object-' + targetStr + '-wrapper');
  i = wrapper.find('.object-' + targetStr).length;
  object.find('legend').each(function() {
    return $(this).html($(this).html().replace('{{' + targetStr + '}}', i));
  });
  object.find('input, textarea').each(function() {
    $(this).attr('name', $(this).attr('name').replace('{{' + targetStr + '}}', i));
    return $(this).attr('id', $(this).attr('id').replace('{{' + targetStr + '}}', i));
  });
  object.find('label').each(function() {
    if ($(this).attr('for')) {
      return $(this).attr('for', $(this).attr('for').replace('{{' + targetStr + '}}', i));
    }
  });
  wrapper.append(object);
  toggleDeleteBtn(wrapper, targetStr);
  if (targetStr === 'question') {
    return object.find('[data-toggle="add-object"][data-target="answer"]').click();
  }
});

$(document).on('click', '[data-toggle="remove-object"]', function(e) {
  var object, parent, targetStr, wrapper;
  e.preventDefault();
  targetStr = $(this).data('target');
  object = $(this).closest('.object-' + targetStr);
  parent = $(this).closest($(this).data('parent'));
  wrapper = parent.find('.object-' + targetStr + '-wrapper');
  object.hide();
  object.find('.object-' + targetStr + '-delete-flg').val(true);
  return toggleDeleteBtn(wrapper, targetStr);
});

toggleDeleteBtn = function(wrapper, targetStr) {
  if ((typeof editType !== "undefined" && editType !== null) && editType === 'enquetes') {
    return;
  }
  if (wrapper.find('.object-' + targetStr + ':visible').length <= 1) {
    return wrapper.find('[data-toggle="remove-object"][data-target="' + targetStr + '"]').hide();
  } else {
    return wrapper.find('[data-toggle="remove-object"][data-target="' + targetStr + '"]').show();
  }
};

$(document).on('click', 'button[type="submit"]', function(e) {
  e.preventDefault();
  $('.object-base').remove();
  return $(this).closest('form').submit();
});

$(function() {
  var targetStr, wrapper;
  if ((typeof editType !== "undefined" && editType !== null) && editType !== 'enquetes') {
    targetStr = editType.slice(0, -1);
    wrapper = $('.object-' + targetStr + '-wrapper');
    if (wrapper.find('.object-' + targetStr + ':visible').length === 0) {
      return $('[data-toggle="add-object"][data-target="' + targetStr + '"]').click();
    } else {
      toggleDeleteBtn(wrapper, targetStr);
      if (targetStr === 'question') {
        return $('.object-answer-wrapper').each(function() {
          return toggleDeleteBtn($(this), 'answer');
        });
      }
    }
  }
});
