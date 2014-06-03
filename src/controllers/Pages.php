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

        $title = 'MATS Agent';
        $homescreen_items = array(
            array(
                'text' => 'Sign In',
                'url' => $this->gen_in_app_url('sigin_in_page', array(), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'Help',
                'url' => $this->gen_in_app_url('help_not_implemented', null, 'GeneralTextPages'),
                'nav' => 'normal_button',
            ),
        );
        $this->gen_button_page($title, $homescreen_items);
    }

    public function gen_button_page($title, $screen_items) {
        $bml = $this->bml_doc->get_bml();
        $this->gen_banner($bml->page, $title);
        $main_psegment = $bml->page->gen_psegment(
                array('x' => '0', 'y' => 'y', 'scroll_type' => 'panning')
        );
        $y = round($this->bml_doc->line_height * 0.5);
        $x = round(SCREEN_WIDTH * 0.01);
        $yaxis = round(SCREEN_HEIGHT * 0.8) - round($this->bml_doc->line_height * 1.5);

        foreach ($screen_items as $screen_item) {
            if ($screen_item['nav'] != 'navigation_button') {
                $button_params = array(
                    'url' => $screen_item['url'],
                    'text' => $screen_item['text'],
                    'text_style' => 'button_text',
                    'text_align' => 'center',
                    'text_y_indent' => round($this->bml_doc->line_height * 0.25),
                    'x' => round(SCREEN_WIDTH * 0.1),
                    'y' => $y,
                    'w' => round(SCREEN_WIDTH * 0.8),
                    'h' => round($this->bml_doc->line_height * 1.5),
                    'bg_style' => 'footer_bg_color',
                );
                if ($screen_item['url'] != $this->gen_in_app_url('help_not_implemented', null, 'GeneralTextPages') && $screen_item['url'] != $this->gen_in_app_url('home')) {
                    $button_params['bg_style'] = 'footer_bg_color';
                } else {
                    $button_params['bg_style'] = 'grey';
                }
                $y += round($this->bml_doc->line_height * 2);
            } else {
//always make sure the signout and home page buttons are at the base

                if ($yaxis < $y) {
                    $yaxis = $y;
                }
                $button_params = array(
                    'x' => $x,
                    'y' => $yaxis,
                    'w' => round(SCREEN_WIDTH * 0.3),
                    'h' => round($this->bml_doc->line_height * 1.5),
                    'bg_style' => 'grey_button_bg',
                    'url' => $home_url,
                    'text' => $screen_item['text'],
                    'text_style' => 'button_text',
                    'text_align' => 'center',
                    'text_y_indent' => round($this->bml_doc->line_height * 0.25),
                );
                $x = (round(SCREEN_WIDTH) - round(SCREEN_WIDTH * 0.3) - round(SCREEN_WIDTH * 0.01));
                $button_params['bg_style'] = 'grey';
            }
            $button_params['url'] = $screen_item['url'];
            $main_psegment->gen_button($button_params);
        }
        $this->render_bml($bml);
    }

    public function login_to_mats_agent() {
        $title = 'Choose a MATS Service';
        $homescreen_items = array(
            array(
                'text' => 'Cash In',
                // 'url' => $this->gen_in_app_url('cash_in'),
                'url' => $this->gen_in_app_url('make_payment_form', array('Subscriber Personal Account', 'login_to_mats_agent', '0'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'Cash Out',
                'url' => $this->gen_in_app_url('cash_out'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'Merchant Payment',
                'url' => $this->gen_in_app_url('make_payment'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'Bill Payment',
                'url' => $this->gen_in_app_url('bill_payment'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'Float Transfer',
                'url' => $this->gen_in_app_url('float_transfer'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'Sell Airtime',
                'url' => $this->gen_in_app_url('sell_airtime'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'My Account',
                'url' => $this->gen_in_app_url('my_account'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'Sign Out',
                'url' => $this->gen_in_app_url('home'),
                'nav' => 'normal_button',
            ),
        );
        $this->gen_button_page($title, $homescreen_items);
    }

    public function cash_in() {
        $title = 'Cash In';
        $cashin_items = array(
            array(
                'text' => 'Deposit From Customers',
                'url' => $this->gen_in_app_url('not_implemented', null, 'GeneralTextPages'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'Payment',
                'url' => $this->gen_in_app_url('not_implemented', null, 'GeneralTextPages'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'Sign Out',
                'url' => $this->gen_in_app_url('home'),
                'nav' => 'navigation_button',
            ),
            array(
                'text' => 'Back',
                'url' => $this->gen_in_app_url('login_to_mats_agent'),
                'nav' => 'navigation_button',
            ),
        );
        $this->gen_button_page($title, $cashin_items);
    }

    public function cash_out() {
        $title = 'Cash Out';
        $cashout_items = array(
            array(
                'text' => 'From Dealer Account',
                'url' => $this->gen_in_app_url('cashout_form', '0', 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'From MATS Inflow Account',
                'url' => $this->gen_in_app_url('cashout_form', '1', 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'From Token',
                'url' => $this->gen_in_app_url('cashout_form', '2', 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'Sign Out',
                'url' => $this->gen_in_app_url('home'),
                'nav' => 'navigation_button',
            ),
            array(
                'text' => 'Back',
                'url' => $this->gen_in_app_url('login_to_mats_agent'),
                'nav' => 'navigation_button',
            ),
        );
        $this->gen_button_page($title, $cashout_items);
    }

    public function float_transfer() {
        $title = 'Transfer Float';
        $transferfloat_items = array(
            array(
                'text' => 'Recieve Float',
                 'url' => $this->gen_in_app_url('make_payment_form', array('Dealer Account', 'float_transfer', '1'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'Return Float',
                'url' => $this->gen_in_app_url('cashout_form', '3', 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'Sign Out',
                'url' => $this->gen_in_app_url('home'),
                'nav' => 'navigation_button',
            ),
            array(
                'text' => 'Back',
                'url' => $this->gen_in_app_url('login_to_mats_agent'),
                'nav' => 'navigation_button',
            ),
        );
        $this->gen_button_page($title, $transferfloat_items);
    }

    public function make_payment() {
        $title = 'Merchants Collecting Payments';
        $makepayment_items = array(
            array(
                'text' => 'Shoprite',
                'url' => $this->gen_in_app_url('make_payment_form', array('Shoprite', 'make_payment', '0'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'Cash n Carry',
                'url' => $this->gen_in_app_url('make_payment_form', array('Cash n Carry', 'make_payment', '1'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'Park n Shop',
                'url' => $this->gen_in_app_url('make_payment_form', array('Park n Shop', 'make_payment', '2'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'Konga',
                'url' => $this->gen_in_app_url('make_payment_form', array('Konga', 'make_payment', '3'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'Jumia',
                'url' => $this->gen_in_app_url('make_payment_form', array('Jumia', 'make_payment', '4'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'Silverbird Cinemas',
                'url' => $this->gen_in_app_url('make_payment_form', array('Silverbird Cinemas', 'make_payment', '5'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'K F C',
                'url' => $this->gen_in_app_url('make_payment_form', array('K F C', 'make_payment', '6'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'Chicken Republic',
                'url' => $this->gen_in_app_url('make_payment_form', array('Chicken Republic', 'make_payment', '7'), 'Forms'),
                'nav' => 'normal_button',
            ),
        );
        $back_url = $this->gen_in_app_url('login_to_mats_agent');

        $this->gen_list_page($makepayment_items, $title, $back_url);
    }

    public function bill_payment() {
        $title = 'Utility Bills';
        $billpayment_items = array(
            array(
                'text' => 'PHCN',
                'url' => $this->gen_in_app_url('pay_bills_form', array('PHCN', 'bill_payment', '0'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'DSTV',
                'url' => $this->gen_in_app_url('pay_bills_form', array('DSTV', 'bill_payment', '1'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'WATER BILL',
                'url' => $this->gen_in_app_url('pay_bills_form', array('WATER BILL', 'bill_payment', '2'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'LAWMA',
                'url' => $this->gen_in_app_url('pay_bills_form', array('LAWMA', 'bill_payment', '3'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'LCC Toll Gate',
                'url' => $this->gen_in_app_url('pay_bills_form', array('LCC Toll Gate', 'bill_payment', '4'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'Security Bill',
                'url' => $this->gen_in_app_url('pay_bills_form', array('Security Bill', 'bill_payment', '5'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'Vehicle Licensing',
                'url' => $this->gen_in_app_url('pay_bills_form', array('Vehicle Licensing', 'bill_payment', '6'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'Land-use Bill',
                'url' => $this->gen_in_app_url('pay_bills_form', array('Land-use Bill', 'bill_payment', '7'), 'Forms'),
                'nav' => 'normal_button',
            ),
        );
        $back_url = $this->gen_in_app_url('login_to_mats_agent');

        $this->gen_list_page($billpayment_items, $title, $back_url);
    }

    public function sell_airtime() {
        $title = 'Choose a GSM Operator';
        $sellairtime_items = array(
            array(
                'text' => 'MTN',
                'url' => $this->gen_in_app_url('send_airtime_form', array('MTN', 'sell_airtime', '0'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'AIRTEL',
                'url' => $this->gen_in_app_url('send_airtime_form', array('AIRTEL', 'sell_airtime', '1'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'ETISALAT',
                'url' => $this->gen_in_app_url('send_airtime_form', array('ETISALAT', 'sell_airtime', '2'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'GLO',
                'url' => $this->gen_in_app_url('send_airtime_form', array('GLO', 'sell_airtime', '3'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'STARCOMMS',
                'url' => $this->gen_in_app_url('send_airtime_form', array('STARCOMMS', 'sell_airtime', '4'), 'Forms'),
                'nav' => 'normal_button',
            ), array(
                'text' => 'CELTEL',
                'url' => $this->gen_in_app_url('send_airtime_form', array('CELTEL', 'sell_airtime', '5'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'ZAIN',
                'url' => $this->gen_in_app_url('send_airtime_form', array('ZAIN', 'sell_airtime', '6'), 'Forms'),
                'nav' => 'normal_button',
            ), array(
                'text' => 'ORANGE',
                'url' => $this->gen_in_app_url('send_airtime_form', array('ORANGE', 'sell_airtime', '7'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'ECONET',
                'url' => $this->gen_in_app_url('send_airtime_form', array('ECONET', 'sell_airtime', '8'), 'Forms'),
                'nav' => 'normal_button',
            ), array(
                'text' => 'VODACOM',
                'url' => $this->gen_in_app_url('send_airtime_form', array('VODACOM', 'sell_airtime', '9'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'U-COM',
                'url' => $this->gen_in_app_url('send_airtime_form', array('U-COM', 'sell_airtime', '10'), 'Forms'),
                'nav' => 'normal_button',
            ), array(
                'text' => 'TIGO',
                'url' => $this->gen_in_app_url('send_airtime_form', array('TIGO', 'sell_airtime', '11'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'MTEL',
                'url' => $this->gen_in_app_url('send_airtime_form', array('MTEL', 'sell_airtime', '12'), 'Forms'),
                'nav' => 'normal_button',
            ), array(
                'text' => 'ZOOM',
                'url' => $this->gen_in_app_url('send_airtime_form', array('ZOOM', 'sell_airtime', '13'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'MULTILINKS TELKOM',
                'url' => $this->gen_in_app_url('send_airtime_form', array('VODACOM', 'sell_airtime', '14'), 'Forms'),
                'nav' => 'normal_button',
            ), array(
                'text' => 'SAFARICOM',
                'url' => $this->gen_in_app_url('send_airtime_form', array('SAFARICOM', 'sell_airtime', '15'), 'Forms'),
                'nav' => 'normal_button',
            ), array(
                'text' => 'VODAFONE',
                'url' => $this->gen_in_app_url('send_airtime_form', array('VODAFONE', 'sell_airtime', '16'), 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'VISAFONE',
                'url' => $this->gen_in_app_url('send_airtime_form', array('VISAFONE', 'sell_airtime', '17'), 'Forms'),
                'nav' => 'normal_button',
            ),
        );
        $back_url = $this->gen_in_app_url('login_to_mats_agent');

        $this->gen_list_page($sellairtime_items, $title, $back_url);
    }

    public function my_account() {
        $title = 'My Account';
        $myaccount_items = array(
            array(
                'text' => 'Check Balance',
                'url' => $this->gen_in_app_url('balance_check'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'View Statement',
                'url' => $this->gen_in_app_url('view_statement'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'Change Pin',
                'url' => $this->gen_in_app_url('chnage_pin_form', null, 'Forms'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => 'Sign Out',
                'url' => $this->gen_in_app_url('home'),
                'nav' => 'navigation_button',
            ),
            array(
                'text' => 'Back',
                'url' => $this->gen_in_app_url('login_to_mats_agent'),
                'nav' => 'navigation_button',
            ),
        );
        $this->gen_button_page($title, $myaccount_items);
    }

    public function view_statement() {
        $title = 'View Statement';
        $statement_items = array(
            array(
                'text' => date("Y-m-d H:i:s") . ' || Shoprite || NGN 6,000.00',
                'url' => $this->gen_in_app_url('transaction_details'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => date("Y-m-d H:i:s") . ' || Cash n Carry || NGN 6,000.00',
                'url' => $this->gen_in_app_url('transaction_details'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => date("Y-m-d H:i:s") . ' || Park n Shop || NGN 6,000.00',
                'url' => $this->gen_in_app_url('transaction_details'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => date("Y-m-d H:i:s") . ' || Konga || NGN 6,000.00',
                'url' => $this->gen_in_app_url('transaction_details'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => date("Y-m-d H:i:s") . ' || Jumia || NGN 6,000.00',
                'url' => $this->gen_in_app_url('transaction_details'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => date("Y-m-d H:i:s") . ' || Silverbird Cinemas || NGN 6,000.00',
                'url' => $this->gen_in_app_url('transaction_details'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => date("Y-m-d H:i:s") . ' || K F C || NGN 6,000.00',
                'url' => $this->gen_in_app_url('transaction_details'),
                'nav' => 'normal_button',
            ),
            array(
                'text' => date("Y-m-d H:i:s") . ' || Chicken Republic || NGN 6,000.00',
                'url' => $this->gen_in_app_url('transaction_details'),
                'nav' => 'normal_button',
            ),
        );
        $back_url = $this->gen_in_app_url('my_account');

        $this->gen_list_page($statement_items, $title, $back_url);
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
        $back_url = $this->gen_in_app_url('login_to_mats_agent');

        $this->gen_list_page($list_items, 'Send Money', $back_url);
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
        $back_url = $this->gen_in_app_url('login_to_mats_agent');

        $this->gen_list_page($list_items, 'Send Money', $back_url);
    }

    public function send_airtime_process($ph_num, $redirectfunc, $amount) {
        $title = 'Airtime Sale Notification';
        setlocale(LC_MONETARY, 'en_NG');
        $list_items = array(
            array('text' => 'Airtime sale of ' . money_format('%i', $amount)),
            array('text' => ' successfully sold to subscriber :'),
            array('text' => $ph_num),
        );
        $back_url = $this->gen_in_app_url($redirectfunc);

        $this->gen_text_page($list_items, $title, $back_url);
    }

    public function pay_bills_process($utility_name, $redirectfunc, $amount) {
        $title = 'Payment Notification';
        setlocale(LC_MONETARY, 'en_NG');
        $list_items = array(
            array('text' => 'Utility Bill of ' . money_format('%i', $amount)),
            array('text' => 'was successfully paid for :'),
            array('text' => $utility_name),
        );
        $back_url = $this->gen_in_app_url($redirectfunc);

        $this->gen_text_page($list_items, $title, $back_url);
    }

    public function make_payment_process($recipient, $redirectfunc, $amount) {
        $title = 'Payment Notification';
        setlocale(LC_MONETARY, 'en_NG');

        $list_items = array(
            array('text' => 'Payment of ' . money_format('%i', $amount)),
            array('text' => 'was successfully made and'),
            array('text' => 'the ewallet credited for :'),
            array('text' => $recipient),
        );
        $back_url = $this->gen_in_app_url($redirectfunc);

        $this->gen_text_page($list_items, $title, $back_url);
    }

    public function pay_bills_confirm($utility_name, $redirectfunc, $direction) {
        $pin_hidden = str_repeat('•', strlen($_GET['3']));
        setlocale(LC_MONETARY, 'en_NG');


        $list_items = array(
            array('text' => 'Utility Name: ' . $utility_name),
            array('text' => 'Customer ID Number: ' . $_GET['1']),
            array('text' => 'Amount: ' . money_format('%i', $_GET['2'])),
            array('text' => 'Fees: 0'),
            array('text' => 'Total: ' . money_format('%i', $_GET['2'])),
        );

        $final_process = 'pay_bills_process';
        $cancel_url = $this->gen_in_app_url('pay_bills_form', array($utility_name, $redirectfunc, $direction), 'Forms');

        $redirection_arg = array('recipient' => $utility_name, 'redirectfunc' => $redirectfunc, 'final_process' => $final_process, 'cancel_url' => $cancel_url);

        $this->gen_list_confirm_button($list_items, $redirection_arg);
    }

    public function make_payment_confirm($recipient, $redirectfunc, $direction) {
        $pin_hidden = str_repeat('•', strlen($_GET['3']));
        setlocale(LC_MONETARY, 'en_NG');


        $list_items = array(
            array('text' => 'Beneficiary Name: ' . $recipient),
            array('text' => 'MSISDN / Wallet Number: ' . $_GET['1']),
            array('text' => 'Amount: ' . money_format('%i', $_GET['2'])),
            array('text' => 'Fees: 0'),
            array('text' => 'Total: ' . money_format('%i', $_GET['2'])),
        );

        $final_process = 'make_payment_process';
        $cancel_url = $this->gen_in_app_url('make_payment_form', array($recipient, $redirectfunc, $direction), 'Forms');

        $redirection_arg = array('recipient' => $recipient, 'redirectfunc' => $redirectfunc, 'final_process' => $final_process, 'cancel_url' => $cancel_url);

        $this->gen_list_confirm_button($list_items, $redirection_arg);
    }

    public function send_airtime_confirm($network_op, $redirectfunc, $direction) {
        $pin_hidden = str_repeat('•', strlen($_GET['3']));
        setlocale(LC_MONETARY, 'en_NG');


        $list_items = array(
            array('text' => 'Telcom Network: ' . $network_op),
            array('text' => 'Subscriber: ' . $_GET['1']),
            array('text' => 'Amount: ' . money_format('%i', $_GET['2'])),
            array('text' => 'Fees: 0'),
            array('text' => 'Total: ' . money_format('%i', $_GET['2'])),
        );

        $final_process = 'send_airtime_process';
        $cancel_url = $this->gen_in_app_url('send_airtime_form', array($network_op, $redirectfunc, $direction), 'Forms');

        $redirection_arg = array('recipient' => $network_op, 'redirectfunc' => $redirectfunc, 'final_process' => $final_process, 'cancel_url' => $cancel_url);

        $this->gen_list_confirm_button($list_items, $redirection_arg);
    }

    public function gen_list_confirm_button($list_items, $redirection_arg) {
        $recipient = $redirection_arg['recipient'];
        $redirectfunc = $redirection_arg['redirectfunc'];
        $final_process = $redirection_arg['final_process'];
        $cancel_url = $redirection_arg['cancel_url'];

        $bml = $this->bml_doc->get_bml();
        $this->gen_banner($bml->page, 'Confirmation');

        $resultset = $bml->page->gen_list(array(
            'items' => $list_items,
            'line_height' => $this->bml_doc->line_height,
            'text_style' => 'body',
            'text_mode' => 'truncate',
            'x' => $this->bml_doc->indent,
            'y' => 'y + ' . $this->bml_doc->indent,
        ));

        $main_psegment = $resultset['segment'];
        $yaxis = $resultset['yaxis'] + round($this->bml_doc->line_height * 1.5);
        $main_psegment->gen_button(array(
            'x' => $x,
            'y' => $yaxis,
            'w' => round(SCREEN_WIDTH * 0.3),
            'h' => round($this->bml_doc->line_height * 1.5),
            'bg_style' => 'grey_button_bg',
            'text_style' => 'button_text',
            'text_align' => 'center',
            'text_y_indent' => round($this->bml_doc->line_height * 0.25),
            'url' => $this->gen_in_app_url($final_process, array($recipient, $redirectfunc, $_GET['2'])),
            'text' => 'Confirm',
        ));
        $main_psegment->gen_button(array(
            'x' => round(SCREEN_WIDTH - round(SCREEN_WIDTH * 0.3)),
            'y' => $yaxis,
            'w' => round(SCREEN_WIDTH * 0.3),
            'h' => round($this->bml_doc->line_height * 1.5),
            'bg_style' => 'grey_button_bg',
            'text_style' => 'button_text',
            'text_align' => 'center',
            'text_y_indent' => round($this->bml_doc->line_height * 0.25),
            'url' => $cancel_url,
            'text' => 'Cancel',
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
        $back_url = $this->gen_in_app_url('login_to_mats_agent');

        $this->gen_list_page($list_items, 'My Profile', $back_url);
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
        $back_url = $this->gen_in_app_url('login_to_mats_agent');

        $this->gen_list_page($list_items, 'Cashout', $back_url);
    }

    public function cashout_confirm($cashout_type) {
        $bml = $this->bml_doc->get_bml();
        $this->gen_banner($bml->page, 'Confirmation');
        setlocale(LC_MONETARY, 'en_NG');
        $pin_hidden = str_repeat('•', strlen($_GET['2']));
        $list_items = array(
            array('text' => 'Amount: ' . money_format('%i', $_GET['1'])),
        );


        $resultset = $bml->page->gen_list(array(
            'items' => $list_items,
            'line_height' => $this->bml_doc->line_height,
            'text_style' => 'body',
            'text_mode' => 'truncate',
            'x' => $this->bml_doc->indent,
            'y' => 'y + ' . $this->bml_doc->indent,
        ));

        $main_psegment = $resultset['segment'];
        $yaxis = $resultset['yaxis'] + round($this->bml_doc->line_height * 1.5);
        $main_psegment->gen_button(array(
            'x' => $x,
            'y' => $yaxis,
            'w' => round(SCREEN_WIDTH * 0.3),
            'h' => round($this->bml_doc->line_height * 1.5),
            'bg_style' => 'grey_button_bg',
            'text_style' => 'button_text',
            'text_align' => 'center',
            'text_y_indent' => round($this->bml_doc->line_height * 0.25),
            'url' => $this->gen_in_app_url('cashout_process', array(
                $cashout_type, $_GET['1'], $_GET['2']
            )),
            'text' => 'Confirm',
        ));
        $main_psegment->gen_button(array(
            'x' => round(SCREEN_WIDTH - round(SCREEN_WIDTH * 0.3)),
            'y' => $yaxis,
            'w' => round(SCREEN_WIDTH * 0.3),
            'h' => round($this->bml_doc->line_height * 1.5),
            'bg_style' => 'grey_button_bg',
            'text_style' => 'button_text',
            'text_align' => 'center',
            'text_y_indent' => round($this->bml_doc->line_height * 0.25),
            'url' => $this->gen_in_app_url('cashout_form', $cashout_type, 'Forms'),
            'text' => 'Cancel',
        ));


        $this->render_bml($bml);
    }

    public function cashout_process($cashout_type, $amount, $pin) {
        $title = 'Cashout Successful';
        setlocale(LC_MONETARY, 'en_NG');
        $list_items = array(
            array('text' => 'Amount: ' . money_format('%i', $amount)),
            array('text' => 'Fees: 0'),
            array('text' => 'Total: ' . money_format('%i', $amount)),
             array('text' => 'Cashout Code: 21312321312'),
        );
        $back_url = $this->gen_in_app_url('cash_out');

        $this->gen_text_page($list_items, $title, $back_url);
    }

    public function balance_check() {
        $title = 'Balance';
        $back_url = $this->gen_in_app_url('my_account');
        $list_items = array(
            array('text' => 'Yours balance at '.date("Y-m-d H:i:s")),
            array('text' => 'is : NGN 5,000.00'),
        );

        $this->gen_text_page($list_items, $title, $back_url);
    }

    public function pin_change_status() {
        $title = 'Pin Change Notification';
        $back_url = $this->gen_in_app_url('my_account');
        $list_items = array(
            array('text' => 'Pin Changed Successfully.'),
        );
        $this->gen_text_page($list_items, $title, $back_url);
    }

    public function transaction_details() {
        $title = 'Transaction Details';
        $back_url = $this->gen_in_app_url('view_statement');
        $list_items = array(
            array('text' => 'NGN 5,000.00 was used to pay'),
            array('text' => 'PHCN Bills on '),
            array('text' => date("Y-m-d H:i:s"))
        );
        $this->gen_text_page($list_items, $title, $back_url);
    }

    protected function gen_text_page($items, $title, $back_url) {
        $bml = $this->bml_doc->get_bml();
        $this->gen_banner($bml->page, $title);

        $resultset = $bml->page->gen_list(array(
            'items' => $items,
            'line_height' => round($this->bml_doc->line_height * 1.5),
            'text_style' => 'body',
            'text_x_indent' => $this->bml_doc->indent,
            'text_y_indent' => round($this->bml_doc->line_height * 0.25),
            'y' => 'y',
        ));

        $this->gen_nav_buttons($resultset, $back_url);
        $this->render_bml($bml);
    }

    protected function gen_list_page($items, $title, $back_url) {
        $bml = $this->bml_doc->get_bml();
        $this->gen_banner($bml->page, $title);

        $resultset = $bml->page->gen_list(array(
            'items' => $items,
            'line_height' => round($this->bml_doc->line_height * 1.5),
            'text_style' => 'body',
            'text_x_indent' => $this->bml_doc->indent,
            'text_y_indent' => round($this->bml_doc->line_height * 0.25),
            'dividing_line_style' => 'grey',
            'y' => 'y',
        ));
        $this->gen_nav_buttons($resultset, $back_url);
        $this->render_bml($bml);
    }

    protected function gen_nav_buttons($resultset, $back_url) {
        $main_psegment = $resultset['segment'];
        $button_items = array(array(
                'text' => 'Sign Out',
                'url' => $this->gen_in_app_url('home'),
                'nav' => 'navigation_button',
            ),
            array(
                'text' => 'Back',
                'url' => $back_url,
                'nav' => 'navigation_button',
            ),
        );
        //  $newy = 'y + ' . DocConfig::$config['indent'];
        // $newy = 'y';
        $yaxis = round(SCREEN_HEIGHT * 0.8) - round($this->bml_doc->line_height * 1.5);

        if ($yaxis < $resultset['yaxis']) {
            $yaxis = $resultset['yaxis'] + round($this->bml_doc->line_height * 1.5);
        }
        foreach ($button_items as $screen_item) {
            $button_params = array(
                'x' => $x,
                'y' => $yaxis,
                'w' => round(SCREEN_WIDTH * 0.3),
                'h' => round($this->bml_doc->line_height * 1.5),
                'bg_style' => 'grey_button_bg',
                'text' => $screen_item['text'],
                'text_style' => 'button_text',
                'text_align' => 'center',
                'text_y_indent' => round($this->bml_doc->line_height * 0.25),
            );
            $x = (round(SCREEN_WIDTH) - round(SCREEN_WIDTH * 0.3) - round(SCREEN_WIDTH * 0.01));
            $button_params['bg_style'] = 'grey';
            //  $yaxis = 'y - ' . (DocConfig::$config['indent'] * 3);
            $button_params['url'] = $screen_item['url'];
            $main_psegment->gen_button($button_params);
        }
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
