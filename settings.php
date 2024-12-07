<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Redis Cache Store - Settings
 *
 * @package   cachestore_mbsredis
 * @copyright   2024 ISB Bayern
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$settings->add(
    new admin_setting_configcheckbox(
        name: 'cachestore_mbsredis/test_clustermode',
        visiblename: get_string('clustermode', 'cachestore_mbsredis'),
        description: cache_helper::is_cluster_available() ?
            get_string('clustermode_help', 'cachestore_mbsredis') :
            get_string('clustermodeunavailable', 'cachestore_mbsredis'),
        defaultsetting: 0,
    )
);

$settings->add(
    new admin_setting_configtextarea(
        name: 'cachestore_mbsredis/test_server',
        visiblename: get_string('test_server', 'cachestore_mbsredis'),
        description: get_string('test_server_desc', 'cachestore_mbsredis'),
        defaultsetting: '',
        paramtype: PARAM_TEXT,
    )
);

$settings->add(new admin_setting_configcheckbox(
        'cachestore_mbsredis/test_encryption',
        get_string('encrypt_connection', 'cachestore_mbsredis'),
        get_string('encrypt_connection', 'cachestore_mbsredis'),
        false));
$settings->add(
    new admin_setting_configtext(
        'cachestore_mbsredis/test_cafile',
        get_string('ca_file', 'cachestore_mbsredis'),
        get_string('ca_file', 'cachestore_mbsredis'),
        '',
        PARAM_TEXT,
        16
    )
);
$settings->add(
    new admin_setting_configpasswordunmask(
        'cachestore_mbsredis/test_password',
        get_string('test_password', 'cachestore_mbsredis'),
        get_string('test_password_desc', 'cachestore_mbsredis'),
        ''
    )
);

if (class_exists('Redis')) { // Only if Redis is available.

    $options = array(Redis::SERIALIZER_PHP => get_string('serializer_php', 'cachestore_mbsredis'));

    if (defined('Redis::SERIALIZER_IGBINARY')) {
        $options[Redis::SERIALIZER_IGBINARY] = get_string('serializer_igbinary', 'cachestore_mbsredis');
    }

    $settings->add(new admin_setting_configselect(
            'cachestore_mbsredis/test_serializer',
            get_string('test_serializer', 'cachestore_mbsredis'),
            get_string('test_serializer_desc', 'cachestore_mbsredis'),
            Redis::SERIALIZER_PHP,
            $options
        )
    );
}

$settings->add(new admin_setting_configcheckbox(
        'cachestore_mbsredis/test_ttl',
        get_string('test_ttl', 'cachestore_mbsredis'),
        get_string('test_ttl_desc', 'cachestore_mbsredis'),
        false));
