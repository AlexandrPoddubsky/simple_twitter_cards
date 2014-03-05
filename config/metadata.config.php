<?php
return array(
    'name'    => 'Simple Twitter cards',
    'version' => '0.1',
    'icons' => array(
        16 => 'static/apps/simple_twitter_cards/images/icon-16.png',
        32 => 'static/apps/simple_twitter_cards/images/icon-32.png',
        64 => 'static/apps/simple_twitter_cards/images/icon-64.png',
    ),
    'provider' => array(
        'name' => 'Foine',
    ),
    'namespace' => 'Twitter\Card',
    'permission' => array(
    ),
    'extends' => array('local'),
    'requires' => array('lib_options'),
    'launchers' => array(
        'simple_twitter_cards_launcher_configuration' => array(
            'name' => 'Simple Twitter cards',
            'icon64' => 'static/apps/simple_twitter_cards/images/icon-64.png',
            'action' => array(
                'action' => 'nosTabs',
                'tab' => array(
                    'url' => 'admin/simple_twitter_cards/config/form',
                )
            ),
        ),
    ),
);
