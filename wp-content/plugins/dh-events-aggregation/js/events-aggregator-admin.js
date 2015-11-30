jQuery('body').delegate('.up', 'click', function(event) {
    event.preventDefault();

    if(jQuery(this).parent().parent().prev().attr('class') == 'category_container')
    {
        var above = jQuery(this).parent().parent().prev().contents();
        var current = jQuery(this).parent().parent().contents();

        jQuery(this).parent().parent().append(above);
        jQuery(this).parent().parent().prev().append(current);
    }
});

jQuery('body').delegate('.down', 'click', function(event) {
    event.preventDefault();

    if(jQuery(this).parent().parent().next().attr('class') == 'category_container')
    {
        var current = jQuery(this).parent().parent().contents();
        var below = jQuery(this).parent().parent().next().contents();

        jQuery(this).parent().parent().append(below);
        jQuery(this).parent().parent().next().append(current);
    }
});

var time_delay = 0;

jQuery('[name="event[end_date]"]').change(function() {
    var start_date = jQuery('[name="event[start_date]"]').datepicker("getDate");
    var end_date   = jQuery('[name="event[end_date]"]').datepicker("getDate");

    if(time_delay == 0) {
        if(start_date != null && end_date != null) {
            if(start_date > end_date) {
                alert('Warning! The end date you have selected is before the start date. Please amend the end date.');

                time_delay = 1;

                setTimeout(function(){
                    time_delay = 0;
                }, 2000);
            }
        }
    }
});

jQuery('[name="event[start_date]"]').change(function() {
    var start_date = jQuery('[name="event[start_date]"]').datepicker("getDate");
    var end_date   = jQuery('[name="event[end_date]"]').datepicker("getDate");

    if(time_delay == 0) {
        if(start_date != null && end_date != null) {
            if(start_date > end_date) {
                alert('Warning! The start date you have selected is after the end date. Please amend the end date.');

                time_delay = 1;

                setTimeout(function(){
                    time_delay = 0;
                }, 2000);
            }
        }

    }
});