/**
 * Show items in a node
 * @param items
 */
(function () {
    jQuery.fn.wtgSlider = function (options) {
        'use strict';
        var $this = this,
            $containerEle = jQuery(jQuery.templates.slider.render()),
            $buttonPrevEle = $containerEle.find('.prev'),
            $buttonNextEle = $containerEle.find('.next'),
            $itemContainerEle = $containerEle.find('.item-container'),
            limit = options.limit || 4,
            offset = options.offset || 0,
            colClass = options.colClass || '',
            template = options.tpl,
            /**
             * handle requesting the next / previous set of items using a HTTP data source
             */
            showHttp = function () {
                var data = options.source.params;

                data.offset = offset;
                data.count = limit;

                //data disable buttons
                $buttonNextEle.addClass('off');

                jQuery.ajax({
                    url: options.source.url,
                    type: options.source.method,
                    data: data,
                    dataType: 'json'
                }).done(function (data) {
                    if (data.post_count == 0) {
                        $this.remove();
                    }
                    if ((offset + limit) < data.post_count) {
                        $buttonNextEle.removeClass('off');
                    }
                    $itemContainerEle.html(""); //empty container before we add items.
                    jQuery(data.posts).each(function (key, item) {
                        var content = jQuery('<div class="' + colClass + '"></div>')
                            .append(jQuery.templates[template].render(item));

                        content.appendTo($itemContainerEle)
                            .find('img')
                            .on('load', alignButtons); // fixes the arrow alignment bug
                    });
                    jQuery(':last-child', $itemContainerEle).addClass('last-child');
                });
            },
            /**
             * show next / previous items based on offset and the items options
             */
            showArray = function () {
                if ((offset + limit) < options.items.length) {
                    $buttonNextEle.removeClass('off');
                }
                $itemContainerEle.html(""); //empty container before we add items.
                jQuery(options.items).each(function (key, item) {
                    if (key >= offset && key < (offset + limit)) {
                        $itemContainerEle.append(item.compiled[0].outerHTML);
                        // Originally the method below was used for appending,
                        // but Internet Explorer did not like that.
                        // item.compiled.appendTo($itemContainerEle);
                    }
                });
                jQuery(':last-child', $itemContainerEle).addClass('last-child');
                alignButtons();
            },
            /**
             * this is the generic function called to handle showing next and previous items and decides on the showArray
             * or showHttp functions based on the source or items property - source will be the primary data source if
             * available
             */
            show = function () {
                //update buttons
                $buttonPrevEle.addClass('off');
                $buttonNextEle.addClass('off');
                if (offset >= limit) {
                    $buttonPrevEle.removeClass('off');
                }
                if (options.source) {
                    showHttp();
                }
                else if (options.items) {
                    showArray();
                }
                else {
                    throw "not implemented!";
                }
            },
            /**
             * handler for when the next button is clicked
             * @param e
             */
            next = function (e) {
                e.preventDefault();
                if (!$buttonNextEle.hasClass('off')) {
                    offset += limit;
                    show();
                }
            },
            /**
             * handler for when the previous button is clicked.
             * @param e
             */
            prev = function (e) {
                e.preventDefault();
                if (!$buttonPrevEle.hasClass('off')) {
                    offset -= limit;
                    show();
                }
            },
            /**
             * Corrects the height position of the next / previous buttons when new content is rendered into the slider.
             */
            alignButtons = function () {
                var topOffset = ($containerEle.height() / 3) - ($buttonNextEle.height() / 2),
                    style = { marginTop: '25px' };
                $buttonNextEle.css(style);
                $buttonPrevEle.css(style);
            };


        //pre-compile all the items, only if they are set!
        if (options.items) {
            jQuery(options.items).each(function (k, item) {
                options.items[k].compiled = jQuery('<div class="' + colClass + '"></div>')
                    .append(jQuery.templates[template].render(item));
            });
        }

        $buttonPrevEle.on('click', prev);
        $buttonNextEle.on('click', next);
        $containerEle.appendTo(this);
        //display the widget
        show();
    };
})(jQuery);
