<?php
namespace PMVC\PlugIn\view;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\view_react';

const SEPARATOR = '<!--start-->';

/**
 * Parameters
 * @parameters string NODE node bin path
 * @parameters string reactData
 * @parameters string CSS 
 * @parameters string jsFile custom js path 
 * @see https://github.com/pmvc-plugin/view/blob/master/src/ViewEngine.php
 */
class view_react extends ViewEngine
{
    private $_returnCode;
    public function init()
    {
        $this['headers']=[
            'X-Accel-Buffering: no'
        ];
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
        // echo '{"themePath":"home"}' | node ./server.js
        $js = \PMVC\value($this, ['jsFile'], $this['themeFolder'].'/server.js');
        $cmd = $this['NODE'].' '.$js;
        \PMVC\dev(function() use($cmd) {
            $s = "echo '".$this['reactData']."' | ".$cmd;
            return $s;
        }, 'view');
        return $this->_shell($cmd,$this['reactData'],$this->_returnCode);
    }

    public function process()
    {
        $t = $this->initTemplateHelper();
        if (!isset($this['run'])) {
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
            $this->flush();
        } else {
            trigger_error(
                'Template fie was not found: ['.
                $file.
                ']'
            );
        }
        if (!empty($this['run'])) {
            $this->clean();
        }
    }
}
