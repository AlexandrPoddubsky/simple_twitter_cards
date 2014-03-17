<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Twitter\Card;

use \Nos\Orm_Behaviour;

class Orm_Behaviour_TwitterCard extends Orm_Behaviour
{
    /**
     * 'fields' => array(
     *      'title' => ''
     *      'description' => ''
     *      'image' => ''
     * ),
     * 'type' => ''
     */
    protected $_properties = array();

    private static $_image_size = array(
        'mobile-non-retina' => array(280,375),
        'mobile-retina' => array(560,750),
        'web' => array(435,375),
        'small' => array(280,150),
    );

    public function __construct($class)
    {
        parent::__construct($class);
    }

    /**
     * Add the twitter card tags
     */
    public function setTwitterCardTags(\Nos\Orm\Model $item, $html)
    {
        $config = Controller_Admin_Config::getOptions();
        $config = \Arr::get($config, \Nos\Nos::main_controller()->getContext());

        /* if there is no configuration for the context, return the html with no modification */
        if (empty($config)) return $html;

        if (\Arr::get($this->_properties, 'type', false)) {
            $type = \Arr::get($this->_properties, 'type');
        } else {
            $type = \Arr::get($config, 'card_type', 'summary');
        }
        $site_username = \Arr::get($config, 'site_username');
        if (\Str::starts_with($site_username, '@')) $site_username = \Str::sub($site_username,1);
        $creator_username = \Arr::get($config, 'creator_username');
        if (\Str::starts_with($creator_username, '@')) $creator_username = \Str::sub($creator_username,1);

        $tags_values = $this->_getTagsValues($item);

        $title = \Arr::get($tags_values, 'title');

        $summary = \Arr::get($tags_values, 'summary');
        if (\Str::is_html($summary)) $summary = strip_tags($summary);
        $summary = \Str::truncate($summary, 197);
        $tab = array(CHR(13) => " ", CHR(10) => " " );
        $summary = strtr($summary,$tab);

        $img_url = '';
        $image = \Arr::get($tags_values, 'image', null);
        if (empty($image) && \Arr::get($config, 'default_img')) {
            $image = \Nos\Media\Model_Media::find(\Arr::get($config, 'default_img'));
        }

        if (!empty($image) && $image->isImage()) {
            $image_size = \Arr::get($config, 'img_size', 'web');
            if (\Arr::get($config, 'img_resize', 0)) {
                $img_url = $image->getToolkitImage()->crop_resize(self::$_image_size[$image_size][0], self::$_image_size[$image_size][1])->url();
            } else {
                $img_url = $image->getToolkitImage()->shrink(self::$_image_size[$image_size][0], self::$_image_size[$image_size][1])->url();
            }
        }

        $meta_tags = '';
        $meta_tags .= '<meta name="twitter:card" content="'.$type.'">'."\n";
        $meta_tags .= '<meta name="twitter:site" content="@'.$site_username.'">'."\n";
        $meta_tags .= '<meta name="twitter:title" content="'.$title.'">'."\n";
        $meta_tags .= '<meta name="twitter:description" content="'.$summary.'">'."\n";
        $meta_tags .= '<meta name="twitter:creator" content="@'.$creator_username.'">'."\n";
        if (!empty($img_url)) {
            $meta_tags .= '<meta name="twitter:image:src" content="'.$img_url.'">'."\n";
        }

        preg_match("/<\/head>/", $html, $matches);
        if (!empty($matches) && isset($matches[0])) {
            $html = str_replace($matches[0], $meta_tags.$matches[0], $html);
        }

        return $html;
    }

    /**
     * @param \Orm\Model $item
     * @return array
     */
    private function _getTagsValues($item) {
        $fields = \Arr::get($this->_properties, 'fields');
        if (empty($fields)) return array();

        $values = array(
            'title' => '',
            'summary' => '',
            'image' => '',
        );

        foreach ($fields as $field_label => $field_possibilities) {
            if (is_array($field_possibilities)) {
                \Arr::set($values, $field_label, $this->_getTagValueFromArray($item, $field_label, $field_possibilities));
            } else {
                \Arr::set($values, $field_label, $this->_getTagValue($item, $field_label, $field_possibilities));
            }
        }

        return $values;
    }

    /**
     * @param \Orm\Model $item
     * @param string $field_label
     * @param array $field_name_possibilities
     * @return mixed
     */
    private function _getTagValue($item, $field_label, $field_name) {
        $field_name_part = explode('->', $field_name);
        $value = false;
        //If we are in a image case, the default assecor is medias
        if ($field_label == 'image' && count($field_name_part) == 1) {
            $field_name_part = array(
                'medias',
                reset($field_name_part),
            );
        }
        foreach ($field_name_part as $field_acessor){
            if ($value === false) {
                $value = $item->{$field_acessor};
            } else if (is_object($value)) {
                $value = $value->{$field_acessor};
            }
        }
        return $value;
    }

    /**
     * @param \Orm\Model $item
     * @param string $field_label
     * @param array $field_name_possibilities
     * @return mixed
     */
    private function _getTagValueFromArray($item, $field_label, $field_name_possibilities) {
        $value = false;
        foreach ($field_name_possibilities as $field_name) {
            $value = $this->_getTagValue($item, $field_label, $field_name);
            if (!empty($value)) break;
        }
        return $value;
    }
}
