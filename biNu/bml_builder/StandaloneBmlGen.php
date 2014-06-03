<?php

require_once 'DocConfig.php';
require_once 'BmlElement.php';

/**
 * Class StandaloneBmlGen
 *
 * This class is used as a wrapper to call BmlElement code generation methods without
 * needing to instantiate a BmlDocument and create an entire BML XML tree.
 */
class StandaloneBmlGen
{
    protected $doc_conf;
    protected $bml_root;

    /**
     * Initialises a config in the same way as BmlDocument.
     *
     * @param array $cookies
     * @param string $cookie_domain Domain name to store cookies.
     * @param string $link_type_default
     */
    public function __construct(
        array $cookies, $cookie_domain, $link_type_default
    ) {
       $this->doc_conf = new DocConfig($cookies, $cookie_domain, $link_type_default);
    }

    /**
     * This method is the heart of the class. It allows any public method that
     * generates BML in the BmlElement class to be invoked in isolation. Raw,
     * formatted, XML is returned.
     *
     * @param string $name The method name.
     * @param string $arguments The method args.
     * @return string XML generate by the method invoked.
     */
    public function __call($name, $arguments)
    {
        $bml_root = new BmlElement('<root />');
        call_user_func_array(array($bml_root, $name), $arguments);
        $child_xml = '';
        foreach ($bml_root->children() as $child) {
            $child_xml .= $this->format($child->asXML());
        }
        return $child_xml;
    }

    /**
     * Format a snippet of XML code.
     *
     * @param string $xml
     * @return string
     */
    protected function format($xml)
    {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml);
        return $dom->saveXml($dom->documentElement);
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
