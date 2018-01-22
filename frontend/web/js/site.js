$(document).ready(function() {
    $(".autoscroll-to-bottom").each(function(index, el) {
        $(el).scrollTop($(el).prop("scrollHeight"));
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
});
