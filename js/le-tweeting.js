$.fn.setCursorPosition = function(pos) {
  this.each(function(index, elem) {
    if (elem.setSelectionRange) {
      elem.setSelectionRange(pos, pos);
    } else if (elem.createTextRange) {
      var range = elem.createTextRange();
      range.collapse(true);
      range.moveEnd('character', pos);
      range.moveStart('character', pos);
      range.select();
    }
  });
  return this;
};

$(function() {
    var submit = $('#tweet-form input[type=submit]');
    var status = $('#tweet-form textarea[name=status]');
    status.focus(function(){$(this).css('height', '4em')});
    $('a.action.reply').click(function() {
        var parent = $(this).parents('.tweet');
        var in_reply_to = parent.find('.screen-name').text();
        var in_reply_to_status_id = parent.attr('id').replace('tweet-', '');
        $('#tweet-form input[name=in_reply_to_status_id]').val(in_reply_to_status_id);
        $('#tweet-form h3 label').text('Reply to @' + in_reply_to);
        var text = '@' + in_reply_to + ' ';
        status.text(text).focus().setCursorPosition(text.length)
        return false;
    })
});