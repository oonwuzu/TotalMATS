<?php

/**
 * Class that represent a BML element.
 *
 * It allows various BML sub-elements to be added to it, as well as providing
 * convenience methods that allow higher-level BML constructs to be created -- lists,
 * buttons etc.
 *
 * Functionality does not cover all you can do with BML, so new methods should be
 * added to this when needed. Alternatively you should subclass this for more
 * application-specific methods.
 *
 * The bml generation take associative arrays as arguments. The keys are documented
 * in the following style:
 *
 *      key name | value type | default value | optional description
 *
 * If one of the keys references another associative array, then its keys are
 * documented in the same way, but indented another level.
 *
 * @author Will Sewell <will.sewell@binu-inc.com>
 */
class BmlElement extends SimpleXMLElement
{
    /**
     * @param array $args
     *      x | string | indent
     *      y | string | y
     *      w | int | not set, set to screen width - x if bg_style set
     *      h | int | not set, set to line height if bg_style set
     *      scroll_type | string (panning or fixed) | panning
     *      bg_style | string | not set
     *      translate | string (y or n) | y | y allows <t> tags to be used in text
     *
     * @return BmlElement
     */
    public function gen_psegment(array $args = array())
    {
        $bml_psegment = $this->addChild('pageSegment');
        $default_args = array(
            //'x' => DocConfig::$config['indent'],
            'x' => '0',
        	'y' => 'y',
            'translate' => 'y',
        );
        $args = array_merge($default_args, $args);
        if (isset($args['bg_style'])) {
            $default_args = array(
                'w' => DocConfig::$config['screen_w'] - $args['x'],
                'h' => DocConfig::$config['line_height'],
            );
            $args = array_merge($default_args, $args);
        }
        $attrs = array('x', 'y', 'w', 'h', 'translate');
        $this->add_attrs($bml_psegment, $args, $attrs);

        if (isset($args['scroll_type'])) {
            $scroll_type = $args['scroll_type'];
        } else {
            $scroll_type = 'panning';
        }
        $bml_scroll_element = $bml_psegment->addChild($scroll_type);

        if (isset($args['bg_style'])) {
            $rect_args = $this->extract_vals($args, array('w', 'h'));
            $rect_args['x'] = '0';
            $rect_args['y'] = '0';
            $rect_args['style'] = $args['bg_style'];
            $bml_scroll_element->gen_rectangle($rect_args);
        }

        return $bml_scroll_element;
    }

    /**
     * @param array $args
     *      url | string | REQUIRED
     *      w | int | line_height
     *      h | int | line_height
     *      x | string | 0
     *      y | string | y + indent
     *      mode | string (scale or crop or asis) | crop
     *
     * @return BmlElement
     */
    public function gen_img(array $args)
    {
        $this->validate_required($args, array('url'));
        $default_args = array(
            'x' => '0',
            'y' => 'y + ' . DocConfig::$config['indent'],
            'w' => DocConfig::$config['line_height'],
            'h' => DocConfig::$config['line_height'],
            'mode' => 'crop',
        );
        $args = array_merge($default_args, $args);
        $bml_image = $this->addChild('image');
        $attrs = array('url', 'x', 'y', 'w', 'h', 'mode');
        $this->add_attrs($bml_image, $args, $attrs);
        return $bml_image;
    }

