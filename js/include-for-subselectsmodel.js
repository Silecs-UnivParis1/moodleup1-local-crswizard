$(document).ready(function() {
    var cpt= 1;
    $('select#id_selm2').transformIntoSubselects({
        separator: / \/ /,
        labels: ["Période :", "Cours :"]
    });

    if (! $("#id_modeletype_selm2").prop('checked')) {
        $("#fitem_id_selm2").addClass('cache');
        $("#bb_duplication").addClass('cache');
        $("#headerselectepi").addClass('cache');
    }

    var selm1 = $("#id_selm1").val();
    var text = $("#id_course_summary").children('option[value='+selm1+']').text();
    $("#id_selm1").closest('fieldset').after('<div id="text_summary" class="felement fselect text_summary"><span class="text_summary">'+text+'</span></div>');

    $("#id_modeletype_selm2").click(
        function() {
            if ($(this).prop('checked')) {
                $("#fitem_id_selm2").removeClass('cache');
                $("#bb_duplication").removeClass('cache');
                $("#headerselectepi").removeClass('cache');
            }
    });

    $("#id_modeletype_selm1").click(
        function() {
            if ($(this).prop('checked')) {
                $("#fitem_id_selm2").addClass('cache');
                $("#bb_duplication").addClass('cache');
                $("#headerselectepi").addClass('cache');
            }
    });

    $("#id_selm1").change(
        function() {
            var sel = this.value;
            var text = $("#id_course_summary").children('option[value='+sel+']').text();
            if ($('#text_summary').length) {
                $('#text_summary').remove();
            }
            $(this).closest('fieldset').after('<div id="text_summary" class="felement fselect text_summary"><span class="text_summary">'+text+'</span></div>');
    });
});
