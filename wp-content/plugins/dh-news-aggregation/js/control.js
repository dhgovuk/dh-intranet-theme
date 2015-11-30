jQuery(document).on('click','.na_upRow', function(event) {
    event.preventDefault();

    if(jQuery(this).parent().parent().prev().hasClass('category_container'))
    {
        var above = jQuery(this).parent().parent().prev();
        var current = jQuery(this).parent().parent();

        current.insertBefore(above);
    }
});

jQuery(document).on('click','.na_downRow', function(event) {
    event.preventDefault();

    if(jQuery(this).parent().parent().next().hasClass('category_container'))
    {
        var current = jQuery(this).parent().parent();
        var below = jQuery(this).parent().parent().next();

        current.insertAfter(below);
    }
});