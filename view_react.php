<?php
namespace PMVC\PlugIn\view;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\view_react';

const SEPARATOR = "\r\n\r\n";

/**
 * Parameters
 *
 * @parameters string reactData
 * @parameters string CSS
 * @parameters string jsFile      custom server.js path
 * @parameters bool   ttfb        ttfb runtime status
 * @see        https://github.com/pmvc-plugin/view/blob/master/src/ViewEngine.php
 */
class view_react extends ViewEngine
{
    private $_returnCode;
    private $_openProcId;
    public function init()
    {
        if (empty(\PMVC\getOption('disableTTFB'))) {
            /*For disable output buffer*/
            $this['headers'] = [
                'X-Accel-Buffering: no',
                // https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Encoding
                'Content-Encoding: identity',
            ];
        }
    }

    private function _killProc($proc = null, $pipes = null)
    {
        if (is_resource($proc)) {
            !empty($pipes) && fclose($pipes[1]);
            !empty($pipes) && fclose($pipes[2]);
            $this->_returnCode = proc_close($proc);
            is_resource($proc) && proc_terminate($proc, SIGSTOP);
        }
        if (isset($this->_openProcId)) {
            $sid = posix_getsid($this->_openProcId);
            if ($sid) {
                posix_kill($this->_openProcId, SIGTERM);
                posix_kill($this->_openProcId, SIGKILL);
            }
        }
    }

    public function __destruct()
    {
        $this->_killProc();
    }

    private function _shell($command, $input)
    {
        $proc = proc_open(
            $command,
            [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'a']],
            $pipes
        );
        if (is_resource($proc)) {
            ignore_user_abort(true);
            $procInfo = proc_get_status($proc);
            $this->_openProcId = $procInfo['pid'];
            fwrite($pipes[0], $input);
            fclose($pipes[0]);
            echo stream_get_line($pipes[1], 8192, SEPARATOR);
            $this['ssrcb'] = function () use ($proc, $pipes) {
                while (!feof($pipes[1])) {
                    echo stream_get_line($pipes[1], 8192, '');
                    if (connection_aborted()) {
                        $this->_killProc($proc, $pipes);
                        die();
                    }
                }
                $this->_killProc($proc, $pipes);
            };
        }
    }

    private function _process_ssr($data)
    {
        $data = $data ? json_encode($data) : '{}';
        $nodejs = \PMVC\plug('nodejs')->getNodeJs();

        // echo '{"themePath":"home"}' | node ./server.js
        $js = \PMVC\value(
            $this,
            ['jsFile'],
            $this['themeFolder'] . '/server.js'
        );
        $js = \PMVC\realPath($js);
        $cmd = $nodejs . ' ' . $js;
        \PMVC\dev(function () use ($cmd, $data) {
            $tmpFile = tempnam(sys_get_temp_dir(), 'react-data-');
            chmod($tmpFile, 0777);
            file_put_contents($tmpFile, $data);
            $s = 'cat ' . $tmpFile . ' | ' . $cmd;
            return $s;
        }, 'view');
        $this->_shell($cmd, $data);
    }

    private function _load($__f)
    {
        include $__f;
        $this->flush();
    }

    public function toJsonParse($s)
    {
        return $s
            ? 'JSON.parse(\'' .
                    str_replace(
                        ['\\'],
                        ['\\\\'],
                        json_encode($s, JSON_HEX_APOS | JSON_UNESCAPED_UNICODE)
                    ) .
                    '\')'
            : '{}';
    }

    /**
     * ssrHeader should call from template file (html).
     * so not set switch to disable or enable it.
     */
    public function ssrHeader()
    {
        $this['reactData'] = $this->get();
        $this->_process_ssr($this['reactData']);
        $this->flush();
    }

    /**
     * ssrBody should call from template file (html).
     * so not set switch to disable or enable it.
     */
    public function ssrBody()
    {
        $this['ssrcb']();
        $this->flush();
    }

    public function process()
    {
        $file = $this->getTplFile($this['themePath']);
        if ($file) {
            $this->_load($file);
        } else {
            trigger_error('Template fie was not found: [' . $file . ']');
        }
        if (!empty($this['run'])) {
            $this->clean();
        }
    }
}
