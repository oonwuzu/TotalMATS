<?php

require_once 'conf/conf.php';
require_once 'Controller.php';
require_once 'biNu/bml_builder/BmlElement.php';
require_once 'biNu/bml_builder/BmlDocument.php';

class Forms extends Controller {

    protected $bml;

    public function __construct() {
        $this->bml = new BmlElement('<binu />');
        $this->bml->addAttribute('ttl', TTL);
        $this->bml->addAttribute('developer', DEV_ID);
        $this->bml->addAttribute('app', APP_ID);

        $this->bml->addChild('page');
        $bml_control = $this->bml->addChild('control');
        $bml_control->addAttribute('textUTF8', 'true');
        $bml_control->addChild('actions');
    }

    public function send_money_form($recipient, $direction) {
        $submit_url = $this->gen_in_app_url(
                'send_money_confirm', array($recipient, $direction), 'Pages'
        );

        $bml_action = $this->bml->control->actions->addChild('action', $submit_url);
        $bml_action->addAttribute('key', 'action');
        $bml_action->addAttribute('linkType', 'o');

        $this->bml->page->gen_text_entry(
                array(
                    'title' => '',
                    'fields' => array(
                        array(
                            'name' => 'Phone Number',
                            'mode' => 'phone',
                            'mandatory' => 'true',
                        ),
                        array(
                            'name' => 'Amount',
                            'mode' => 'numeric',
                            'mandatory' => 'true',
                        ),
                        array(
                            'name' => 'Pin',
                            'mode' => 'numeric',
                            'hide_value' => 'true',
                            'mandatory' => 'true',
                        ),
                    ),
                )
        );

        $this->render_bml($this->bml);
    }

    public function pay_bills_form($utility_name, $redirectfunc, $direction) {
        $submit_url = $this->gen_in_app_url(
                'pay_bills_confirm', array($utility_name, $redirectfunc, $direction), 'Pages'
        );

        $bml_action = $this->bml->control->actions->addChild('action', $submit_url);
        $bml_action->addAttribute('key', 'action');
        $bml_action->addAttribute('linkType', 'o');

        $this->bml->page->gen_text_entry(
                array(
                    'title' => '',
                    'fields' => array(
                        array(
                            'name' => 'Customer ID Number',
                            'mode' => 'text',
                            'mandatory' => 'true',
                        ),
                        array(
                            'name' => 'Amount',
                            'mode' => 'numeric',
                            'mandatory' => 'true',
                        ),
                        array(
                            'name' => 'Pin',
                            'mode' => 'numeric',
                            'hide_value' => 'true',
                            'mandatory' => 'true',
                        ),
                    ),
                )
        );

        $this->render_bml($this->bml);
    }

    public function make_payment_form($recipient, $redirectfunc, $direction) {
        $submit_url = $this->gen_in_app_url(
                'make_payment_confirm', array($recipient, $redirectfunc, $direction), 'Pages'
        );

        $bml_action = $this->bml->control->actions->addChild('action', $submit_url);
        $bml_action->addAttribute('key', 'action');
        $bml_action->addAttribute('linkType', 'o');

        $this->bml->page->gen_text_entry(
                array(
                    'title' => '',
                    'fields' => array(
                        array(
                            'name' => 'MSISDN / Wallet Number',
                            'mode' => 'numeric',
                            'mandatory' => 'true',
                        ),
                        array(
                            'name' => 'Amount',
                            'mode' => 'numeric',
                            'mandatory' => 'true',
                        ),
                        array(
                            'name' => 'Pin',
                            'mode' => 'numeric',
                            'hide_value' => 'true',
                            'mandatory' => 'true',
                        ),
                    ),
                )
        );

        $this->render_bml($this->bml);
    }

    public function send_airtime_form($network_op, $redirectfunc, $direction) {
        $submit_url = $this->gen_in_app_url(
                'send_airtime_confirm', array($network_op, $redirectfunc, $direction), 'Pages'
        );

        $bml_action = $this->bml->control->actions->addChild('action', $submit_url);
        $bml_action->addAttribute('key', 'action');
        $bml_action->addAttribute('linkType', 'o');

        $this->bml->page->gen_text_entry(
                array(
                    'title' => '',
                    'fields' => array(
                        array(
                            'name' => 'Phone Number',
                            'mode' => 'phone',
                            'mandatory' => 'true',
                        ),
                        array(
                            'name' => 'Amount',
                            'mode' => 'numeric',
                            'mandatory' => 'true',
                        ),
                        array(
                            'name' => 'Pin',
                            'mode' => 'numeric',
                            'hide_value' => 'true',
                            'mandatory' => 'true',
                        ),
                    ),
                )
        );

        $this->render_bml($this->bml);
    }

    public function sigin_in_page() {
        $submit_url = $this->gen_in_app_url(
                'login_to_mats_agent', array(), 'Pages'
        );

        $bml_action = $this->bml->control->actions->addChild('action', $submit_url);
        $bml_action->addAttribute('key', 'action');
        $bml_action->addAttribute('linkType', 'o');

        $this->bml->page->gen_text_entry(
                array(
                    'title' => '',
                    'fields' => array(
                        array(
                            'name' => 'Username',
                            'mode' => 'text',
                            'mandatory' => 'true',
                        ),
                        array(
                            'name' => 'Password',
                            'mode' => 'text',
                            'hide_value' => 'true',
                            'mandatory' => 'true',
                        ),
                    ),
                )
        );

        $this->render_bml($this->bml);
    }

    public function cashout_form($cashout_type) {
        $submit_url = $this->gen_in_app_url(
                'cashout_confirm', array($cashout_type), 'Pages'
        );

        $bml_action = $this->bml->control->actions->addChild('action', $submit_url);
        $bml_action->addAttribute('key', 'action');
        $bml_action->addAttribute('linkType', 'o');

        $this->bml->page->gen_text_entry(
                array(
                    'title' => '',
                    'fields' => array(
                        array(
                            'name' => 'Amount',
                            'mode' => 'numeric',
                            'mandatory' => 'true',
                        ),
                        array(
                            'name' => 'Pin',
                            'mode' => 'numeric',
                            'hide_value' => 'true',
                            'mandatory' => 'true',
                        ),
                    ),
                )
        );

        $this->render_bml($this->bml);
    }

    public function chnage_pin_form() {
        $submit_url = $this->gen_in_app_url(
                'pin_change_status', null, 'Pages'
        );

        $bml_action = $this->bml->control->actions->addChild('action', $submit_url);
        $bml_action->addAttribute('key', 'action');
        $bml_action->addAttribute('linkType', 'o');

        $this->bml->page->gen_text_entry(
                array(
                    'title' => '',
                    'fields' => array(
                        array(
                            'name' => 'Old Pin',
                            'mode' => 'numeric',
                            'hide_value' => 'true',
                            'mandatory' => 'true',
                        ),
                        array(
                            'name' => 'New Pin',
                            'mode' => 'numeric',
                            'mandatory' => 'true',
                        ),
                        array(
                            'name' => 'Re-enter Pin',
                            'mode' => 'numeric',
                            'mandatory' => 'true',
                        ),
                    ),
                )
        );

        $this->render_bml($this->bml);
    }

}
