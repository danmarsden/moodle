<?php
defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) { // needs this condition or there is error on login page

    $settings = new admin_settingpage('local_kaltura', get_string('pluginname', 'local_kaltura'));
    $ADMIN->add('localplugins', $settings);


    $settings->add(new admin_setting_heading('servicesettings', get_string('servicesettings', 'local_kaltura') , ''));

    $settings->add(new admin_setting_configtext('local_kaltura/server_uri',
        get_string('serveruri', 'local_kaltura'), get_string('serveruri-explanation', 'local_kaltura'), 'http://www.kaltura.com', PARAM_TEXT));

    $settings->add(new admin_setting_configtext('local_kaltura/partner_id',
        get_string('partnerid', 'local_kaltura'), null, null, PARAM_TEXT, 8));

    $settings->add(new admin_setting_configpasswordunmask('local_kaltura/secret',
        get_string('secret', 'local_kaltura'), null, null, PARAM_TEXT, 8));

    $settings->add(new admin_setting_configpasswordunmask('local_kaltura/admin_secret',
        get_string('adminsecret', 'local_kaltura'), null, null, PARAM_TEXT, 8));


    $settings->add(new admin_setting_heading('pluginsettings', get_string('pluginsettings', 'local_kaltura') , ''));

    $settings->add(new admin_setting_configtext('local_kaltura/uploader_regular',
        get_string('uploaderregular', 'local_kaltura'), null, '1002217', PARAM_TEXT, 8));

    $settings->add(new admin_setting_configtext('local_kaltura/player_regular_dark',
        get_string('playerregulardark', 'local_kaltura'), null, '1466342', PARAM_TEXT, 8));

    $settings->add(new admin_setting_configtext('local_kaltura/player_regular_light',
        get_string('playerregularlight', 'local_kaltura'), null, '1466432', PARAM_TEXT, 8));

    $settings->add(new admin_setting_configtext('local_kaltura/player_mix_dark',
        get_string('playermixdark', 'local_kaltura'), null, '1466482', PARAM_TEXT, 8));

    $settings->add(new admin_setting_configtext('local_kaltura/player_mix_light',
        get_string('playermixlight', 'local_kaltura'), null, '1496582', PARAM_TEXT, 8));

    $settings->add(new admin_setting_configtext('local_kaltura/video_presentation',
        get_string('videopresentation', 'local_kaltura'), null, '1003069', PARAM_TEXT, 8));

}
