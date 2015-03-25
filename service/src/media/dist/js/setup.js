(function ($) {
    // Navigation
    var $nav = $('.nav');

    // Hide pages on load
    $nav.find('.nav-page').hide();

    // Show pages on header click
    $nav.on('click', '.nav-header', function (e) {
        e.preventDefault();

        var $header = $(this);

        if ($header.data('isOpen') === true) {
            $header.data('isOpen', false);
            $header.nextUntil('.nav-header').hide();
        } else {
            $header.data('isOpen', true);
            $header.nextUntil('.nav-header').show();
        }
    });
})(jQuery);

(function ($) {
    var $exemple = $('.page-article');
    $exemple.find('.exemple').hide();

    $exemple.on('click', '.exemple-window', function (i) {
        i.preventDefault();

        var $header = $(this);

        if ($header.data('isOpen') === true) {
            $header.data('isOpen', false);
            $header.nextUntil('.exemple-window').hide();
        } else {
            $header.data('isOpen', true);
            $header.nextUntil('.exemple-window').show();
        }
    });
})(jQuery);