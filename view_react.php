<?php
namespace PMVC\PlugIn\view;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\view_react';

const SEPARATOR = '<!--start-->';

/**
 * @parameters string NODE node bin path
 * @parameters string themeFolder
 * @parameters string themePath
 * @parameters string reactData
 * @parameters string CSS 
 */
class view_react extends ViewEngine
{
    private $_returnCode;
    public function init()
    {
        if (!isset($this['NODE'])) {
            $this['NODE'] = \PMVC\plug('get')->get('NODE');
        }
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
        if (empty($this['NODE'])) {
            return false;
        }
        // node ../themes/react_case/server.js '{"path":"home"}'
        $cmd = $this['NODE'].' '.$this['themeFolder'].'/server.js';
        return $this->_shell($cmd,$this['reactData'],$this->_returnCode);
    }

    public function process()
    {
        $t = $this->initTemplateHelper();
        if (!isset($this['run'])) {
            $headFile = $this->getTplFile('head', false);
            if ($headFile) {
                include($headFile);
                flush();
            }
            $this['reactData'] = json_encode($this->get());
            $run = trim($this->_run());
            $separatorPos = strpos($run, SEPARATOR);
            $this['CSS'] = substr($run,0,$separatorPos);
            if ( !empty($this['CSS']) || 0===$separatorPos ) {
                $runStart =  strlen($this['CSS'].SEPARATOR);
                $this['run'] = substr($run,$runStart);
            } else {
                $this['run'] = $run; 
            }
        }
        $file = $this->getTplFile($this['themePath']);
        if ($file) {
            include($file);
        } else {
            trigger_error('Template fie was not found: ['.$file.']');
        }
        $this->clean();
    }
}
