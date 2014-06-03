<?php

require_once 'conf/conf.php';
require_once 'Controller.php';
require_once 'biNu/bml_builder/GeneralTextBmlDoc.php';

class GeneralTextPages extends Controller
{
    /** @var BmlDocument $bml_doc */
    protected $bml_doc;

    public function not_implemented()
    {
        $home_url = $this->gen_in_app_url('login_to_mats_agent', null, 'Pages');
        $this->gen_general_text(
            'Function not implemented in this Proof of Concept version.',
            function ($psegment) use ($home_url) {
                $psegment->gen_button(array(
                    'x' => round(SCREEN_WIDTH / 4) - DocConfig::$config['indent'],
                    'y' => 'y + ' . DocConfig::$config['indent'],
                    'w' => round(SCREEN_WIDTH / 2),
                    'h' => DocConfig::$config['line_height'],
                    'bg_style' => 'grey_button_bg',
                    'url' => $home_url,
                    'text' => 'Home Menu',
                    'text_style' => 'button_text',
                    'text_align' => 'center',
                ));
            }
        );
    }
    public function help_not_implemented()
    {
        $home_url = $this->gen_in_app_url('home', null, 'Pages');
        $this->gen_general_text(
            'This contains the list of FAQs.',
            function ($psegment) use ($home_url) {
                $psegment->gen_button(array(
                    'x' => round(SCREEN_WIDTH / 4) - DocConfig::$config['indent'],
                    'y' => 'y + ' . DocConfig::$config['indent'],
                    'w' => round(SCREEN_WIDTH / 2),
                    'h' => DocConfig::$config['line_height'],
                    'bg_style' => 'grey_button_bg',
                    'url' => $home_url,
                    'text' => 'Back',
                    'text_style' => 'button_text',
                    'text_align' => 'center',
                ));
            }
        );
    }

    protected function gen_general_text($text, Closure $footer_init_fn = null)
    {
        $this->bml_doc = new GeneralTextBmlDoc(
            TTL, DEV_ID, APP_ID, $_COOKIE, COOKIE_DOMAIN, LTYPE, FOOTER_BG_COLOR,
            FOOTER_TEXT_COLOR, BG_COLOR
        );
        $this->set_common_styles();
        $this->gen_menu();
        $this->bml_doc->create_page(
            $text, 'body', function ($bml_page) {
                $common_args = array(
                    'x' => '0',
                    'h' => round(DocConfig::$config['title_line_height'] * 1.5),
                    'w' => SCREEN_WIDTH,
                );
                $psegment_args = array_merge(
                    $common_args,
                    array(
                        'y' => '0',
                        'scroll_type' => 'fixed',
                        'bg_style' => 'footer_bg_color',
                    )
                );
                $psegment = $bml_page->gen_psegment($psegment_args);
                $text_args = array_merge(
                    $common_args,
                    array(
                        'y' => round(DocConfig::$config['title_line_height'] * 0.25),
                        'text' => 'Work In Progress',
                        'style' => 'heading',
                        'mode' => 'truncate',
                        'align' => 'center',
                    )
                );
                $psegment->gen_text($text_args);
            }, $footer_init_fn
        );
        $this->render_bml($this->bml_doc->get_bml());
    }
}
