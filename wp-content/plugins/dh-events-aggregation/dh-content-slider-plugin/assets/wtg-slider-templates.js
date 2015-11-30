jQuery.templates({
    slider:
    '<div class="slider">' +
    '<div class="slider item-container"></div>' +
    '</div>',

    events:
    '<div class="event-item">' +
    '<div class="event-date">{{:start_day}}<br>{{:start_month}}</div>' +
    '<div class="event-content"><h3><a href="{{:wp_link}}" title="{{:title}}">{{:title}}</a></h3>' +
    '<strong>{{:start_formatted}}</strong>' +
    '<p>{{:description}}</p>' +
    '</div></div>',

    category:
    '<div class="post"> ' +
        '<div class="meta">' +
            '<a href="{{:guid}}#comments" title="{{:comment_count}} Comment(s)">{{:comment_count}}</a>' +
            '<img src="{{:image[0]}}" alt="{{:post_title}}">' +
        '</div>' +
        '<h3>' +
            '<a href="{{:guid}}" title="{{:post_title}}">{{:post_title}}</a>' +
        '</h3>' +
        '<div>' +
            '<small>{{:post_date}}</small>' +
        '</div>' +
    '</div>'
});