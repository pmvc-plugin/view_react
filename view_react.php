<?php
namespace PMVC\PlugIn\view;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\view_react';

class view_react extends ViewEngine
{
    private $node;
    public function init()
    {
        $this->node = getenv('NODE');
    }

    private function run()
    {
        return shell_exec($this->node.' '.$this->folder.'/server.js');
    }

    public function process()
    {
        $t = $this->initTemplateHelper($this->folder);
        $file = $this->getTplFile($this->path);
        $this->set('run', $this->run());
        include($file);
    }
}
