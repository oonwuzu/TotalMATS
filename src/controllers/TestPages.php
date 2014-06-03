<?php

require_once 'conf/conf.php';
require_once 'Controller.php';
require_once 'biNu/bml_builder/BmlDocument.php';

class Pages extends Controller {

    /** @var BmlDocument $bml_doc */
    protected $bml_doc;

    public function __construct() {
        $this->bml_doc = new BmlDocument(
                TTL, DEV_ID, APP_ID, $_COOKIE, COOKIE_DOMAIN, LTYPE, FOOTER_BG_COLOR, FOOTER_TEXT_COLOR, BG_COLOR
        );

        // set styles common to practically all pages
        $this->set_common_styles();
        $this->gen_menu();
    }

    public function home() {

        $bml = $this->bml_doc->get_bml();
        $this->gen_banner($bml->page, 'M A T S Agent');
        $main_psegment = $bml->page->gen_psegment(
                array('x' => '0', 'y' => 'y', 'scroll_type' => 'panning')
        );
        $homescreen_items = array(
            array(
                'text' => 'Sign In',
                'url' => $this->gen_in_app_url('sigin_in_page', array(), 'Forms')
            ),
            array(
                'text' => 'Help',
                'url' => $this->gen_in_app_url('help_not_implemented', null, 'GeneralTextPages'),
            ),
        );
        $y = round($this->bml_doc->line_height * 0.5);

        foreach ($homescreen_items as $homescreen_item) {
            $button_params = array(
                'url' => $homescreen_item['url'],
                'text' => $homescreen_item['text'],
                'text_style' => 'button_text',
                'text_align' => 'center',
                'text_y_indent' => round($this->bml_doc->line_height * 0.25),
                'x' => round(SCREEN_WIDTH * 0.1),
                'y' => $y,
                'w' => round(SCREEN_WIDTH * 0.8),
                'h' => round($this->bml_doc->line_height * 1.5),
                'bg_style' => 'footer_bg_color',
            );
            if ($homescreen_item['url'] != $this->gen_in_app_url('not_implemented', null, 'GeneralTextPages')) {
                $button_params['url'] = $homescreen_item['url'];
                $button_params['bg_style'] = 'footer_bg_color';
            } else {
                $button_params['bg_style'] = 'grey';
            }
            $main_psegment->gen_button($button_params);
            $y += round($this->bml_doc->line_height * 2);
        }
        $this->render_bml($bml);
    }

    public function login_to_my_wallet() {
        $bml = $this->bml_doc->get_bml();
        $this->gen_banner($bml->page, 'My Wallet');
        $main_psegment = $bml->page->gen_psegment(
                array('x' => '0', 'y' => 'y', 'scroll_type' => 'panning')
        );
        $homescreen_items = array(
            array(
                'text' => 'Cash In',
                'url' => $this->gen_in_app_url('cash_in'),
            ),
            array(
                'text' => 'Cash Out',
                'url' => $this->gen_in_app_url('cash_out'),
            ),
            array(
                'text' => 'Float Transfer',
                'url' => $this->gen_in_app_url('float_transfer'),
            ),
            array(
                'text' => 'Check Balance',
                'url' => $this->gen_in_app_url('check_balance'),
            ),
            array(
                'text' => 'View Statement',
                'url' => $this->gen_in_app_url('view_statement'),
            ),
            array(
                'text' => 'Sign Out',
                'url' => $this->gen_in_app_url('home'),
            ),
        );
        $this->gen_list_buttons($homescreen_items);

        $this->render_bml($bml);
    }

