<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade function for the plugin.
 * 
 * @param int $oldversion The old version of the plugin
 * @return bool
 */
function xmldb_local_aiassistant_upgrade($oldversion) {
    global $DB;
    
    $dbman = $DB->get_manager();
    
    // Aquí puedes agregar upgrades futuros según sea necesario
    // Por ejemplo:
    // if ($oldversion < 2025102801) {
    //     // Hacer cambios en la base de datos
    //     upgrade_plugin_savepoint(true, 2025102801, 'local', 'aiassistant');
    // }
    
    return true;
}