    /**
     * @param array $args
     *      text | string | REQUIRED | when can insert <t>...</t> tags to translate
     *      style | string | REQUIRED
     *      mode | string (wrap, truncate or continue) | wrap
     *      align | string (left, center or right) | not set
     *      x | string | 1 (hack because align = center doesn't work if x is 0)
     *      y | string | y + indent
     *      w | int | not set
     *      h | int | not set
     *
     * @return BmlElement
     */
    public function gen_text(array $args)
    {
        $this->validate_required($args, array('text', 'style'));
        $default_args = array(
            'mode' => 'wrap',
            'x' => DocConfig::$config['indent'],
            'y' => 'y + ' . DocConfig::$config['indent'],
        );
        $args = array_merge($default_args, $args);

        preg_match_all('/(<t>[^<]*<\/t>)|([^<]*)/s', $args['text'], $matches);

        foreach ($matches[0] as $match_num => $match) {
            if ($match) {
                $attrs = array('style', 'mode', 'align', 'w', 'h', 'translate');
                if ($match_num === 0) {
                    $attrs[] = 'x';
                    $attrs[] = 'y';
                } else {
                    $args['mode'] = 'continue';
                }

                if (strpos($match, '<t>') !== false) {
                    $args['translate'] = 'y';
                    $match = str_replace('<t>', '', $match);
                    $match = str_replace('</t>', '', $match);
                } else {
                    $args['translate'] = 'n';
                }

                $bml_text = $this->addChild('text', $match);
                $this->add_attrs($bml_text, $args, $attrs);
            }
        }
        return $bml_text;
    }

    /**
     * @param array $args
     *      url | string | not set
     *      text | string | not set
     *      text_style | string | not set, REQUIRED if text is set
     *      x | string | not set
     *      y | string | y + indent
     *      w | int | not set
     *      h | int | not set
     *      icon | string (y or n) | n
     *      link_type | string (s, o, or t) | link_type_default
     *      action_type | string (see BML schema) | not set
     *      text_mode | string (wrap, truncate, or continue) | truncate if text set
     *      text_align | string (left, right, or center) | left if text set
     *      text_x_indent | int | 0, icon_w + indent if icon_url set
     *      text_y_indent | int | 0
     *      text_h | int | line_height
     *      icon_url | string | not set
     *      icon_w | int | line_height
     *      icon_h | int | line_height
     *
     * @return BmlElement
     */
    public function gen_link(array $args = array())
    {
        $bml_link = $this->addChild('link');
        $link_default_args = array(
            'y' => 'y + ' . DocConfig::$config['indent'],
            'icon' => 'n',
            'link_type' => DocConfig::$config['link_type_default'],
            'icon_w' => DocConfig::$config['line_height'],
            'icon_h' => DocConfig::$config['line_height'],
            'text_mode' => 'truncate',
            'text_align' => 'left',
            'text_y_indent' => '0',
            'text_h' => DocConfig::$config['line_height'],
        );
        $args = array_merge($link_default_args, $args);
        $attrs = array(
            'x', 'y', array('link_type', 'linkType'),
            array('action_type', 'actionType'), 'icon', 'url', 'w', 'h'
        );
        $this->add_attrs($bml_link, $args, $attrs);

        if (isset($args['icon_url'])) {
            $img_keys = array(
                array('icon_url', 'url'),
                array('icon_w', 'w'),
                array('icon_h', 'h'),
            );
            $img_args = $this->extract_vals($args, $img_keys);
            $img_args['x'] = '0';
            $img_args['y'] = '0';
            $bml_link->gen_img($img_args);
        }

        if (isset($args['text'])) {
            $this->validate_required($args, array('text_style'));
            $text_keys = array(
                'text',
                'w',
                array('text_mode', 'mode'),
                array('text_align', 'align'),
                array('text_y_indent', 'y'),
                array('text_h', 'h'),
                array('text_style', 'style'),
            );
            $text_args = $this->extract_vals($args, $text_keys);
            if (isset($args['text_x_indent'])) {
                $text_args['x'] = $args['text_x_indent'];
            } else {
                if (isset($args['icon_url'])) {
                    $text_args['x']
                        = $args['icon_w'] + DocConfig::$config['indent'];
                } else {
                    $text_args['x'] = '0';
                }
            }
            $bml_link->gen_text($text_args);
        }
        return $bml_link;
    }

    /**
     * Generate a specialised link that is just an image.
     *
     * @param array $args
     *      icon_url | string | REQUIRED
     *      url | string | not set
     *      x | string | 0
     *      y | string | y + indent
     *      w | int | line_height
     *      h | int | line_height
     *      link_type | string (s, o, or t) | link_type_default
     *      action_type | string (see BML schema) | not set
     *
     * @return BmlElement
     */
    public function gen_icon_link(array $args) {
        $this->validate_required($args, array('icon_url'));
        $args['icon_w'] = $args['w'];
        $args['icon_h'] = $args['h'];
        return $this->gen_link($args);
    }

