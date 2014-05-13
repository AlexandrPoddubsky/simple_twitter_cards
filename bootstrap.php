<?php

//Add the twitter card behavior on pages
\Event::register_function('config|noviusos_page::model/page', function(&$config)
{
    if (!\Arr::get($config, 'behaviours')) $config['behaviours'] = array();
    $config['behaviours']['Twitter\Card\Orm_Behaviour_TwitterCard'] = array(
        'fields' => array(
            'title' => 'page_title',
            'summary' => array(
                'page_meta_description',
                'wysiwygs->content',
            ),
        ),
        'type' => 'summary', //Summary type is forced beacause there is no image on pages
    );
});

//Add the twitter card behavior on blog post
if (\Module::exists('noviusos_blog')) {
    \Event::register_function('config|noviusos_blog::model/post', function(&$config)
    {
        if (!\Arr::get($config, 'behaviours')) $config['behaviours'] = array();
        $config['behaviours']['Twitter\Card\Orm_Behaviour_TwitterCard'] = array(
            'fields' => array(
                'title' => 'post_title',
                'summary' => 'post_summary',
                'image' => 'thumbnail',
            ),
        );
    });
}

//Add the twitter card behavior on news post
if (\Module::exists('noviusos_news')) {
    \Event::register_function('config|noviusos_news::model/post', function(&$config)
    {
        if (!\Arr::get($config, 'behaviours')) $config['behaviours'] = array();
        $config['behaviours']['Twitter\Card\Orm_Behaviour_TwitterCard'] = array(
            'fields' => array(
                'title' => 'post_title',
                'summary' => 'post_summary',
                'image' => 'thumbnail',
            ),
        );
    });
}
Event::register_function('front.display', function(&$html)
{
    $methodVariable = array(\Nos\Nos::main_controller(), 'getItemDisplayed');
    if (is_callable($methodVariable)) {
        $item = \Nos\Nos::main_controller()->getItemDisplayed();
        $behaviour = $item->behaviours('Twitter\Card\Orm_Behaviour_TwitterCard');
        if (!empty($behaviour)) {
            $html = $item->setTwitterCardTags($html);
        }
    }
});
