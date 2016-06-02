<?php
PMVC\Load::plug();
PMVC\addPlugInFolders(['../']);
class HtmlViewTest extends PHPUnit_Framework_TestCase
{
    function testHtmlView()
    {
        $html = PMVC\plug('view_react');
        ob_start();
        print_r($html);
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertContains('view_react',trim($output));
    }
}