    /**
     * Generate a specialised link that is styled like a button.
     *
     * @param array $args
     *      url | string | not set
     *      bg_style | string | REQUIRED
     *      x | string | 0
     *      y | string | y + indent
     *      w | int | screen_width / 2
     *      h | int | line_height
     *      text | string | not set
     *      text_style | string | not set, REQUIRED if text set
     *      text_h | int | line_height
     *      text_mode | string (wrap, truncate, or continue) | truncate if text set
     *      text_align | string (left, right, or center) | center if text set
     *      text_x_indent | int | 1 (because align = center won't work otherwise)
     *      link_type | string (s, o, or t) | link_type_default
     *      action_type | string (see BML schema) | not set
     *
     * @return BmlElement
     */
    public function gen_button(array $args) {
        $this->validate_required($args, array('bg_style'));
        $default_args = array(
            'x' => '0',
            'y' => 'y + ' . DocConfig::$config['indent'],
            'w' => round(DocConfig::$config['screen_w'] / 2),
            'h' => DocConfig::$config['line_height'],
            'text_align' => 'center',
        );
        $args = array_merge($default_args, $args);
        $this->gen_mark(array('name' => 'top_button_mark', 'y' => $args['y']));
        $rect_keys = array('x', 'w', 'h', array('bg_style', 'style'));
        $rect_args = $this->extract_vals($args, $rect_keys);
        $rect_args['y'] = 'top_button_mark';
        $this->gen_rectangle($rect_args);

        $link_keys = array(
            'url', 'text', 'text_style', 'x', 'w', 'h', 'link_type',
            'action_type', 'text_align', 'text_x_indent', 'text_y_indent',
            'text_mode', 'text_h'
        );
        $link_args = $this->extract_vals($args, $link_keys);
        $link_args['y'] = 'top_button_mark';

        return $this->gen_link($link_args);
    }

    /**
     * @param array $args
     *      text | string | not set
     *      text_style | string | not set, REQUIRED of text set
     *      bg_style | string | REQUIRED
     *      x | string | 0
     *      y | string | y + indent
     *      w | int | screen_width / 2
     *      h | int | line_height + 2
     *      url | string | not set
     *      border_style | string | not set
     *      link_type | string (s, o, or t) | link_type_default
     *      action_type | string (see BML schema) | not set
     *      text_x_indent | int | indent
     *
     * @return BmlElement
     */
    public function gen_text_box(array $args) {
        $this->validate_required($args, array('bg_style'));
        $default_vals = array(
            'x' => DocConfig::$config['indent'],
            'y' => 'y + ' . DocConfig::$config['indent'],
            'w' => round(DocConfig::$config['screen_w'] / 2),
            'h' => DocConfig::$config['line_height'] + 2,
            'text_x_indent' => DocConfig::$config['indent'],
        );
        $args = array_merge($default_vals, $args);
        $this->gen_mark(array('name' => 'top_tb_mark', 'y' => $args['y']));

        if (isset($args['border_style'])) {
            $rect_keys = array('x', 'w', 'h', array('border_style', 'style'));
            $rect_args = $this->extract_vals($args, $rect_keys);
            $rect_args['y'] = 'top_tb_mark';
            $this->gen_rectangle($rect_args);

            $args['x'] += 1;
            $args['y'] = 'top_tb_mark + 1';
            $args['w'] -= 2;
            $args['h'] -= 2;
        } else {
            $args['y'] = 'top_tb_mark';
        }

        $rect_keys = array('x', 'y', 'w', 'h', array('bg_style', 'style'));
        $rect_args = $this->extract_vals($args, $rect_keys);
        $this->gen_rectangle($rect_args);

        $link_keys = array(
            'url', 'text', 'text_style', 'x', 'y', 'w', 'h', 'text_align',
            'text_x_indent', 'link_type', 'action_type', 'text_h'
        );
        $link_args = $this->extract_vals($args, $link_keys);
        return $this->gen_link($link_args);
    }

