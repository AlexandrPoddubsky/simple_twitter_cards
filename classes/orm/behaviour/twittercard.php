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
        if (\Arr::get($this->_properties, 'type', false)) {
            $type = \Arr::get($this->_properties, 'type');
        } else {
            $type = \Arr::get($config, 'card_type', 'summary');
        }
        $site_username = \Arr::get($config, 'site_username');
        if (\Str::starts_with($site_username, '@')) $site_username = \Str::sub($site_username,1);
        $creator_username = \Arr::get($config, 'creator_username');
        if (\Str::starts_with($creator_username, '@')) $creator_username = \Str::sub($creator_username,1);

        //Parsing field value if there is field name with acessor in it.
        if(!is_array(\Arr::get($this->_properties, 'fields'))) {
            \Arr::set($this->_properties, 'fields', array());
        }
        foreach (\Arr::get($this->_properties, 'fields') as $field_prop => $field_name) {
            $field_name = explode('->', $field_name);
            if (count($field_name) == 1) $field_name = reset($field_name);
            \Arr::set($this->_properties, 'fields.'.$field_prop, $field_name);
        }

        if (is_array(\Arr::get($this->_properties, 'fields.title'))) {
            $value = false;
            foreach (\Arr::get($this->_properties, 'fields.title') as $field_acessor) {
                if ($value === false) {
                    $value = $item->{$field_acessor};
                } else if (is_object($value)) {
                    $value = $value->{$field_acessor};
                }
            }
            $title = !empty($value) ? $value : '';
        } else {
            $title = isset($item->{\Arr::get($this->_properties, 'fields.title')}) ? $item->get(\Arr::get($this->_properties, 'fields.title')) : '';
        }

        if (is_array(\Arr::get($this->_properties, 'fields.summary'))) {
            $value = false;
            foreach (\Arr::get($this->_properties, 'fields.summary') as $field_acessor) {
                if ($value === false) {
                    $value = $item->{$field_acessor};
                } else if (is_object($value)) {
                    $value = $value->{$field_acessor};
                }
            }
            $summary = !empty($value) ? $value : '';
        } else {
            $summary = isset($item->{\Arr::get($this->_properties, 'fields.summary')}) ? $item->get(\Arr::get($this->_properties, 'fields.summary')) : '';
        }
        if (\Str::is_html($summary)) $summary = strip_tags($summary);
        $summary = \Str::truncate($summary, 197);
        $tab = array( CHR(13) => " ", CHR(10) => " " );
        $summary = strtr($summary,$tab);

        $img_url = '';
        $image = null;
        if (is_array(\Arr::get($this->_properties, 'fields.image'))) {
            $value = false;
            foreach (\Arr::get($this->_properties, 'fields.image') as $field_acessor) {
                if ($value === false) {
                    $value = $item->{$field_acessor};
                } else if (is_object($value)) {
                    $value = $value->{$field_acessor};
                }
            }
            $image = !empty($value) ? $value : null;
        } else {
            if (isset($item->medias->{\Arr::get($this->_properties, 'fields.image')})) {
                $image = $item->medias->{\Arr::get($this->_properties, 'fields.image')};
            }
        }

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
}
