<?php

namespace PMVC\PlugIn\view;

use PMVC\TestCase;

class ReactViewTest extends TestCase
{
    function testHtmlView()
    {
        $html = \PMVC\plug('view_react');
        ob_start();
        print_r($html);
        $output = ob_get_contents();
        ob_end_clean();
        $this->haveString('view_react',trim($output));
    }
}
