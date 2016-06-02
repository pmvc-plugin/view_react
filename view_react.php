<?php
namespace PMVC\PlugIn\view;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\view_react';

class view_react extends ViewEngine
{
    private $_node;
    private $_returnCode;
    public function init()
    {
        $this->_node = \PMVC\plug('get')->get('NODE');
    }

    private function _shell($command, $input, &$returnCode)
    {
        $proc = proc_open($command, [ 
            ['pipe','r'],
            ['pipe','w'],
            ['pipe','a']
        ], $pipes);
        $result = null;
        if (is_resource($proc)) {
            fwrite($pipes[0],$input);
            fclose($pipes[0]);
            $result = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            $returnCode = proc_close($proc);
        }
        return $result;
    }

    private function _run()
    {
        if (empty($this->_node)) {
            return false;
        }
        // node ../themes/react_case/server.js '{"path":"home"}'
        $cmd = $this->_node.' '.$this['themeFolder'].'/server.js';
        return $this->_shell($cmd,$this['react_data'],$this->_returnCode);
    }

    public function process()
    {
        $t = $this->initTemplateHelper();
        if (!isset($this['run'])) {
            $headFile = $this->getTplFile('head', false);
            if (\PMVC\realpath($headFile)) {
                include($headFile);
                flush();
            }
            $this['react_data'] = json_encode($this->get());
            $run = trim($this->_run());
            $separator = '<!--start-->';
            $separatorPos = strpos($run,$separator);
            $this['run_css'] = substr($run,0,$separatorPos);
            if ( !empty($this['run_css']) || 0===$separatorPos ) {
                $runStart =  strlen($this['run_css'].$separator);
                $this['run'] = substr($run,$runStart);
            } else {
                $this['run'] = $run; 
            }
        }
        $file = $this->getTplFile($this['themePath']);
        if (\PMVC\realpath($file)) {
            include($file);
        } else {
            trigger_error('Template fie was not found: ['.$file.']');
        }
        $this->clean();
    }
}