    public function cash_in() {
        $bml = $this->bml_doc->get_bml();
        $this->gen_banner($bml->page, 'Cash In');
        $main_psegment = $bml->page->gen_psegment(
                array('x' => '0', 'y' => 'y', 'scroll_type' => 'panning')
        );
        $cashin_items = array(
            array(
                'text' => 'Deposit From Customers',
                'url' => $this->gen_in_app_url('deposit_from_customers'),
            ),
            array(
                'text' => 'Payment',
                'url' => $this->gen_in_app_url('payment'),
            ),
        );

        $y = $this->gen_list_buttons($cashin_items);
       // $this->display_navigation_button($main_psegment, $y, $this->gen_in_app_url('cash_in'));
        $this->render_bml($bml);
    }

/*    public function display_navigation_button($main_psegment, $y, $backurl) {
        $x = round(SCREEN_WIDTH * 0.1);
        $newY = $y + round($this->bml_doc->line_height * 2);

        $navbutton_items = array(
            array(
                'text' => 'Sign Out',
                'url' => $this->gen_in_app_url('sign_out'),
            ),
            array(
                'text' => 'Back',
                'url' => $backurl,
            ),
        );
        foreach ($navbutton_items as $navbutton_item) {
            $button_params = array(
                'x' => $x,
                'y' => $newY,
                'w' => round(SCREEN_WIDTH / 2),
                'h' => DocConfig::$config['line_height'],
                'bg_style' => 'grey_button_bg',
                'url' => $navbutton_item['url'],
                'text' => $navbutton_item['text'],
                'text_style' => 'button_text',
                'text_align' => 'center',
            );
            $main_psegment->gen_button($button_params);
            $x = round(SCREEN_WIDTH / 4) - DocConfig::$config['indent'];
        }
    }*/

    public function cash_out() {
        $bml = $this->bml_doc->get_bml();
        $this->gen_banner($bml->page, 'Cash Out');
        $main_psegment = $bml->page->gen_psegment(
                array('x' => '0', 'y' => 'y', 'scroll_type' => 'panning')
        );
        $cashout_items = array(
            array(
                'text' => 'From Dealer Account',
                'url' => $this->gen_in_app_url('from_dealer_account'),
            ),
            array(
                'text' => 'From MATS Inflow Account',
                'url' => $this->gen_in_app_url('from_mats_account'),
            ),
            array(
                'text' => 'Float Transfer',
                'url' => $this->gen_in_app_url('float_transfer'),
            ),
        );
        $navbutton_items = array(
            array(
                'text' => 'Sign Out',
                'url' => $this->gen_in_app_url('sign_out'),
            ),
            array(
                'text' => 'Back',
                'url' => $this->gen_in_app_url('back'),
            ),
        );
        $y = $this->gen_list_buttons($cashout_items);
      //  $this->display_navigation_button($main_psegment, $y, $this->gen_in_app_url('cash_out'));

        $this->render_bml($bml);
    }

    public function float_transfer() {
        $bml = $this->bml_doc->get_bml();
        $this->gen_banner($bml->page, 'Float Transfer');
        $main_psegment = $bml->page->gen_psegment(
                array('x' => '0', 'y' => 'y', 'scroll_type' => 'panning')
        );
        $floattxn_items = array(
            array(
                'text' => 'Recieve Float',
                'url' => $this->gen_in_app_url('revieve_float'),
            ),
            array(
                'text' => 'Return Float',
                'url' => $this->gen_in_app_url('return_float'),
            ),
            array(
                'text' => 'Float Transfer',
                'url' => $this->gen_in_app_url('float_transfer'),
            ),
        );
        $y = $this->gen_list_buttons($floattxn_items);
    //    $this->display_navigation_button($main_psegment, $y, $this->gen_in_app_url('float_transfer'));

        $this->render_bml($bml);
    }

