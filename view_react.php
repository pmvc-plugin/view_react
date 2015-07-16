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
        if (empty($this->node)) {
            return false;
        }
        $get = escapeshellarg($this['react_data']);
        return shell_exec($this->node.' '.$this->folder.'/server.js '.$get);
    }

    public function process()
    {
        $t = $this->initTemplateHelper($this->folder);
        $file = $this->getTplFile($this->path);
        if (empty($this['run'])) {
            $this['react_data'] = json_encode($this->get());
            $this['run'] = trim($this->run());
        }
        include($file);
        $this->clean();
    }
}
