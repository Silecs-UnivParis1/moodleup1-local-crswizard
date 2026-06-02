document.addEventListener('DOMContentLoaded', function() {
    let informations = document.querySelectorAll('div.sylrofinfo');
    if (informations) {
        for (let i = 0; i < informations.length; i++) {
            let info = informations[i];
            let infoid = info.id;
            let targetname = 'id_' + infoid.substring(5);
            if (targetname == 'id_syl_obligatoire') {
                targetname = 'id_syl_obligatoire_description';
            }
            let target = document.getElementById(targetname);
            if (target) {
                let texte = info.innerHTML;
                let htmlinfo = '<div class="form-defaultinfo text-muted" style="padding-left:5px;">'+texte+'</div>';
                target.insertAdjacentHTML('afterend', htmlinfo);
                info.remove();
            }
        }
    }
});