    public function check_balance() {
        $bml = $this->bml_doc->get_bml();
        $this->gen_banner($bml->page, 'Account Balance');
        $main_psegment = $bml->page->gen_psegment(
                array('x' => '0', 'y' => 'y', 'scroll_type' => 'panning')
        );
        $navbutton_items = array(
            array(
                'text' => 'Sign Out',
                'url' => $this->gen_in_app_url('sign_out'),
            ),
            array(
                'text' => 'Back',
                'url' => $this->gen_in_app_url('back'),
            ),
        );
        $y = $this->gen_list_buttons($checkbal_items);
  //      $this->display_navigation_button($main_psegment, $y, $this->gen_in_app_url('check_balance'));

        $this->render_bml($bml);
    }
    public function view_statement() {
        $bml = $this->bml_doc->get_bml();
        $this->gen_banner($bml->page, 'Account Balance');
        $main_psegment = $bml->page->gen_psegment(
                array('x' => '0', 'y' => 'y', 'scroll_type' => 'panning')
        );
        $navbutton_items = array(
            array(
                'text' => 'Sign Out',
                'url' => $this->gen_in_app_url('sign_out'),
            ),
            array(
                'text' => 'Back',
                'url' => $this->gen_in_app_url('back'),
            ),
        );
        $y = $this->gen_list_buttons($checkbal_items);
   ///     $this->display_navigation_button($main_psegment, $y, $this->gen_in_app_url('check_balance'));

        $this->render_bml($bml);
    }

    public function gen_list_buttons($button_items) {
        $y = round($this->bml_doc->line_height * 0.5);

        foreach ($button_items as $homescreen_item) {
            $button_params = array(
                'url' => $homescreen_item['url'],
                'text' => $homescreen_item['text'],
                'text_style' => 'button_text',
                'text_align' => 'center',
                'text_y_indent' => round($this->bml_doc->line_height * 0.25),
                'x' => round(SCREEN_WIDTH * 0.1),
                'y' => $y,
                'w' => round(SCREEN_WIDTH * 0.8),
                'h' => round($this->bml_doc->line_height * 1.5),
                'bg_style' => 'footer_bg_color',
            );
            if ($homescreen_item['url'] != $this->gen_in_app_url('home')) {
                $button_params['url'] = $homescreen_item['url'];
                $button_params['bg_style'] = 'footer_bg_color';
            } else {
                $button_params['bg_style'] = 'grey';
            }
            $main_psegment->gen_button($button_params);
            $y += round($this->bml_doc->line_height * 2);
        }
        return $y;
    }

    public function send_money_select_recipient() {
        $list_items = array(
            array(
                'text' => 'To Self',
                'url' => $this->gen_in_app_url('send_money_select_direction', '0'),
            ),
            array(
                'text' => 'To Others',
                'url' => $this->gen_in_app_url('send_money_select_direction', '1'),
            ),
        );
        $this->gen_list_page($list_items, 'Send Money');
    }

    public function send_money_select_direction($recipient) {
        $list_items = array(
            array(
                'text' => 'Bank to Wallet',
                'url' => $this->gen_in_app_url('send_money_form', array($recipient, '0'), 'Forms'),
            ),
            array(
                'text' => 'Wallet to Bank',
                'url' => $this->gen_in_app_url('send_money_form', array($recipient, '1'), 'Forms'),
            ),
            array(
                'text' => 'Inter Bank Transfer',
                'url' => $this->gen_in_app_url('send_money_form', array($recipient, '1'), 'Forms'),
            ),
        );
        $this->gen_list_page($list_items, 'Send Money');
    }

    public function send_money_confirm($recipient, $direction) {
        $bml = $this->bml_doc->get_bml();
        $this->gen_banner($bml->page, 'Confirm');

        $list_items = array(
            array('text' => 'Amount: ' . $_GET['2']),
            array('text' => 'To: ' . $_GET['1']),
            array('text' => 'From: 000000'), // @todo hardcoded
            array('text' => 'Fees: 0'),
            array('text' => 'Total: ' . $_GET['2']),
        );

        $main_psegment = $bml->page->gen_list(array(
            'items' => $list_items,
            'line_height' => $this->bml_doc->line_height,
            'text_style' => 'body',
            'text_x_indent' => $this->bml_doc->indent,
            'y' => 'y + ' . $this->bml_doc->indent,
        ));

        $main_psegment->gen_button(array(
            'x' => round(SCREEN_WIDTH / 4),
            'y' => 'y + ' . $this->bml_doc->indent,
            'w' => round(SCREEN_WIDTH / 2),
            'h' => $this->bml_doc->line_height,
            'bg_style' => 'grey_button_bg',
            'url' => $this->gen_in_app_url('send_money_process', array(
                $recipient, $direction, $_GET['1'], $_GET['2'], $_GET['3']
            )),
            'text' => 'Confirm',
            'text_style' => 'button_text',
            'text_align' => 'center',
        ));

        $this->render_bml($bml);
    }

