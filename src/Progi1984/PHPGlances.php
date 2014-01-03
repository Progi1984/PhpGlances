<?php
namespace Progi1984;

class PhpGlances
{
    const VERSION = '0.2';

    private $url;
    private $port = 80;
    private $error = '';
    private $useCache = false;
    private $arrCache = array();

    private $oCurl;
    private $extPHPCurl;
    private $extPHPJson;
    private $extPHPXMLRPC;
    private $extPHPSimpleXML;

    public function __construct($psURL, $piPort)
    {
        $this->url             = $psURL;
        $this->port            = $piPort;

        $this->extPHPCurl      = extension_loaded('curl');
        $this->extPHPJson      = extension_loaded('json');
        $this->extPHPXMLRPC    = extension_loaded('xmlrpc');
        $this->extPHPSimpleXML = extension_loaded('simplexml');
        if ($this->extPHPCurl == true) {
            $this->oCurl = curl_init();
        }
    }
    
    public function __destruct()
    {
        if ($this->extPHPCurl == true) {
            if ($this->oCurl) {
                curl_close($this->oCurl);
            }
        }
    }

    /**
     * Replacement for "xmlrpc_encode_request"
     * @param $psString
     * @param array $parrArray
     * @return string
     * @author Progi1984
     */
    private function fnXmlRpcEncodeRequest($psString, array $parrArray)
    {
        if ($this->extPHPXMLRPC == true) {
            return xmlrpc_encode_request($psString, $parrArray);
        } else {
            $psReturn = '<?xml version="1.0" encoding="iso-8859-1"?>';
            $psReturn .= '<methodCall><methodName>'.$psString.'</methodName><params/></methodCall>';
            return $psReturn;
        }
    }

    /**
     * Replacement for "xmlrpc_decode"
     * @param $psString
     * @return array|mixed|string
     * @author Progi1984
     */
    private function fnXmlRpcDecode($psString)
    {
        if ($this->extPHPXMLRPC == true) {
            return xmlrpc_decode($psString);
        } else {
            if ($this->extPHPSimpleXML == true) {
                $oXML = simplexml_load_string($psString);
                // Array
                if (isset($oXML->params->param->value->array)) {
                    $arrReturn = array();
                    foreach ($oXML->params->param->value->array->data->value as $item) {
                        $arrReturn[] = (string)$item->string;
                    }
                    return $arrReturn;
                }
                // String
                elseif (isset($oXML->params->param->value->string)) {
                    return (string) $oXML->params->param->value->string;
                }
                // Error
                elseif (isset($oXML->fault->value->struct->member->name)) {
                    $arrReturn = array();
                    foreach ($oXML->fault->value->struct->member as $item) {
                        if (isset($item->name) && $item->name == 'faultCode') {
                            $arrReturn['faultCode'] = (int)$item->value->int;
                        }
                        if (isset($item->name) && $item->name == 'faultString') {
                            $arrReturn['faultString'] = (string)$item->value->string;
                        }
                    }
                    return $arrReturn;
                }
                return '';
            } else {
                $oXML = new DOMDocument();
                $oXML->loadXML($psString);
                $arrXML = $this->fnXmlConvert($oXML->documentElement);
                // Array
                if (isset($arrXML['params']['param']['value']['array'])) {
                    $arrReturn = array();
                    foreach ($arrXML['params']['param']['value']['array']['data']['value'] as $item) {
                        $arrReturn[] = (string)$item['string'];
                    }
                    return $arrReturn;
                }
                // String
                elseif (isset($arrXML['params']['param']['value']['string'])) {
                    return (string) $arrXML['params']['param']['value']['string'];
                }
                // Error
                elseif (isset($arrXML['fault']['value']['struct']['member']['name'])) {
                    $arrReturn = array();
                    foreach ($arrXML['fault']['value']['struct']['member'] as $item) {
                        if (isset($item['name']) && $item['name'] == 'faultCode') {
                            $arrReturn['faultCode'] = (int)$item['name']['int'];
                        }
                        if (isset($item['name']) && $item['name'] == 'faultString') {
                            $arrReturn['faultString'] = (string)$item['name']['string'];
                        }
                    }
                    return $arrReturn;
                }
            }
        }
    }

