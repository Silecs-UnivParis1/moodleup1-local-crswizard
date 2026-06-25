document.addEventListener('DOMContentLoaded', function() {
    let helpsyllabus = document.querySelector('div.referencesyllabushelp');
    if (helpsyllabus) {
        let targethelp = document.getElementById('id_syl_reference_description');
        if (targethelp) {
            let texte = helpsyllabus.innerHTML;
            let htmlinfo = '<div class="form-defaultinfo text-muted" style="padding-left:10px;">'+texte+'</div>';
            targethelp.insertAdjacentHTML('afterend', htmlinfo);
            helpsyllabus.remove();
        }
    }
    let referencesyllabusinfo = document.querySelector('div.referencesyllabusinfo');
    if (referencesyllabusinfo) {
        let targetinfo = document.getElementById('id_error_syl_reference');
        if (targetinfo) {
            targetinfo.insertAdjacentHTML('afterend', '<div class="refsyllabusinfo">' + referencesyllabusinfo.innerHTML + '</div>');
            referencesyllabusinfo.remove();
        }
    }
});
