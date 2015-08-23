<?php
namespace PMVC\PlugIn\view;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\view_react';

class view_react extends ViewEngine
{
    private $node;
    public function init()
    {
        $this->node = \PMVC\plug('get')->get('NODE');
    }

    private function run()
    {
        if (empty($this->node)) {
            return false;
        }
        $get = escapeshellarg($this['react_data']);
        return shell_exec($this->node.' '.$this['themeDir'].'/server.js '.$get);
    }

    public function process()
    {
        if (!\PMVC\realpath($this['themeDir'])) {
            trigger_error('Template folder was not found: ['.$this['themeDir'].']');
            return;
        }
        $t = $this->initTemplateHelper($this['themeDir']);
        $file = $this->getTplFile($this['themePath']);
        if (empty($this['run'])) {
            $this->set('path',$this['themePath']);
            $this['react_data'] = json_encode($this->get());
            $run = trim($this->run());
            $separator = '<!--start-->';
            $separatorPos = strpos($run,$separator);
            $this['run_css'] = substr($run,0,$separatorPos);
            if ( !empty($this['run_css']) || 0===$separatorPos ) {
                $runStart =  strlen($this['run_css'].$separator);
                $this['run'] = substr($run,$runStart);
            } else {
                $this['run'] =& $run; 
            }
        }
        if (\PMVC\realpath($file)) {
            include($file);
        } else {
            trigger_error('Template fie was not found: ['.$file.']');
        }
        $this->clean();
    }
}
