<?php
$config = array(
    'form_name' => '<img style="vertical-align:middle;" src="static/apps/simple_twitter_cards/images/icon-32.png">&nbsp'.__('Simple Twitter cards'),
    'tab' => array(
        'label' => 'Simple Twitter cards',
    ),
    'layout' => array(
        'lines' => array(
            1 => array(
                'cols' => array(
                    1 => array(
                        'col_number' => 6,
                        'view' => 'nos::form/expander',
                        'params' => array(
                            'title'   => __('Général'),
                            'options' => array(
                                'allowExpand' => true,
                            ),
                            'content' => array(
                                'view' => 'nos::form/fields',
                                'params' => array(
                                    'fields' => array(
                                        'card_type',
                                        'creator_username',
                                        'site_username',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    2 => array(
                        'col_number' => 6,
                        'view' => 'nos::form/expander',
                        'params' => array(
                            'title'   => __('Twitter card validator'),
                            'options' => array(
                                'allowExpand' => true,
                                'expanded' => false
                            ),
                            'content' => array(
                                'view' => 'nos::form/fields',
                                'params' => array(
                                    'fields' => array(
                                        'validator',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            2 => array(
                'cols' => array(
                    2 => array(
                        'col_number' => 8,
                        'view' => 'nos::form/expander',
                        'params' => array(
                            'title'   => __('Images'),
                            'options' => array(
                                'allowExpand' => true,
                            ),
                            'content' => array(
                                'view' => 'nos::form/fields',
                                'params' => array(
                                    'fields' => array(
                                        'img_size',
                                        'img_resize',
                                        'default_img',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'fields' => array(
        'card_type' => array(
            'label' => __('Choisissez votre type de card :'),
            'form' => array(
                'type' => 'select',
                'options' => array(
                    'summary' => __('Résumé'),
                    'summary_large_image' => __('Résumé à grande image'),
                    'photo' => __('Photo'),
                ),
            ),
        ),
        'creator_username' => array(
            'label' => __('Entrez votre compte Twitter personnel :'),
            'form' => array(
                'type' => 'text',
            ),
            'template' => '<tr><td>{label}</td><td>{field}<span style="font-size:11px;color:#ccc;">'.__('(optionnel)').'</span></td></tr>',
        ),
        'site_username' => array(
            'label' => __('Entrez le compte Twitter associé au site web :'),
            'form' => array(
                'type' => 'text',
            ),
            'template' => '<tr><td>{label}</td><td>{field}<span style="font-size:11px;color:#ccc;">'.__('(optionnel)').'</span></td></tr>',
        ),
        'img_size' => array(
            'label' => __('Choisissez la taille de l\'image :'),
            'form' => array(
                'type' => 'select',
                'options' => array(
                    'mobile-non-retina' => __('Mobile à affichage non retina (largeur: 280px - longueur: 375px)'),
                    'mobile-retina' => __('Mobile à affichage retina (largeur: 560px - longueur: 750px)'),
                    'web' => __('Taille max pour le web (largeur: 435px - longueur: 375px)'),
                    'small' => __('Petit (largeur: 280px - longueur: 150px)'),
                )
            ),
        ),
        'img_resize' => array(
            'label' => __('Forcer le recadrage de l\'image de la card :'),
            'form' => array(
                'type' => 'checkbox',
                'value' => '1',
                'empty' => '0',
            ),
        ),
        'default_img' => array(
            'label' => __('Image par défaut des cards :'),
            'form' => array(
            ),
            'renderer' => '\Nos\Media\Renderer_Media',
        ),
        'validator' => array(
            'label' => __('How to validate your cards:'),
            'form' => array(
                'value' => \View::forge('simple_twitter_cards::admin/validator'),
            ),
            'renderer' => '\Nos\Renderer_Text',
        ),
    ),
);

return $config;