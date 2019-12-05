<?php
namespace PMVC\PlugIn\view;

use DomainException; 

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\view_react';

const SEPARATOR = '<!--start-->';

/**
 * Parameters
 * @parameters string NODEJS      node bin path
 * @parameters string reactData
 * @parameters string CSS 
 * @parameters string jsFile      custom server.js path 
 * @parameters bool   ttfb        ttfb runtime status 
 * @see https://github.com/pmvc-plugin/view/blob/master/src/ViewEngine.php
 */
class view_react extends ViewEngine
{
    private $_returnCode;
    public function init()
    {
        if (empty(\PMVC\getOption('disableTTFB'))) {
            /*For disable output buffer*/
            $this['headers']=[
                'X-Accel-Buffering: no',
                // https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Encoding
                'Content-Encoding: identity',
            ];
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
        $nodejs = \PMVC\realpath($this['NODEJS']);
        if (empty($nodejs)) {
            throw new DomainException('NodeJs path was missing. ['. $this['NODEJS']. ']');
        }
        // echo '{"themePath":"home"}' | node ./server.js
        $js = \PMVC\value($this, ['jsFile'], $this['themeFolder'].'/server.js');
        $js = \PMVC\realPath($js);
        $cmd = $nodejs.' '.$js;
        \PMVC\dev(function() use($cmd) {
            $tmpFile = tempnam(sys_get_temp_dir(), 'react-data-');
            chmod($tmpFile, 0777);
            file_put_contents($tmpFile, $this['reactData']);
            $s = 'cat '.$tmpFile.' | '.$cmd;
            return $s;
        }, 'view');
        return $this->_shell($cmd,$this['reactData'],$this->_returnCode);
    }

    private function _load($__f)
    {
        include($__f);
        $this->flush();
    }

    public function toJson($s) {
      return $s ? str_replace('\u0022', '\\\u0022', json_encode($s, JSON_HEX_APOS|JSON_HEX_QUOT)) : '{}';
    }

    public function process()
    {
        if (!isset($this['run'])) {
            $this['reactData'] = $this->toJson($this->get());
            if (empty($this['reactData'])) {
                $this['reactData'] = '{}';
            }
            $run = trim($this->_run());
            \PMVC\dev(function() use($run) {
                return $run;
            }, 'view');
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
            $this->_load($file);
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
