$(document).ready(function() {
    function scrollToBottom($el)
    {
        $el.scrollTop($el.prop('scrollHeight'));
    }

    $(".autoscroll-to-bottom").each(function(index, el) {
        scrollToBottom($(el));
    });

    $('.history-load-more-btn').click(function(event){
        event.preventDefault();

        var $e = $(this);
        $e.trigger('blur');

        $.get($e.attr('href'), $e.data()).then(function(data) {
            if (data.last_record_id) {
                $e.data('last_record_id', data.last_record_id);
            }

            $('.load-more-container').after(data.html);
        });
    });


    $('body').on('click', '.message .close', function() {
        var $e = $(this);
        if (!confirm($e.data('confirm'))) {
            return;
        }

        $.post($e.data('url')).then(function(data) {
            $e.closest('.message').replaceWith('');
        }).fail(function(){
            console.log('Delete message error');
        });
    });

    $('.message-form form').submit(function(event) {
        event.preventDefault();

        var data = $(this).serialize();
        var url = $(this).attr('action');
        $('.field-message-text input, .field-message-text button').attr('disabled', 'disabled');

        $.post(url, data).then(function(data) {
            var $container = $('.message-container');
            $container.append(data.message);
            scrollToBottom($container);

            $('.field-message-text input').val('');
        }).always(function() {
            $('.field-message-text input, .field-message-text button').removeAttr('disabled');
        });
    });
});
