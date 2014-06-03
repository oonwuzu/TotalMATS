<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(dirname(__FILE__)));

require_once 'DocConfig.php';
require_once 'BmlElement.php';

/**
 * Class for generating a skeleton BML document on construction.
 *
 * Once the class is instantiated you can continue to add menu items and styles. At
 * any point you can "get" the BML (a tree of BMLElements) and start manipulating
 * that.
 *
 * In the future it will be useful to create sub-classes of this for generating
 * more specialised BML pages -- e.g. a general list or text page.
 *
 * @author Will Sewell <will.sewell@binu-inc.com>
 */
class BmlDocument
{
    protected $styles = array();
    protected $menu_items = array();

    protected $doc_conf;

    protected $bml;

    /**
     * Create the skeleton XML structure to start building on.
     *
     * @param int $ttl Time the proxy should hold onto this page for.
     * @param int $developer_id ID of the developer.
     * @param int $app_id DevCentral ID of the app.
     * @param array $cookies Cookies (including proxy cookies) from $_COOKIE array.
     * @param string $cookie_domain Domain name to store cookies.
     * @param string $link_type_default Default linkType used by BmlElements
     * @param string $footer_bg_color A hex number.
     * @param string $footer_text_color A hex number.
     * @param string $bg_color A hex number -- the background of the whole page.
     * @param BmlElement $bml_root An optional subclass to use as the root of the
     *      document, so that all elements will be of this type.
     */
    public function __construct(
        $ttl, $developer_id, $app_id, array $cookies, $cookie_domain,
        $link_type_default, $footer_bg_color = null, $footer_text_color = null,
        $bg_color = null, $bml_root = null
    ) {
        $this->doc_conf = new DocConfig($cookies, $cookie_domain, $link_type_default);

        if (is_null($bml_root)) {
            $bml_root = new BmlElement('<binu />');
        }

        $this->init_page(
            $ttl, $developer_id, $app_id, $bg_color, $footer_bg_color,
            $footer_text_color, $bml_root
        );
    }

    /**
     * Helper function to initialise a skeleton BML document.
     *
     * @param int $ttl
     * @param int $developer_id
     * @param int $app_id
     * @param string $bg_color
     * @param string $footer_bg_color
     * @param string $footer_text_color
     * @param BmlElement $root_element The root BmlElement object to use.
     */
    protected function init_page(
        $ttl, $developer_id, $app_id, $bg_color, $footer_bg_color,
        $footer_text_color, BmlElement $root_element
    ) {
        $this->bml = $root_element;
        $this->bml->addAttribute('ttl', $ttl);
        $this->bml->addAttribute('developer', $developer_id);
        $this->bml->addAttribute('app', $app_id);

        // add the three requires sub components of a BML page
        $this->bml->addChild('styles');
        $bml_page = $this->bml->addChild('page');
        $bml_control = $this->bml->addChild('control');
        $bml_control->addAttribute('textUTF8', 'true');

        // set background color (if any)
        if (!is_null($bg_color)) {
            $this->set_style('bg_color', $bg_color);
            $bml_page->addAttribute('backgroundStyle', 'bg_color');
        }

        // set up basic controls
        if (!is_null($footer_bg_color)) {
            $this->set_style('footer_bg_color', $footer_bg_color);
        }
        if (!is_null($footer_text_color)) {
            $this->set_style('footer_text_color', $footer_text_color);
        }

        $bml_footer = $bml_control->addChild('footer');
        $bml_footer->addAttribute('barStyle', 'footer_bg_color');
        $bml_footer->addAttribute('labelStyle', 'footer_text_color');

        $bml_menu = $bml_footer->addChild('menu');
        $bml_menu->addAttribute('key', 'action');
        $bml_menu->addAttribute('text', 'Menu');

        $bml_back_action = $bml_footer->addChild('action');
        $bml_back_action->addAttribute('key', 'navigate');
        $bml_back_action->addAttribute('linkType', 'o');
        $bml_back_action->addAttribute('actionType', 'back');
        $bml_back_action->addAttribute('text', 'Back');
    }

    /**
     * Set a style that can be referenced later on in the BML generation.
     *
     * @param string $name      The styles unique name.
     * @param string $color     In hexadecimal notation.
     * @param string $font_face A proxy supported font.
     * @param int    $font_size Standard font face size.
     *
     * @return void
     */
    public function set_style($name, $color = '', $font_face = '', $font_size = 0)
    {
        $bml_style = $this->bml->styles->addChild('style');
        $bml_style->addAttribute('name', $name);

        if ($color) {
            $bml_color = $bml_style->addChild('color');
            $bml_color->addAttribute('value', $color);
        }
        if ($font_face || $font_size) {
            $bml_font = $bml_style->addChild('font');
            if ($font_face) {
                $bml_font->addAttribute('face', $font_face);
            }
            if ($font_size) {
                $bml_font->addAttribute('size', $font_size);
            }
        }

        // add it to the list of allowed styles for later validation
        $this->styles[] = $name;
    }

    /**
     * Add an item to the menu.
     *
     * @param string $text
     * @param string $url
     * @param string $linktype
     */
    public function set_menu_item($text, $url, $linktype = 'o')
    {
        $bml_menu = $this->bml->control->footer->menu;
        if (isset($bml_menu->action)) {
            $menu_size = count($bml_menu->action);
            // clone existing items and remove them from the XML tree
            // this is so they can be added AFTER the new item to be added
            $old_menu = new SimpleXMLElement($bml_menu->asXML());
            unset($bml_menu->action);
        } else {
            $menu_size = 0;
        }
        $bml_new_action = $bml_menu->addChild('action', $url);
        $bml_new_action->addAttribute('key', $menu_size);
        $bml_new_action->addAttribute('text', $text);
        $bml_new_action->addAttribute('linkType', $linktype);
        if (isset($old_menu)) {
            foreach ($old_menu->action as $existing_action) {
                $bml_old_action = $bml_menu->addChild(
                    'action', (string) $existing_action
                );
                $bml_old_action->addAttribute('key', $existing_action['key']);
                $bml_old_action->addAttribute('text', $existing_action['text']);
                $bml_old_action->addAttribute(
                    'linkType', $existing_action['linkType']
                );
            }
        }
    }

    /**
     * Add a miscellaneous action.
     * @param int $key The key to trigger the action.
     * @param string $url The URL to go to.
     * @param string $linktype
     */
    public function set_action($key, $url, $linktype = 'o')
    {
        if (!isset($this->bml->control->actions)) {
            $bml_actions = $this->bml->control->addChild('actions');
        } else {
            $bml_actions = $this->bml->control->actions;
        }
        $bml_action = $bml_actions->addChild('action', $url);
        $bml_action->addAttribute('key', $key);
        $bml_action->addAttribute('linkType', $linktype);
    }

    /**
     * Return the current state of the BML tree.
     *
     * @return BmlElement
     */
    public function get_bml()
    {
        return $this->bml;
    }

    /**
     * Returns a config/cookie item.
     *
     * @param string $config_item The config item key.
     * @return string The config value.
     * @throws InvalidArgumentException If the key does not exist.
     */
    public function __get($config_item)
    {
        return $this->doc_conf->$config_item;
    }
}
