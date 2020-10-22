<?php

define('CLI_SCRIPT', true);
require(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php'); // global moodle config file.

global $DB;

$idcomposition = $DB->get_field('customfield_field', 'id', ['shortname' => 'up1composition']);
$idcomplement = $DB->get_field('customfield_field', 'id', ['shortname' => 'up1complement']);

echo "idcomposition : " .$idcomposition . "\n";
echo "idcomplement : " .$idcomplement . "\n";

$sql = "select * from {customfield_data} where fieldid=" . $idcomposition;
$compositions = $DB->get_records_sql($sql);


foreach ($compositions as $c) {
    $compl = $DB->get_record('customfield_data', ['instanceid' => $c->instanceid, 'fieldid' => $idcomplement]);
    $complement = trim($compl->value);
    $composition = trim($c->value);

    echo "situation départ " . $c->instanceid . ' : ' . $c->value . ' / ' . $compl->value . "\n";
    if ($composition != $complement) {
        $DB->update_record('customfield_data', ['id' => $compl->id, 'value' => $composition, 'charvalue' => $composition]);
        $DB->update_record('customfield_data', ['id' => $c->id, 'value' => $complement, 'charvalue' => $complement]);
        echo "Modif : " . $c->instanceid . "\n";
    }
}
?>
