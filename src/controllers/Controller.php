<?php

abstract class Controller {

    protected $app_conns;

    protected function render_bml(BmlElement $bml) {
        $dom = dom_import_simplexml($bml)->ownerDocument;
        $dom->formatOutput = true;
//        error_log(print_r($dom->saveXML(), true));
        echo $dom->saveXML();
    }

    protected function get_app_cons() {
        if (is_null($this->app_conns)) {
            require_once 'biNu/App_Connections.php';
            $this->app_conns = new App_Connections(APP_NAME, $this->cur_url());
        }
        return $this->app_conns;
    }

    protected function gen_in_app_url(
    $method, $args = null, $controller = null
    ) {
        if (is_null($controller)) {
            $controller = get_class($this);
        }
        $url = $this->base_url() . 'index.php?c=' . urlencode($controller)
                . '&m=' . urlencode($method);
        if (!is_null($args)) {
            if (!is_array($args)) {
                $args = array($args);
            }
            // check if an arg needs to be serialized
            foreach ($args as &$arg) {
                if (is_object($arg) || is_array($arg)) {
                    $arg = serialize($arg);
                }
            }
            $url .= '&args=' . urlencode(implode('|', $args));
        }
        return $url . '&r=' . time();
    }

    protected function base_url() {
        $cur_url = $this->cur_url();
        return substr($cur_url, 0, strrpos($cur_url, '/') + 1);
    }

    protected function cur_url() {
        $s = !empty($_SERVER['HTTPS']) && $_SERVER["HTTPS"] === 'on' ? 's' : '';
        $sp = strtolower($_SERVER['SERVER_PROTOCOL']);
        $proto = substr($sp, 0, strpos($sp, '/')) . $s . '://';
        if ($_SERVER['SERVER_PORT'] === '80') {
            $port = '';
        } else {
            $port = ':' . $_SERVER["SERVER_PORT"];
        }
        return $proto . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
    }

    protected function set_common_styles() {
        $this->bml_doc->set_style(
                'heading', '#FFFFFF', 'Arial Unicode MS', $this->bml_doc->title_font_size
        );
        $this->bml_doc->set_style(
                'sub_heading', '#666666', 'Arial Unicode MS', round($this->bml_doc->font_size * 1.05)
        );
        $this->bml_doc->set_style('footer_bg_color', FOOTER_BG_COLOR);
        $this->bml_doc->set_style('button_bg', FOOTER_BG_COLOR);
        $this->bml_doc->set_style('grey_button_bg', '#444444');
        $this->bml_doc->set_style('button_border', BORDER_COLOR);
        $this->bml_doc->set_style('grey', '#444444');
        $this->bml_doc->set_style(
                'button_text', '#FFFFFF', 'Arial Unicode MS', $this->bml_doc->font_size
        );
        $this->bml_doc->set_style(
                'body', '#666666', 'Arial Unicode MS', $this->bml_doc->font_size
        );
        $this->bml_doc->set_style(
                'link', FOOTER_BG_COLOR, 'Arial Unicode MS', $this->bml_doc->font_size
        );
    }

    protected function gen_menu() {
        $this->bml_doc->set_menu_item('Sign Out', $this->gen_in_app_url('home') . '&r=' . time());
        $this->bml_doc->set_menu_item('MATS Home', $this->gen_in_app_url('login_to_mats_agent'));
    }

}
