<?php
function xmldb_intersaberesplay_upgrade($oldversion)
{
    global $DB;

    $dbman = $DB->get_manager();

    // ------------------------------
    // Versão 2025111000 - curso e disciplina
    // ------------------------------
    if ($oldversion < 2025111000) {
        $table = new xmldb_table('intersaberesplay');

        // Campo curso
        if (!$dbman->field_exists($table, new xmldb_field('curso'))) {
            $field = new xmldb_field('curso', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $dbman->add_field($table, $field);
        }

        // Campo disciplina
        if (!$dbman->field_exists($table, new xmldb_field('disciplina'))) {
            $field = new xmldb_field('disciplina', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'curso');
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2025111000, 'intersaberesplay');
    }

    /**
     * Abrir em nova página Versão 2025111300
     */
    if ($oldversion < 2025111300) {
        $table = new xmldb_table('intersaberesplay');

        // Novo campo: abrir em nova aba
        $field = new xmldb_field('targetblank', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2025111300, 'intersaberesplay');
    }

    return true;
}
