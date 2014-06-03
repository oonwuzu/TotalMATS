<?php

require_once 'BmlDocument.php';

/**
 * Create a BML document that will display a form.
 */
class FormBmlDoc extends BmlDocument
{
    /**
     * Must arguments are the same as the regular BmlDocument constructor.
     * The important one is $fields which is an array whose keys are described in
     * the documentation for BmlElement::gen_form().
     *
     * @param int $ttl
     * @param int $developer_id
     * @param int $app_id
     * @param array $cookies
     * @param string $cookie_domain
     * @param array $fields
     * @param null|string $submit_url
     * @param string $title
     * @param string $link_type_default
     */
    public function __construct(
        $ttl, $developer_id, $app_id, array $cookies, $cookie_domain, array $fields,
        $submit_url, $title = '', $link_type_default = 'o'
    ) {
        $this->doc_conf = new DocConfig(
        $cookies, $cookie_domain, $link_type_default
        );

        $this->bml = new BmlElement('<binu />');
        $this->bml->addAttribute('ttl', $ttl);
        $this->bml->addAttribute('developer', $developer_id);
        $this->bml->addAttribute('app', $app_id);

        $this->bml->addChild('page');
        $bml_control = $this->bml->addChild('control');
        $bml_control->addAttribute('textUTF8', 'true');
        $bml_control->addChild('actions');

        $bml_action = $this->bml->control->actions->addChild('action', $submit_url);
        $bml_action->addAttribute('key', 'action');
        $bml_action->addAttribute('linkType', $link_type_default);

        $this->bml->page->gen_text_entry(
            array('title' => $title, 'fields' => $fields)
        );
    }
}
