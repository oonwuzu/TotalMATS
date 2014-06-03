<?php

require_once 'BmlDocument.php';

class GeneralTextBmlDoc extends BmlDocument
{
    /**
     * Create a new general text BML skeleton document.
     *
     * @param string $text The text to display in the main content.
     * @param string $text_style
     * @param callable $header_init_fn A function that takes a reference to the
     *      BmlDocument and a BmlElement representing the page, and generate a
     *      heading.
     * @param callable $footer_init_fn Like above, but it takes a BmlElement that
     *      represents the pageSegment to add the footer content to.
     */
    public function create_page(
        $text, $text_style, Closure $header_init_fn = null,
        Closure $footer_init_fn = null
    ) {
        // create a header if set
        if (!is_null($header_init_fn)) {
            call_user_func($header_init_fn, $this->bml->page);
        }

        $main_psegment = $this->bml->page->gen_psegment();
        $main_psegment->gen_text(array(
            'text' => $text,
            'style' => $text_style,
        ));

        // call a function to create any content below the text
        if (!is_null($footer_init_fn)) {
            call_user_func($footer_init_fn, $main_psegment);
        }
    }
}
