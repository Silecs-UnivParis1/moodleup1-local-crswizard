$(document).ready(function() {
    var config = {
        separator: / \/ /,
        labels: ["Composante :", "Type de diplôme :"],
        required: false
    };

	$('#id_rattachements').transformIntoSubselects(config);
});

$(document).ready(function() {
    var config = {
        separator: / \/ /,
        labels: ["Niveau année :"],
        required: false,
        labelButton: 'Ajouter un Niveau année'
    };

    $('#id_up1niveauannee').transformIntoSubselects(config);
});

$(document).ready(function() {
    var config = {
        separator: / \/ /,
        labels: ["Semestre :"],
        required: false,
        labelButton: 'Ajouter un semestre'
    };

    $('#id_up1semestre').transformIntoSubselects(config);
});

$(document).ready(function() {
    var config = {
        separator: / \/ /,
        labels: ["Niveau :"],
        required: false,
        labelButton: 'Ajouter un niveau'
    };

    $('#id_up1niveau').transformIntoSubselects(config);
});
