/**
 * Show items in a node
 * @param items
 */
jQuery.fn.showEventsFhd = function (options) {
    'use strict';
    var containerEle = jQuery('<div class="aggregator"></div>'),
        itemWrapperEle = jQuery('<div class="item_container"></div>'),
        itemContainerEle = jQuery('<div class="row"></div>'),
        buttonPrevEle = jQuery('<div class="prev"><a>&nbsp;</a></div>'),
        buttonNextEle = jQuery('<div class="next"><a>&nbsp;</a></div>'),
        limit = options.limit || 4,
        offset = options.offset || 0,
        colClass = options.colClass || 'col-fhd-3',
        createCol = function (item) {
            return jQuery('<div class="' + colClass + '">' +
                '<div class="col-fhd-4">' +
                    '<div class="event-date">' +
                        item.start_day + '<br>' + item.start_month +
                    '</div>' +
                '</div>' +
                '<div class="col-fhd-8">' +
                    '<h4>' +
                        '<a href="' + item.wp_link + '" title="' + item.title + '">' +
                        item.title +
                        '</a>' +
                    '</h4>' +
                    '<strong>' + item.start_formatted + '</strong>' +
                    '<p>' + item.description + '</p>' +
                '</div>' +
            '</div>');
        },
        show = function () {
            //update buttons
            buttonPrevEle.removeClass('off');
            buttonNextEle.removeClass('off');

            if (offset <= 0) {
                buttonPrevEle.addClass('off');
            }

            if ((offset + limit) >= options.items.length) {
                buttonNextEle.addClass('off');
            }

            //update items
            itemContainerEle.html(""); //empty container before we add items.
            jQuery(options.items).each(function (key, item) {
                if (key >= offset && key < (offset + limit)) {
                    item.compiled.appendTo(itemContainerEle);
                }
            });
        },
        next = function (e) {
            e.preventDefault();
            if (!buttonNextEle.hasClass('off')) {
                offset += limit;
                show();
            }
        },
        prev = function (e) {
            e.preventDefault();
            if (!buttonPrevEle.hasClass('off')) {
                offset -= limit;
                show();
            }
        };

    //pre-compile all the items
    jQuery(options.items).each(function (k, item) {
        options.items[k].compiled = createCol(item);
    });

    buttonPrevEle.appendTo(containerEle).on('click', prev);
    buttonNextEle.appendTo(containerEle).on('click', next);
    itemWrapperEle.appendTo(containerEle);
    itemContainerEle.appendTo(itemWrapperEle);
    containerEle.appendTo(this);

    show();
};