    /**
     * @param array $args
     *      items | array | REQUIRED
     *          text | string | REQUIRED
     *          url | string | not set
     *          link_type | string | link_type_default
     *          action_type | string | page
     *          bg_style | string | not set | overrides bg_style on per item basis
     *      line_height | int | line_height
     *      text_style | string | REQUIRED
     *      bg_style | string | not set
     *      bg_style2 | string | not set | if set, every other item will have this bg
     *      dividing_line_style | string | not set | creates 1 px lines between items
     *      x | string | indent
     *      y | string | y + indent
     *      w | int | screen_width / (indent * 2)
     *      h | int | not set
     *      text_x_indent | int | indent
     *      text _y_indent | int | 0
     *
     * @return BmlElement
     */
    public function gen_list(array $args) {
        $this->validate_required($args, array('items', 'text_style'));
        $default_args = array(
            'line_height' => DocConfig::$config['line_height'],
            'x' => DocConfig::$config['indent'],
            'y' => 'y + ' . DocConfig::$config['indent'],
            'w' => DocConfig::$config['screen_w']
                + DocConfig::$cookies['binusys_display']['scbarW'],
            'text_x_indent' => DocConfig::$config['indent'],
            'text_y_indent' => '0',
        );
        $args = array_merge($default_args, $args);

        $psegment_keys = array('x', 'y', 'w', 'h');
        $psegment_args = $this->extract_vals($args, $psegment_keys);
        $psegment = $this->gen_psegment($psegment_args);

        $list_item_y = 0;
        foreach ($args['items'] as $idx => $item) {
            $this->validate_required($item, array('text'));
            // draw background if specified
            if (isset($args['bg_style1']) || isset($item['bg_style'])) {
                if (isset($item['bg_style'])) {
                    $bml_bg_style = $item['bg_style'];
                } elseif (isset($args['bg_style2'])) {
                    if ($idx % 2 === 0) {
                        $bml_bg_style = $args['bg_style1'];
                    } else {
                        $bml_bg_style = $args['bg_style2'];
                    }
                } else {
                    $bml_bg_style = $args['bg_style1'];
                }
                $rect_args = array(
                    'y' => $list_item_y
,                    'h' => $args['line_height'] -1,
                    'style' => $bml_bg_style,
                );
                if (isset($args['w'])) {
                    $rect_args['w'] = $args['w'];
                }
                $psegment->gen_rectangle($rect_args);
            }
            // generate bml for the link and text, optionally icon too
            if (isset($item['url']) || isset($item['actionType'])) {
                if (isset($item['actiontype'])) {
                    $link_type = $item['actiontype'];
                } else {
                    $link_type = DocConfig::$config['link_type_default'];
                }
                if (isset($item['actiontype'])) {
                    $action_type = $item['action_type'];
                } else {
                    $action_type = 'page';
                }
                $link_keys = array(
                    'text_style', 'w', array('line_height', 'h'), 'text_x_indent',
                    'text_y_indent'
                );
                $link_args = $this->extract_vals($args, $link_keys);
                if (isset($item['url'])) {
                    $link_args['url'] = $item['url'];
                }
                $link_args['link_type'] = $link_type;
                $link_args['action_type'] = $action_type;
                $link_args['x'] = '0';
                $link_args['y'] = $list_item_y;
                $link_args['text'] = $item['text'];
                $psegment->gen_link($link_args);
            } else {
                // it's only text
                $text_keys = array(
                    'line_height',
                    'w',
                    array('text_style', 'style'),
                    array('text_x_indent', 'x'),
                );
                $text_args = $this->extract_vals($args, $text_keys);
                $text_args['text'] = $item['text'];
                $text_args['mode'] = 'truncate';
                $text_args['y'] = $list_item_y;
                $psegment->gen_text($text_args);
            }
            $psegment->gen_list_dividing_line($args, $list_item_y);
            $list_item_y += $args['line_height'];
        }
        $psegment->gen_list_dividing_line($args, $list_item_y);
        $resultset = array('segment'=>$psegment,
            'yaxis'=>$list_item_y,);
      //  return $psegment;
        return $resultset;
    }

