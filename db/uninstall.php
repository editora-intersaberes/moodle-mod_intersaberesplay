<?php
// This file is part of Moodle - http://moodle.org/
// Uninstall script for intersaberesplay module.

defined('MOODLE_INTERNAL') || die();

/**
 * Function to execute during module uninstall.
 *
 * @return bool
 */
function xmldb_intersaberesplay_uninstall() {
    global $DB;

    // Apagar dados relacionados, caso existam.
    // Exemplo de tabela filha:
    // $DB->delete_records('intersaberesplay_childtable', []);

    // Apagar dados principais do módulo (mesmo que já seja feito pelo Moodle normalmente)
    $DB->delete_records('intersaberesplay', []);

    return true;
}
