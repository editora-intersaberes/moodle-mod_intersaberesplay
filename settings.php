<?php
defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    // Campo de token global.
    $settings->add(new admin_setting_configtext(
        'mod_intersaberesplay/token',
        get_string('token', 'mod_intersaberesplay'),
        get_string('token_desc', 'mod_intersaberesplay'),
        '', // valor padrão
        PARAM_TEXT
    ));
}