    public function send_money_process($recipient, $direction, $ph_num, $amount) {
        $bml = $this->bml_doc->get_bml();
        $this->gen_banner($bml->page, 'My Wallet');
        $main_psegment = $bml->page->gen_psegment(
                array('y' => 'y', 'scroll_type' => 'fixed')
        );
        $main_psegment->gen_text(array(
            'text' => 'Success!',
            'style' => 'body',
            'mode' => 'truncate',
            'x' => $this->bml_doc->indent,
            'y' => 'y + ' . $this->bml_doc->indent,
            'h' => $this->bml_doc->line_height,
        ));
        $main_psegment->gen_button(array(
            'x' => round(SCREEN_WIDTH / 4),
            'y' => 'y + ' . $this->bml_doc->indent,
            'w' => round(SCREEN_WIDTH / 2),
            'h' => $this->bml_doc->line_height,
            'bg_style' => 'grey_button_bg',
            'url' => $this->gen_in_app_url('home'),
            'text' => 'Home',
            'text_style' => 'button_text',
            'text_align' => 'center',
        ));

        $this->render_bml($bml);
    }

    public function my_profile() {
        $list_items = array(
            array(
                'text' => 'Cashout',
                'url' => $this->gen_in_app_url('cashout_type'),
            ),
            array(
                'text' => 'Change Pin',
                'url' => $this->gen_in_app_url('not_implemented', null, 'GeneralTextPages'),
                'bg_style' => 'grey',
            ),
            array(
                'text' => 'FAQs',
                'url' => $this->gen_in_app_url('not_implemented', null, 'GeneralTextPages'),
                'bg_style' => 'grey',
            ),
            array(
                'text' => 'Check Balance',
                'url' => $this->gen_in_app_url('not_implemented', null, 'GeneralTextPages'),
                'bg_style' => 'grey',
            ),
            array(
                'text' => 'Transaction History',
                'url' => $this->gen_in_app_url('not_implemented', null, 'GeneralTextPages'),
                'bg_style' => 'grey',
            ),
        );
        $this->gen_list_page($list_items, 'My Profile');
    }

    public function cashout_type() {
        $list_items = array(
            array(
                'text' => 'Cashout agent',
                'url' => $this->gen_in_app_url('cashout_form', '0', 'Forms'),
            ),
            array(
                'text' => 'Change ATM',
                'url' => $this->gen_in_app_url('cashout_form', '1', 'Forms'),
            ),
        );
        $this->gen_list_page($list_items, 'Cashout');
    }

    public function cashout_confirm($cashout_type) {
        $bml = $this->bml_doc->get_bml();
        $this->gen_banner($bml->page, 'Confirmation');

        $pin_hidden = str_repeat('•', strlen($_GET['2']));
        $list_items = array(
            array('text' => 'Amount: ' . $_GET['1']),
            array('text' => 'Pin: ' . $pin_hidden),
        );

        $main_psegment = $bml->page->gen_list(array(
            'items' => $list_items,
            'line_height' => $this->bml_doc->line_height,
            'text_style' => 'body',
            'text_mode' => 'truncate',
            'x' => $this->bml_doc->indent,
            'y' => 'y + ' . $this->bml_doc->indent,
        ));

        $main_psegment->gen_button(array(
            'x' => round(SCREEN_WIDTH / 4),
            'y' => 'y + ' . $this->bml_doc->indent,
            'w' => round(SCREEN_WIDTH / 2),
            'h' => $this->bml_doc->line_height,
            'bg_style' => 'grey_button_bg',
            'url' => $this->gen_in_app_url('cashout_process', array(
                $cashout_type, $_GET['1'], $_GET['2']
            )),
            'text' => 'Confirm',
            'text_style' => 'button_text',
            'text_align' => 'center',
        ));

        $this->render_bml($bml);
    }