    /**
     * Support for PHP4 for XML
     * @param $node
     * @return array|string
     * @author Progi1984
     * @url http://www.lalit.org/wordpress/wp-content/uploads/2011/07/XML2Array.txt?ver=0.2
     */
    private function fnXmlConvert($node)
    {
        $output = array();
        switch ($node->nodeType) {
            case XML_CDATA_SECTION_NODE:
                $output['@cdata'] = trim($node->textContent);
                break;
            case XML_TEXT_NODE:
                $output = trim($node->textContent);
                break;
            case XML_ELEMENT_NODE:
                // for each child node, call the covert function recursively
                for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = $this->fn_xml_convert($child);
                    if (isset($child->tagName)) {
                        $t = $child->tagName;
                        // assume more nodes of same kind are coming
                        if (!isset($output[$t])) {
                            $output[$t] = array();
                        }
                        $output[$t][] = $v;
                    } else {
                        //check if it is not an empty text node
                        if ($v !== '') {
                            $output = $v;
                        }
                    }
                }
                if (is_array($output)) {
                    // if only one node of its kind, assign it directly instead if array($value);
                    foreach ($output as $t => $v) {
                        if (is_array($v) && count($v)==1) {
                            $output[$t] = $v[0];
                        }
                    }
                    if (empty($output)) {
                        //for empty nodes
                        $output = '';
                    }
                }
                // loop through the attributes and collect them
                if ($node->attributes->length) {
                    $a = array();
                    foreach ($node->attributes as $attrName => $attrNode) {
                        $a[$attrName] = (string) $attrNode->value;
                    }
                    // if its an leaf node, store the value in @value instead of directly storing it.
                    if (!is_array($output)) {
                        $output = array('@value' => $output);
                    }
                    $output['@attributes'] = $a;
                }
                break;
        }
        return $output;
    }

    /**
     * Replacement for "json_decode"
     * @param $psString
     * @return mixed|null
     * @author Progi1984
     * @url https://code.google.com/p/simplejson-php/source/browse/trunk/simplejson.php
     */
    private function fnJsonDecode($psString, $pbAssoc = false)
    {
        if ($this->extPHPJson == true) {
            return json_decode($psString, true);
        } else {
            // $matchString = '/(".*?(?<!\\\\)"|\'.*?(?<!\\\\)\')/';
            $matchString = '/".*?(?<!\\\\)"/';

            // safety / validity test
            $t = preg_replace( $matchString, '', $psString );
            $t = preg_replace( '/[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]/', '', $t );
            if ($t != '') { return null; }

            // build to/from hashes for all strings in the structure
            $s2m = array();
            $m2s = array();
            preg_match_all( $matchString, $psString, $m );
            foreach ($m[0] as $s) {
                $hash       = '"' . md5( $s ) . '"';
                $s2m[$s]    = $hash;
                $m2s[$hash] = str_replace( '$', '\$', $s );  // prevent $ magic
            }

            // hide the strings
            $psString = strtr( $psString, $s2m );

            // convert JS notation to PHP notation
            $a = ($pbAssoc) ? '' : '(object) ';
            $psString = strtr( $psString, array(
                ':' => '=>',
                '[' => 'array(',
                '{' => "{$a}array(",
                ']' => ')',
                '}' => ')'
                )
            );

            // remove leading zeros to prevent incorrect type casting
            $psString = preg_replace( '~([\s\(,>])(-?)0~', '$1$2', $psString );

            // return the strings
            $psString = strtr( $psString, $m2s );

            /* "eval" string and return results.
               As there is no try statement in PHP4, the trick here
                is to suppress any parser errors while a function is
                built and then run the function if it got made. */
            $f = @create_function( '', "return {$psString};" );
            $r = ($f) ? $f() : null;

            // free mem (shouldn't really be needed, but it's polite)
            unset( $s2m ); unset( $m2s ); unset( $f );

            return $r;
        }
    }

    private function _api($psMethod)
    {
        if ($this->extPHPCurl == true) {
            curl_setopt($this->oCurl, CURLOPT_HEADER, false);
            curl_setopt($this->oCurl, CURLOPT_URL, $this->url.'/RPC2');
            curl_setopt($this->oCurl, CURLOPT_PORT, $this->port);
            curl_setopt($this->oCurl, CURLOPT_POST, true);
            curl_setopt($this->oCurl, CURLOPT_HTTPHEADER, array('Content-Type' => 'text/xml'));
            curl_setopt($this->oCurl, CURLOPT_RETURNTRANSFER, true);
            $psContent = $this->fnXmlRpcEncodeRequest($psMethod, array());
            curl_setopt($this->oCurl, CURLOPT_POSTFIELDS, $psContent);
            $res = curl_exec($this->oCurl);
            if ($res === false) {
                trigger_error(__CLASS__.' > '.__METHOD__.'(l.'.__LINE__.') : '.curl_error($this->oCurl), E_USER_WARNING);
                return false;
            } else {
                return $this->fnXmlRpcDecode($res);
            }
        } else {
            $params = array(
                'http' => array(
                    'method' => 'POST',
                    'content' => $this->fnXmlRpcEncodeRequest($psMethod, array())
                )
            );
            $oCtx = stream_context_create($params);
            $oStream = @fopen($this->url.':'.$this->port.'/RPC2', 'rb', false, $oCtx);
            if (!$oStream) {
                if (isset($php_errormsg) && preg_match("/401/", $php_errormsg)) header("HTTP/1.1 401 Authentication failed");
                else header("HTTP/1.1 403 Forbidden");
                die();
            }
            $res = @stream_get_contents($oStream);
            fclose($oStream);
            if ($res === false || empty($res)) {
                $this->error = __CLASS__.' > '.__METHOD__.'(l.'.__LINE__.') : Problem reading data from '.$this->url.'/RPC2';
                return false;
            } else {
                $res = $this->fnXmlRpcDecode($res);
                if (isset($res['faultCode']) && $res['faultCode'] == 1) {
                    $this->error = $res['faultString'];
                    return false;
                }
                $this->error = '';
                return $res;
            }
        }
    }

    public function pingServer()
    {
        if ($this->extPHPCurl == true) {
            curl_setopt($this->oCurl, CURLOPT_HEADER, false);
            curl_setopt($this->oCurl, CURLOPT_URL, $this->url.'/RPC2');
            curl_setopt($this->oCurl, CURLOPT_PORT, $this->port);
            curl_setopt($this->oCurl, CURLOPT_POST, true);
            curl_setopt($this->oCurl, CURLOPT_HTTPHEADER, array('Content-Type' => 'text/xml'));
            curl_setopt($this->oCurl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($this->oCurl, CURLOPT_TIMEOUT, 5);
            $psContent = $this->fnXmlRpcEncodeRequest('init', array());
            curl_setopt($this->oCurl, CURLOPT_POSTFIELDS, $psContent);
            curl_exec($this->oCurl);
            $iHTTPCode = curl_getinfo($this->oCurl, CURLINFO_HTTP_CODE);
            if ($iHTTPCode>=200 && $iHTTPCode<300) {
                return true;
            } else {
                return false;
            }
        } else {
            $params = array(
                'http' => array(
                    'method' => 'POST',
                    'content' => $this->fnXmlRpcEncodeRequest('init', array())
                )
            );
            $oCtx = stream_context_create($params);
            $oStream = @fopen($this->url.':'.$this->port.'/RPC2', 'rb', false, $oCtx);
            if ($oStream) {
                $res = @stream_get_contents($oStream);
                fclose($oStream);
                if (!($res === false || empty($res))) {
                    return true;
                }
            }
            return false;
        }
    }

    /**
     * Return the intercepted error
     * @return string
     * @author Progi1984
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Enable or disable the cache
     * @param boolean $bUseCache
     * @return $this
     * @author Progi1984
     */
    public function setCacheStatus($bUseCache)
    {
        $this->useCache = $bUseCache;
        return $this;
    }

    /**
     * Get the cache status
     * @return bool
     * @author Progi1984
     */
    public function getCacheStatus()
    {
        return $this->useCache;
    }

    /**
     * @return array
     * @author Progi1984
     */
    public function listMethods()
    {
        return $this->_api('system.listMethods');
    }

    public function getCore()
    {
        return $this->_api('getCore');
    }

    private function getCpu()
    {
        if ($this->useCache) {
            if (!isset($this->arrCache['getCpu'])) {
                $this->arrCache['getCpu'] = $this->fnJsonDecode($this->_api('getCpu'), true);
            }
            return $this->arrCache['getCpu'];
        } else {
            return $this->fnJsonDecode($this->_api('getCpu'), true);
        }
    }
    public function cpu_getIOWait()
    {
        $res = $this->getCpu();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['iowait'])) {
                return $res['iowait'];
            } else {
                return 0;
            }
        }
    }
    public function cpu_getSystem()
    {
        $res = $this->getCpu();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['system'])) {
                return $res['system'];
            } else {
                return 0;
            }
        }
    }
    public function cpu_getIdle()
    {
        $res = $this->getCpu();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['idle'])) {
                return $res['idle'];
            } else {
                return 0;
            }
        }
    }
    public function cpu_getUser()
    {
        $res = $this->getCpu();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['user'])) {
                return $res['user'];
            } else {
                return 0;
            }
        }
    }
    public function cpu_getIRQ()
    {
        $res = $this->getCpu();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['irq'])) {
                return $res['irq'];
            } else {
                return 0;
            }
        }
    }
    public function cpu_getNice()
    {
        $res = $this->getCpu();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['nice'])) {
                return $res['nice'];
            } else {
                return 0;
            }
        }
    }

    private function getDiskIO()
    {
        if ($this->useCache) {
            if (!isset($this->arrCache['getDiskIO'])) {
                $this->arrCachearrCache['getDiskIO'] = $this->fnJsonDecode($this->_api('getDiskIO'), true);
            }
            return $this->arrCache['getDiskIO'];
        } else {
            return $this->fnJsonDecode($this->_api('getDiskIO'), true);
        }
    }
    public function diskIO_getCount()
    {
        $res = $this->getDiskIO();
        if ($res === false) {
            return false;
        } else {
            return count($res);
        }
    }
    public function diskIO_getDiskName($piIdx)
    {
        $res = $this->getDiskIO();
        if ($res === false) {
            return false;
        } else {
            if (isset($res[$piIdx]['disk_name'])) {
                return $res[$piIdx]['disk_name'];
            } else {
               return '';
            }
        }
    }
    public function diskIO_getReadBytes($piIdx)
    {
        $res = $this->getDiskIO();
        if ($res === false) {
            return false;
        } else {
            if (isset($res[$piIdx]['read_bytes'])) {
                return $res[$piIdx]['read_bytes'];
            } else {
                return '';
            }
        }
    }
    public function diskIO_getWriteBytes($piIdx)
    {
        $res = $this->getDiskIO();
        if ($res === false) {
            return false;
        } else {
            if (isset($res[$piIdx]['write_bytes'])) {
                return $res[$piIdx]['write_bytes'];
            } else {
                return '';
            }
        }
    }

    private function getFs()
    {
        if ($this->useCache) {
            if (!isset($this->arrCache['getFs'])) {
                $this->arrCache['getFs'] = $this->fnJsonDecode($this->_api('getFs'), true);
            }
            return $this->arrCache['getFs'];
        } else {
            return $this->fnJsonDecode($this->_api('getFs'), true);
        }
    }
    public function fs_getCount()
    {
        $res = $this->getFs();
        if ($res === false) {
            return false;
        } else {
            return count($res);
        }
    }
    public function fs_getMountPoint($piIdx)
    {
        $res = $this->getFs();
        if ($res === false) {
            return false;
        } else {
            if (isset($res[$piIdx]['mnt_point'])) {
                return $res[$piIdx]['mnt_point'];
            } else {
                return '';
            }
        }
    }
    public function fs_getDeviceName($piIdx)
    {
        $res = $this->getFs();
        if ($res === false) {
            return false;
        } else {
            if (isset($res[$piIdx]['device_name'])) {
                return $res[$piIdx]['device_name'];
            } else {
                return '';
            }
        }
    }
    public function fs_getFileSystemType($piIdx)
    {
        $res = $this->getFs();
        if ($res === false) {
            return false;
        } else {
            if (isset($res[$piIdx]['fs_type'])) {
                return $res[$piIdx]['fs_type'];
            } else {
                return '';
            }
        }
    }
    public function fs_getUsed($piIdx)
    {
        $res = $this->getFs();
        if ($res === false) {
            return false;
        } else {
            if (isset($res[$piIdx]['used'])) {
                return $res[$piIdx]['used'];
            } else {
                return 0;
            }
        }
    }
    public function fs_getAvailable($piIdx)
    {
        $res = $this->getFs();
        if ($res === false) {
            return false;
        } else {
            if (isset($res[$piIdx]['avail'])) {
                return $res[$piIdx]['avail'];
            } else {
                return 0;
            }
        }
    }
    public function fs_getSize($piIdx)
    {
        $res = $this->getFs();
        if ($res === false) {
            return false;
        } else {
            if (isset($res[$piIdx]['size'])) {
                return $res[$piIdx]['size'];
            } else {
                return 0;
            }
        }
    }

    private function getLoad()
    {
        if ($this->useCache) {
            if (!isset($this->arrCache['getLoad'])) {
                $this->arrCache['getLoad'] = $this->fnJsonDecode($this->_api('getLoad'), true);
            }
            return $this->arrCache['getLoad'];
        } else {
            return $this->fnJsonDecode($this->_api('getLoad'), true);
        }
    }
    public function load_getMin1()
    {
        $res = $this->getLoad();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['min1'])) {
                return $res['min1'];
            } else {
                return 0;
            }
        }
    }
    public function load_getMin5()
    {
        $res = $this->getLoad();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['min5'])) {
                return $res['min5'];
            } else {
                return 0;
            }
        }
    }
    public function load_getMin15()
    {
        $res = $this->getLoad();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['min15'])) {
                return $res['min15'];
            } else {
                return 0;
            }
        }
    }

    private function getLimits()
    {
        if ($this->useCache) {
            if (!isset($this->arrCache['getAllLimits'])) {
                $this->arrCache['getAllLimits'] = $this->fnJsonDecode($this->_api('getAllLimits'), true);
            }
            return $this->arrCache['getAllLimits'];
        } else {
            return $this->fnJsonDecode($this->_api('getAllLimits'), true);
        }
    }
    public function limit_getSTD()
    {
        $res = $this->getLimits();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['STD'])) {
                return $res['STD'];
            } else {
                return 0;
            }
        }
    }
    public function limit_getCPU_IOWait()
    {
        $res = $this->getLimits();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['CPU_IOWAIT'])) {
                return $res['CPU_IOWAIT'];
            } else {
                return 0;
            }
      }
    }
    public function limit_getFS()
    {
        $res = $this->getLimits();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['FS'])) {
                return $res['FS'];
            } else {
                return 0;
            }
        }
    }
    public function limit_getLoad()
    {
        $res = $this->getLimits();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['LOAD'])) {
                return $res['LOAD'];
            } else {
                return 0;
            }
        }
    }
    public function limit_getCPUSystem()
    {
        $res = $this->getLimits();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['CPU_SYSTEM'])) {
                return $res['CPU_SYSTEM'];
            } else {
                return 0;
            }
        }
    }
    public function limit_getProcessMem()
    {
        $res = $this->getLimits();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['PROCESS_MEM'])) {
                return $res['PROCESS_MEM'];
            } else {
                return 0;
            }
        }
    }
    public function limit_getTemp()
    {
        $res = $this->getLimits();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['TEMP'])) {
                return $res['TEMP'];
            } else {
                return 0;
            }
        }
    }
    public function limit_getMem()
    {
        $res = $this->getLimits();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['MEM'])) {
                return $res['MEM'];
            } else {
                return 0;
            }
        }
    }
    public function limit_getCPUUser()
    {
        $res = $this->getLimits();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['CPU_USER'])) {
                return $res['CPU_USER'];
            } else {
                return 0;
            }
        }
    }
    public function limit_getProcessCPU()
    {
        $res = $this->getLimits();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['PROCESS_CPU'])) {
                return $res['PROCESS_CPU'];
            } else {
                return 0;
            }
        }
    }
    public function limit_getSWAP()
    {
        $res = $this->getLimits();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['SWAP'])) {
                return $res['SWAP'];
            } else {
                return 0;
            }
        }
    }

    private function getMem()
    {
        if ($this->useCache) {
            if (!isset($this->arrCache['getMem'])) {
                $this->arrCache['getMem'] = $this->fnJsonDecode($this->_api('getMem'), true);
            }
            return $this->arrCache['getMem'];
        } else {
            return $this->fnJsonDecode($this->_api('getMem'), true);
        }
    }
    public function mem_getInactive()
    {
        $res = $this->getMem();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['inactive'])) {
                return $res['inactive'];
            } else {
                return 0;
            }
        }
    }
    public function mem_getCached()
    {
        $res = $this->getMem();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['cached'])) {
                return $res['cached'];
            } else {
                return 0;
            }
        }
    }
    public function mem_getUsed()
    {
        $res = $this->getMem();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['used'])) {
                return $res['used'];
            } else {
                return 0;
            }
        }
    }
    public function mem_getBuffers()
    {
        $res = $this->getMem();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['buffers'])) {
                return $res['buffers'];
            } else {
                return 0;
            }
        }
    }
    public function mem_getActive()
    {
        $res = $this->getMem();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['active'])) {
                return $res['active'];
            } else {
                return 0;
            }
        }
    }
    public function mem_getTotal()
    {
        $res = $this->getMem();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['total'])) {
                return $res['total'];
            } else {
                return 0;
            }
        }
    }
    public function mem_getPercent()
    {
        $res = $this->getMem();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['percent'])) {
                return $res['percent'];
            } else {
                return 0;
            }
        }
    }
    public function mem_getFree()
    {
        $res = $this->getMem();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['free'])) {
                return $res['free'];
            } else {
                return 0;
            }
        }
    }

    private function getMemSwap()
    {
        if ($this->useCache) {
            if (!isset($this->arrCache['getMemSwap'])) {
                $this->arrCache['getMemSwap'] = $this->fnJsonDecode($this->_api('getMemSwap'), true);
            }
            return $this->arrCache['getMemSwap'];
        } else {
            return $this->fnJsonDecode($this->_api('getMemSwap'), true);
        }
    }
    public function memswap_getTotal()
    {
        $res = $this->getMemSwap();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['total'])) {
                return $res['total'];
            } else {
                return 0;
            }
        }
    }
    public function memswap_getPercent()
    {
        $res = $this->getMemSwap();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['percent'])) {
                return $res['percent'];
            } else {
                return 0;
            }
        }
    }
    public function memswap_getUsed()
    {
        $res = $this->getMemSwap();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['used'])) {
                return $res['used'];
            } else {
                return 0;
            }
        }
    }

    private function getNetwork()
    {
        if ($this->useCache) {
            if (!isset($this->arrCache['getNetwork'])) {
                $this->arrCache['getNetwork'] = $this->fnJsonDecode($this->_api('getNetwork'), true);
            }
            return $this->arrCache['getNetwork'];
        } else {
            return $this->fnJsonDecode($this->_api('getNetwork'), true);
        }
    }
    public function network_getCount()
    {
        $res = $this->getNetwork();
        if ($res === false) {
            return false;
        } else {
            return count($res);
        }
    }
    public function network_getInterfaceName($piIdx)
    {
        $res = $this->getNetwork();
        if ($res === false) {
            return false;
        } else {
            if (isset($res[$piIdx]['interface_name'])) {
                return $res[$piIdx]['interface_name'];
            } else {
                return '';
            }
        }
    }
    public function network_getRX($piIdx)
    {
        $res = $this->getNetwork();
        if ($res === false) {
            return false;
        } else {
            if (isset($res[$piIdx]['rx'])) {
                return $res[$piIdx]['rx'];
            } else {
                return '';
            }
        }
    }
    public function network_getTX($piIdx)
    {
        $res = $this->getNetwork();
        if ($res === false) {
            return false;
        } else {
            if (isset($res[$piIdx]['tx'])) {
                return $res[$piIdx]['tx'];
            } else {
                return '';
            }
        }
    }

    public function getNow()
    {
        return $this->_api('getNow');
    }

    private function getProcessCount()
    {
        if ($this->useCache) {
            if (!isset($this->arrCache['getProcessCount'])) {
                $this->arrCache['getProcessCount'] = $this->fnJsonDecode($this->_api('getProcessCount'), true);
            }
            return $this->arrCache['getProcessCount'];
        } else {
            return $this->fnJsonDecode($this->_api('getProcessCount'), true);
        }
    }
    public function processcount_getZombie()
    {
        $res = $this->getProcessCount();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['zombie'])) {
                return $res['zombie'];
            } else {
                return 0;
            }
        }
    }
    public function processcount_getRunning()
    {
        $res = $this->getProcessCount();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['running'])) {
                return $res['running'];
            } else {
                return 0;
            }
        }
    }
    public function processcount_getTotal()
    {
        $res = $this->getProcessCount();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['total'])) {
                return $res['total'];
            } else {
                return 0;
            }
        }
    }
    public function processcount_getSleeping()
    {
        $res = $this->getProcessCount();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['sleeping'])) {
                return $res['sleeping'];
            } else {
                return 0;
            }
        }
    }

    private function getProcessList()
    {
        if ($this->useCache) {
            if (!isset($this->arrCache['getProcessList'])) {
                $this->arrCache['getProcessList'] = $this->fnJsonDecode($this->_api('getProcessList'), true);
            }
            return $this->arrCache['getProcessList'];
        } else {
            return $this->fnJsonDecode($this->_api('getProcessList'), true);
        }
    }
    public function processlist_getCount()
    {
        $res = $this->getProcessList();
        if ($res === false) {
            return false;
        } else {
            return count($res);
        }
    }
    public function processlist_getUserName($piIdx)
    {
        $res = $this->getProcessList();
        if ($res === false) {
            return false;
        } else {
            if (isset($res[$piIdx]['username'])) {
                return $res[$piIdx]['username'];
            } else {
                return '';
            }
        }
    }
    public function processlist_getStatus($piIdx)
    {
        $res = $this->getProcessList();
        if ($res === false) {
            return false;
        } else {
            if (isset($res[$piIdx]['status'])) {
                return $res[$piIdx]['status'];
            } else {
                return '';
            }
        }
    }
    public function processlist_getCpuTimes($piIdx)
    {
        $res = $this->getProcessList();
        if ($res === false) {
            return false;
        } else {
            if (isset($res[$piIdx]['cpu_times'])) {
                return $res[$piIdx]['cpu_times'];
            } else {
                return array();
            }
        }
    }
    public function processlist_getName($piIdx)
    {
        $res = $this->getProcessList();
        if ($res === false) {
            return false;
        } else {
            if (isset($res[$piIdx]['name'])) {
                return $res[$piIdx]['name'];
            } else {
                return '';
            }
        }
    }
    public function processlist_getMemoryPercent($piIdx)
    {
        $res = $this->getProcessList();
        if ($res === false) {
            return false;
        } else {
            if (isset($res[$piIdx]['memory_percent'])) {
                return $res[$piIdx]['memory_percent'];
            } else {
                return 0;
            }
        }
    }
    public function processlist_getCpuPercent($piIdx)
    {
        $res = $this->getProcessList();
        if ($res === false) {
            return false;
        } else {
            if (isset($res[$piIdx]['cpu_percent'])) {
                return $res[$piIdx]['cpu_percent'];
            } else {
                return 0;
            }
        }
    }
    public function processlist_getPid($piIdx)
    {
        $res = $this->getProcessList();
        if ($res === false) {
            return false;
        } else {
            if (isset($res[$piIdx]['pid'])) {
                return $res[$piIdx]['pid'];
            } else {
                return 0;
            }
        }
    }
    public function processlist_getIOCounters($piIdx)
    {
        $res = $this->getProcessList();
        if ($res === false) {
            return false;
        } else {
            if (isset($res[$piIdx]['io_counters'])) {
                return $res[$piIdx]['io_counters'];
            } else {
                return array();
            }
        }
    }
    public function processlist_getCommandLine($piIdx)
    {
        $res = $this->getProcessList();
        if ($res === false) {
            return false;
        } else {
            if (isset($res[$piIdx]['cmdline'])) {
                return $res[$piIdx]['cmdline'];
            } else {
                return '';
            }
        }
    }
    public function processlist_getMemoryInfo($piIdx)
    {
        $res = $this->getProcessList();
        if ($res === false) {
            return false;
        } else {
            if (isset($res[$piIdx]['memory_info'])) {
                return $res[$piIdx]['memory_info'];
            } else {
                return array();
            }
        }
    }
    public function processlist_getNice($piIdx)
    {
        $res = $this->getProcessList();
        if ($res === false) {
            return false;
        } else {
            if (isset($res[$piIdx]['nice'])) {
                return $res[$piIdx]['nice'];
            } else {
                return 0;
            }
        }
    }

    private function getSensors()
    {
        if ($this->useCache) {
            if (!isset($this->arrCache['getSensors'])) {
                $this->arrCache['getSensors'] = $this->fnJsonDecode($this->_api('getSensors'), true);
            }
            return $this->arrCache['getSensors'];
        } else {
            return $this->fnJsonDecode($this->_api('getSensors'), true);
        }
    }
    public function sensors_getCount()
    {
        $res = $this->getSensors();
        if ($res === false) {
            return false;
        } else {
            return count($res);
        }
    }
    public function sensors_getValue($piIdx)
    {
        $res = $this->getSensors();
        if ($res === false) {
            return false;
        } else {
            if (isset($res[$piIdx]['value'])) {
                return $res[$piIdx]['value'];
            } else {
                return '';
            }
        }
    }
    public function sensors_getLabel($piIdx)
    {
        $res = $this->getSensors();
        if ($res === false) {
            return false;
        } else {
            if (isset($res[$piIdx]['label'])) {
                return $res[$piIdx]['label'];
            } else {
                return '';
            }
        }
    }

    private function getSystem()
    {
        if ($this->useCache) {
            if (!isset($this->arrCache['getSystem'])) {
                $this->arrCache['getSystem'] = $this->fnJsonDecode($this->_api('getSystem'), true);
            }
            return $this->arrCache['getSystem'];
        } else {
            return $this->fnJsonDecode($this->_api('getSystem'), true);
        }
    }
    public function system_getLinuxDistro()
    {
        $res = $this->getSystem();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['linux_distro'])) {
                return $res['linux_distro'];
            } else {
                return '';
            }
        }
    }
    public function system_getPlatform()
    {
        $res = $this->getSystem();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['platform'])) {
                return $res['platform'];
            } else {
                return '';
            }
        }
    }
    public function system_getOSName()
    {
        $res = $this->getSystem();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['os_name'])) {
                return $res['os_name'];
            } else {
                return '';
            }
        }
    }
    public function system_getHostname()
    {
        $res = $this->getSystem();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['hostname'])) {
                return $res['hostname'];
            } else {
                return '';
            }
        }
    }
    public function system_getOSVersion()
    {
        $res = $this->getSystem();
        if ($res === false) {
            return false;
        } else {
            if (isset($res['os_version'])) {
                return $res['os_version'];
            } else {
                return '';
            }
        }
    }
}
