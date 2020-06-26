$(document).ready(function() {

    if ($("#id_libre").prop('checked')) {
        $("#fitem_id_passwordv").addClass('cache');
        $("#fitem_id_enrolstartdatev").addClass('cache');
        $("#fitem_id_enrolenddatev").addClass('cache');
    }

    $("#id_libre").click(
        function() {
            if ($(this).prop('checked')) {
                $("#fitem_id_passwordv").addClass('cache');
                $("#fitem_id_enrolstartdatev").addClass('cache');
                $("#fitem_id_enrolenddatev").addClass('cache');
            } else {
                $("#fitem_id_passwordv").removeClass('cache');
                $("#fitem_id_enrolstartdatev").removeClass('cache');
                $("#fitem_id_enrolenddatev").removeClass('cache');
            }
    });
});
