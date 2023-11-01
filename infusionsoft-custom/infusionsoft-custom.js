(function ($) {
    $("#infusionsoft-contact").submit(function () {
        var memberid = $("#memberid").val();
        $.ajax({
            url: WPURLS.siteurl + '/wp-admin/admin-ajax.php',
            type: 'GET',
            dataType: 'html',
            cache: false,
            data: {
                action: 'infusionsoft_form',
                memberid: memberid
            },
            success: function (data) {
                $('#infusionsoft-custom #result').html(data).show();
            }
        });
        return false;
    });
})(jQuery);
