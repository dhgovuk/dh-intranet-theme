jQuery(document).ready(function ($) {
    $('input.subscribe_checkbox').on('change', function () {
        var catID = $(this).data('value');
        $.ajax('/wp-admin/admin-post.php?action=dh_subscribe_category', {
            type: 'POST',
            data: {
                cat_ID: catID,
                enabled: $(this).is(':checked') ? 1 : 0
            }
        }).success(function (data) {

        });
    })
});