    /**
     * Helper for gen_list() to avoid code duplication for an optional addition of
     * a dividing line between list items based on whether dividing_line_style is set
     * in $args.
     *
     * @param $args
     * @param $y
     */
    private function gen_list_dividing_line($args, $y)
    {
        if (isset($args['dividing_line_style'])) {
            $rect_keys = array('w', array('dividing_line_style', 'style'));
            $rect_args = $this->extract_vals($args, $rect_keys);
            $rect_args['x'] = '0';
            $rect_args['y'] = $y;
            $rect_args['h'] = '1';
            $this->gen_rectangle($rect_args);
        }
    }

    /**
     * @param array $args
     *      text | string | REQUIRED
     *      text_style | string | REQUIRED
     *      h | int | title line height
     *      bg_style | string | not set
     *      text_y_indent | string | 0
     *
     * @return BmlElement
     */
    public function gen_text_banner(array $args)
    {
        $this->validate_required($args, array('text', 'text_style'));
        $default_args = array('h' => DocConfig::$config['title_line_height']);
        $args = array_merge($default_args, $args);

        $psegment_args = array(
            'x' => '0',
            'y' => '0',
            'w' => DocConfig::$config['screen_w'],
            'h' => $args['h'],
            'scroll_type' => 'fixed',
        );
        if (isset($args['bg_style'])) {
            $psegment_args['bg_style'] = $args['bg_style'];
        }
        $psegment = $this->gen_psegment($psegment_args);

        $text_args = array(
            'text' => $args['text'],
            'style' => $args['text_style'],
            'mode' => 'truncate',
            'align' => 'center',
            'y' => '0',
        );
        if (isset($args['text_y_indent'])) {
            $text_args['y'] = $args['text_y_indent'];
        }
        $psegment->gen_text($text_args);

        return $psegment;
    }

    /**
     * @param array $args
     *      name | string | REQUIRED
     *      x | string | not set
     *      y | string | y
     *
     * @return BmlElement
     */
    public function gen_mark(array $args)
    {
        $this->validate_required($args, array('name'));
        $args = array_merge(array('y' => 'y'), $args);
        $bml_mark = $this->addChild('mark');
        $this->add_attrs($bml_mark, $args, array('name', 'x', 'y'));
        return $bml_mark;
    }

    /**
     * @param array $args
     *      x | string | 0
     *      y | string | y
     *      w | int | screen_width
     *      h | int | line_height
     *      style | string | REQUIRED
     *
     *      REQUIRED: x, y, w, h, style
     *
     * @return BmlElement
     */
    public function gen_rectangle(array $args)
    {
        $this->validate_required($args, array('style'));
        $defaul_args = array(
            'x' => '0',
            'y' => 'y',
            'w' => DocConfig::$config['screen_w'],
            'h' => DocConfig::$config['line_height'],
        );
        $args = array_merge($defaul_args, $args);
        $bml_rect = $this->addChild('rectangle');
        $this->add_attrs($bml_rect, $args, array('x', 'y', 'w', 'h', 'style'));
        return $bml_rect;
    }

    /**
     * @param array $args
     *      title | string | not set
     *      fields | array | REQUIRED
     *          value | string | empty string
     *          name | string | not set
     *          mode | string (see schema) | not set
     *          mandatory | string (true or false) | not set
     *          predictive_text | string (no or allow) | not set
     *          hide_value | string (true or false) | not set
     *          max_length | int | 50
     *
     * @return BmlElement
     */
    public function gen_text_entry(array $args)
    {
        $this->validate_required($args, array('fields'));
        $psegment = $this->addChild('pageSegment');

        $bml_te = $psegment->addChild('textEntry');
        $this->add_attr_ifset($bml_te, $args, 'title');

        if (!is_array($args['fields'])) {
            $args['fields'] = array($args['fields']);
        }

        foreach ($args['fields'] as $field) {
            $bml_tef = $bml_te->addChild('textEntryField');
            $attr_keys = array(
                'mode',
                'name',
                'mandatory',
                array('predictive_text', 'predictiveText'),
                array('hide_value', 'hideValue'),
            );
            $this->add_attrs($bml_tef, $field, $attr_keys);
            if (isset($field['value'])) {
                $bml_tef->addAttribute('value', $field['value']);
            } else {
                $bml_tef->addAttribute('value', '');
            }
            if (isset($field['max_length'])) {
                $bml_tef->addAttribute('maxLength', $field['max_length']);
            } else {
                $bml_tef->addAttribute('maxLength', '50');
            }
            if (count($args['fields']) === 1) {
                $bml_tef->addAttribute('fullscreen', 'true');
            } else {
                $bml_tef->addAttribute('fullscreen', 'false');
            }
        }

        return $psegment;
    }