    public function cashout_process($cashout_type, $amount, $pin) {
        $bml = $this->bml_doc->get_bml();
        $this->gen_banner($bml->page, 'Confirmation');

        $pin_hidden = str_repeat('•', strlen($pin));
        $list_items = array(
            array('text' => 'Amount: ' . $amount),
            array('text' => 'Fees: 0'),
            array('text' => 'Total: ' . $amount),
            array('text' => 'Pin: ' . $pin_hidden),
            array('text' => 'CashoutCode: 21312321312'),
        );

        $subheading_psegment = $bml->page->gen_psegment(
                array('y' => 'y', 'scroll_type' => 'fixed')
        );
        $subheading_psegment->gen_text(array(
            'text' => 'Cashout Successful:',
            'style' => 'sub_heading',
            'mode' => 'truncate',
            'h' => '21',
            'x' => $this->bml_doc->indent,
            'y' => 'y + ' . $this->bml_doc->indent,
        ));

        $main_psegment = $bml->page->gen_list(array(
            'items' => $list_items,
            'line_height' => $this->bml_doc->line_height,
            'text_style' => 'body',
            'text_mode' => 'truncate',
            'y' => 'y',
        ));

        $main_psegment->gen_mark(array('name' => 'buttons_top', 'y' => 'y + ' . $this->bml_doc->indent));
        $x = round((SCREEN_WIDTH - (round(SCREEN_WIDTH / 3) * 2 + $this->bml_doc->indent)) / 2);
        $main_psegment->gen_button(array(
            'x' => $x,
            'y' => 'buttons_top',
            'w' => round(SCREEN_WIDTH / 3),
            'h' => $this->bml_doc->line_height,
            'bg_style' => 'grey_button_bg',
            'url' => $this->gen_in_app_url('home'),
            'text' => 'Home',
            'text_style' => 'button_text',
            'text_align' => 'center',
        ));
        $main_psegment->gen_button(array(
            'x' => $x + round(SCREEN_WIDTH / 3) + $this->bml_doc->indent,
            'y' => 'buttons_top',
            'w' => round(SCREEN_WIDTH / 3),
            'h' => $this->bml_doc->line_height,
            'bg_style' => 'grey_button_bg',
            'url' => $this->gen_in_app_url('home'),
            'text' => 'Logout',
            'text_style' => 'button_text',
            'text_align' => 'center',
        ));

        $this->render_bml($bml);
    }

    protected function gen_list_page($items, $title) {
        $bml = $this->bml_doc->get_bml();
        $this->gen_banner($bml->page, $title);
        $bml->page->gen_list(array(
            'items' => $items,
            'line_height' => round($this->bml_doc->line_height * 1.5),
            'text_style' => 'body',
            'text_x_indent' => $this->bml_doc->indent,
            'text_y_indent' => round($this->bml_doc->line_height * 0.25),
            'dividing_line_style' => 'grey',
            'y' => 'y',
        ));

        $this->render_bml($bml);
    }

    public function gen_banner(BmlElement &$bml_page, $text) {
        $common_args = array(
            'x' => '0',
            'h' => round($this->bml_doc->title_line_height * 1.5),
            'w' => SCREEN_WIDTH,
        );
        $psegment_args = array_merge(
                $common_args, array(
            'y' => '0',
            'scroll_type' => 'fixed',
            'bg_style' => 'footer_bg_color',
                )
        );
        $psegment = $bml_page->gen_psegment($psegment_args);
        $text_args = array_merge(
                $common_args, array(
            'y' => round($this->bml_doc->title_line_height * 0.25),
            'text' => $text,
            'style' => 'heading',
            'mode' => 'truncate',
            'align' => 'center',
                )
        );
        $psegment->gen_text($text_args);
    }

}