    /**
     * This does one thing over the vanilla addChild(); it automatically encodes
     * the value - it's extremely confusing that it does not do this by default when
     * addAttribute() does.
     *
     * @param string $name
     * @param string $value
     * @return SimpleXMLElement
     */
    public function addChild($name, $value = '')
    {
        $enc_val = htmlspecialchars($value, ENT_QUOTES, 'UTF-8', false);
        return parent::addChild($name, $enc_val);
    }

    /**
     * Add an element from $attrs, specified by key $key, as an attribute to
     * $bml_elem so long as the key exists.
     *
     * @param BmlElement $bml_elem
     * @param array $attrs
     * @param string $key
     * @param string $attr The attribute name if it is not the same as the key.
     */
    protected function add_attr_ifset(
        BmlElement $bml_elem, array $attrs, $key, $attr = null
    ) {
        if (is_null($attr)) {
            $attr = $key;
        }
        if (isset($attrs[$key])) {
            $bml_elem->addAttribute($attr, $attrs[$key]);
        }
    }

    /**
     * Used to add multiple attributes to $bml_elem at once.
     *
     * @param BmlElement $bml_elem
     * @param array $attrs
     * @param array $attr_keys An array of keys - if the key is different to the name
     *      of the attribute for a particular element, then the element should be an
     *      array where the first element is the $attrs key and the second is the
     *      name of the attribute.
     */
    protected function add_attrs(
        BmlElement $bml_elem, array $attrs, array $attr_keys
    ) {
        foreach ($attr_keys as $attr_key) {
            if (is_array($attr_key)) {
                $this->add_attr_ifset($bml_elem, $attrs, $attr_key[0], $attr_key[1]);
            } else {
                $this->add_attr_ifset($bml_elem, $attrs, $attr_key);
            }
        }
    }

    /**
     * Since the methods of this class accept associative arrays rather than
     * traditional arguments, we manually call this function to make sure that the
     * required keys exist.
     *
     * @param array $args
     * @param array $required
     * @throws InvalidArgumentException
     */
    protected function validate_required(array $args, array $required)
    {
        $in_both = array_intersect($required, array_keys($args));
        if (count($in_both) < count($required)) {
            throw new InvalidArgumentException(
                'Method was called with not all required array keys set!' . PHP_EOL
                . 'Expected keys:' . PHP_EOL . print_r($required, true)
                . 'Received keys:' . PHP_EOL . print_r(array_keys($args), true)
            );
        }
    }

    /**
     * Many method use a subset of its arguments as arguments to generate a
     * sub-element. This function provides a convenient way of extracting a subset of
     * elements from a larger array.
     *
     * @param array $args
     * @param array $to_extract Keys to extract. Items can be an array of the key to
     *      extract as the first element and the new key name as the second.
     * @return array
     */
    protected function extract_vals(array $args, array $to_extract)
    {
        $extracted = array();
        foreach ($to_extract as $to_extract_item) {
            if (is_array($to_extract_item)) {
                list($key_name, $attr_name) = $to_extract_item;
                if (isset($args[$key_name])) {
                    $extracted[$attr_name] = $args[$key_name];
                }
            } else {
                if (isset($args[$to_extract_item])) {
                    $extracted[$to_extract_item] = $args[$to_extract_item];
                }
            }
        }
        return $extracted;
    }
}
