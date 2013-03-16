<?php

  class PHPGlances{
    private $_url;
    private $_port;
    private $_oCurl;

    public function __construct($psURL, $piPort){
      $this->_url = $psURL;
      $this->_port = $piPort;

      $this->_oCurl = curl_init();
    }
    public function __destruct(){
      if($this->_oCurl){
        curl_close($this->_oCurl);
      }
    }

    private function _api($psMethod){
      curl_setopt($this->_oCurl, CURLOPT_HEADER, false);
      curl_setopt($this->_oCurl, CURLOPT_URL, $this->_url.'/RPC2');
      curl_setopt($this->_oCurl, CURLOPT_PORT, $this->_port);
      curl_setopt($this->_oCurl, CURLOPT_POST, true);
      curl_setopt($this->_oCurl, CURLOPT_HTTPHEADER, array('Content-Type' => 'text/xml'));
      curl_setopt($this->_oCurl, CURLOPT_RETURNTRANSFER, true);
      $psContent = xmlrpc_encode_request($psMethod, array());
      curl_setopt($this->_oCurl, CURLOPT_POSTFIELDS, $psContent);
      $res = curl_exec($this->_oCurl);
      if($res === false){
        trigger_error('PHPGlances > CurlError : '.curl_error($this->_oCurl),E_USER_WARNING);
        return false;
      } else {
        return xmlrpc_decode($res);
      }
    }

    public function listMethods(){
      return $this->_api('system.listMethods');
    }

    public function getCore(){
      return $this->_api('getCore');
    }

    private function getCpu(){
      return json_decode($this->_api('getCpu'), true);
    }
    public function cpu_getIOWait(){
      $res = $this->getCpu();
      if($res === false){
        return false;
      } else {
        if(isset($res['iowait'])){
          return $res['iowait'];
        } else {
          return 0;
        }
      }
    }
    public function cpu_getSystem(){
      $res = $this->getCpu();
      if($res === false){
        return false;
      } else {
        if(isset($res['system'])){
          return $res['system'];
        } else {
          return 0;
        }
      }
    }
    public function cpu_getIdle(){
      $res = $this->getCpu();
      if($res === false){
        return false;
      } else {
        if(isset($res['idle'])){
          return $res['idle'];
        } else {
          return 0;
        }
      }
    }
    public function cpu_getUser(){
      $res = $this->getCpu();
      if($res === false){
        return false;
      } else {
        if(isset($res['user'])){
          return $res['user'];
        } else {
          return 0;
        }
      }
    }
    public function cpu_getIRQ(){
      $res = $this->getCpu();
      if($res === false){
        return false;
      } else {
        if(isset($res['irq'])){
          return $res['irq'];
        } else {
          return 0;
        }
      }
    }
    public function cpu_getNice(){
      $res = $this->getCpu();
      if($res === false){
        return false;
      } else {
        if(isset($res['nice'])){
          return $res['nice'];
        } else {
          return 0;
        }
      }
    }

    private function getDiskIO(){
      return json_decode($this->_api('getDiskIO'), true);
    }
    public function diskIO_getCount(){
      $res = $this->getDiskIO();
      if($res === false){
        return false;
      } else {
        return count($res);
      }
    }
    public function diskIO_getDiskName($piIdx){
      $res = $this->getDiskIO();
      if($res === false){
        return false;
      } else {
        if(isset($res[$piIdx]['disk_name'])){
          return $res[$piIdx]['disk_name'];
        } else {
          return '';
        }
      }
    }
    public function diskIO_getReadBytes($piIdx){
      $res = $this->getDiskIO();
      if($res === false){
        return false;
      } else {
        if(isset($res[$piIdx]['read_bytes'])){
          return $res[$piIdx]['read_bytes'];
        } else {
          return '';
        }
      }
    }
    public function diskIO_getWriteBytes($piIdx){
      $res = $this->getDiskIO();
      if($res === false){
        return false;
      } else {
        if(isset($res[$piIdx]['write_bytes'])){
          return $res[$piIdx]['write_bytes'];
        } else {
          return '';
        }
      }
    }

    private function getFs(){
      return json_decode($this->_api('getFs'), true);
    }
    public function fs_getCount(){
      $res = $this->getFs();
      if($res === false){
        return false;
      } else {
        return count($res);
      }
    }
    public function fs_getMountPoint($piIdx){
      $res = $this->getFs();
      if($res === false){
        return false;
      } else {
        if(isset($res[$piIdx]['mnt_point'])){
          return $res[$piIdx]['mnt_point'];
        } else {
          return '';
        }
      }
    }
    public function fs_getDeviceName($piIdx){
      $res = $this->getFs();
      if($res === false){
        return false;
      } else {
        if(isset($res[$piIdx]['device_name'])){
          return $res[$piIdx]['device_name'];
        } else {
          return '';
        }
      }
    }
    public function fs_getFileSystemType($piIdx){
      $res = $this->getFs();
      if($res === false){
        return false;
      } else {
        if(isset($res[$piIdx]['fs_type'])){
          return $res[$piIdx]['fs_type'];
        } else {
          return '';
        }
      }
    }
    public function fs_getUsed($piIdx){
      $res = $this->getFs();
      if($res === false){
        return false;
      } else {
        if(isset($res[$piIdx]['used'])){
          return $res[$piIdx]['used'];
        } else {
          return 0;
        }
      }
    }
    public function fs_getAvailable($piIdx){
      $res = $this->getFs();
      if($res === false){
        return false;
      } else {
        if(isset($res[$piIdx]['avail'])){
          return $res[$piIdx]['avail'];
        } else {
          return 0;
        }
      }
    }
    public function fs_getSize($piIdx){
      $res = $this->getFs();
      if($res === false){
        return false;
      } else {
        if(isset($res[$piIdx]['size'])){
          return $res[$piIdx]['size'];
        } else {
          return 0;
        }
      }
    }

    private function getLoad(){
      return json_decode($this->_api('getLoad'), true);
    }
    public function load_getMin1(){
      $res = $this->getLoad();
      if($res === false){
        return false;
      } else {
        if(isset($res['min1'])){
          return $res['min1'];
        } else {
          return 0;
        }
      }
    }
    public function load_getMin5(){
      $res = $this->getLoad();
      if($res === false){
        return false;
      } else {
        if(isset($res['min5'])){
          return $res['min5'];
        } else {
          return 0;
        }
      }
    }
    public function load_getMin15(){
      $res = $this->getLoad();
      if($res === false){
        return false;
      } else {
        if(isset($res['min15'])){
          return $res['min15'];
        } else {
          return 0;
        }
      }
    }

    private function getMem(){
      return json_decode($this->_api('getMem'), true);
    }
    public function mem_getInactive(){
      $res = $this->getMem();
      if($res === false){
        return false;
      } else {
        if(isset($res['inactive'])){
          return $res['inactive'];
        } else {
          return 0;
        }
      }
    }
    public function mem_getCached(){
      $res = $this->getMem();
      if($res === false){
        return false;
      } else {
        if(isset($res['cached'])){
          return $res['cached'];
        } else {
          return 0;
        }
      }
    }
    public function mem_getUsed(){
      $res = $this->getMem();
      if($res === false){
        return false;
      } else {
        if(isset($res['used'])){
          return $res['used'];
        } else {
          return 0;
        }
      }
    }
    public function mem_getBuffers(){
      $res = $this->getMem();
      if($res === false){
        return false;
      } else {
        if(isset($res['buffers'])){
          return $res['buffers'];
        } else {
          return 0;
        }
      }
    }
    public function mem_getActive(){
      $res = $this->getMem();
      if($res === false){
        return false;
      } else {
        if(isset($res['active'])){
          return $res['active'];
        } else {
          return 0;
        }
      }
    }
    public function mem_getTotal(){
      $res = $this->getMem();
      if($res === false){
        return false;
      } else {
        if(isset($res['total'])){
          return $res['total'];
        } else {
          return 0;
        }
      }
    }
    public function mem_getPercent(){
    $res = $this->getMem();
    if($res === false){
      return false;
    } else {
      if(isset($res['percent'])){
        return $res['percent'];
      } else {
        return 0;
      }
    }
  }
    public function mem_getFree(){
      $res = $this->getMem();
      if($res === false){
        return false;
      } else {
        if(isset($res['free'])){
          return $res['free'];
        } else {
          return 0;
        }
      }
    }

    private function getMemSwap(){
      return json_decode($this->_api('getMemSwap'), true);
    }
    public function memswap_getTotal(){
      $res = $this->getMemSwap();
      if($res === false){
        return false;
      } else {
        if(isset($res['total'])){
          return $res['total'];
        } else {
          return 0;
        }
      }
    }
    public function memswap_getPercent(){
      $res = $this->getMemSwap();
      if($res === false){
        return false;
      } else {
        if(isset($res['percent'])){
          return $res['percent'];
        } else {
          return 0;
        }
      }
    }
    public function memswap_getUsed(){
      $res = $this->getMemSwap();
      if($res === false){
        return false;
      } else {
        if(isset($res['used'])){
          return $res['used'];
        } else {
          return 0;
        }
      }
    }

    private function getNetwork(){
      return json_decode($this->_api('getNetwork'), true);
    }
    public function network_getCount(){
      $res = $this->getNetwork();
      if($res === false){
        return false;
      } else {
        return count($res);
      }
    }
    public function network_getInterfaceName($piIdx){
      $res = $this->getNetwork();
      if($res === false){
        return false;
      } else {
        if(isset($res[$piIdx]['interface_name'])){
          return $res[$piIdx]['interface_name'];
        } else {
          return '';
        }
      }
    }
    public function network_getRX($piIdx){
      $res = $this->getNetwork();
      if($res === false){
        return false;
      } else {
        if(isset($res[$piIdx]['rx'])){
          return $res[$piIdx]['rx'];
        } else {
          return '';
        }
      }
    }
    public function network_getTX($piIdx){
      $res = $this->getNetwork();
      if($res === false){
        return false;
      } else {
        if(isset($res[$piIdx]['tx'])){
          return $res[$piIdx]['tx'];
        } else {
          return '';
        }
      }
    }

    public function getNow(){
      return $this->_api('getNow');
    }

    private function getProcessCount(){
      return json_decode($this->_api('getProcessCount'), true);
    }
    public function processcount_getZombie(){
      $res = $this->getProcessCount();
      if($res === false){
        return false;
      } else {
        if(isset($res['zombie'])){
          return $res['zombie'];
        } else {
          return 0;
        }
      }
    }
    public function processcount_getRunning(){
      $res = $this->getProcessCount();
      if($res === false){
        return false;
      } else {
        if(isset($res['running'])){
          return $res['running'];
        } else {
          return 0;
        }
      }
    }
    public function processcount_getTotal(){
      $res = $this->getProcessCount();
      if($res === false){
        return false;
      } else {
        if(isset($res['total'])){
          return $res['total'];
        } else {
          return 0;
        }
      }
    }
    public function processcount_getSleeping(){
      $res = $this->getProcessCount();
      if($res === false){
        return false;
      } else {
        if(isset($res['sleeping'])){
          return $res['sleeping'];
        } else {
          return 0;
        }
      }
    }

    private function getProcessList(){
      return json_decode($this->_api('getProcessList'), true);
    }
    public function processlist_getCount(){
      $res = $this->getProcessList();
      if($res === false){
        return false;
      } else {
        return count($res);
      }
    }
    public function processlist_getUserName($piIdx){
      $res = $this->getProcessList();
      if($res === false){
        return false;
      } else {
        if(isset($res[$piIdx]['username'])){
          return $res[$piIdx]['username'];
        } else {
          return '';
        }
      }
    }
    public function processlist_getStatus($piIdx){
      $res = $this->getProcessList();
      if($res === false){
        return false;
      } else {
        if(isset($res[$piIdx]['status'])){
          return $res[$piIdx]['status'];
        } else {
          return '';
        }
      }
    }
    public function processlist_getCpuTimes($piIdx){
      $res = $this->getProcessList();
      if($res === false){
        return false;
      } else {
        if(isset($res[$piIdx]['cpu_times'])){
          return $res[$piIdx]['cpu_times'];
        } else {
          return array();
        }
      }
    }
    public function processlist_getName($piIdx){
      $res = $this->getProcessList();
      if($res === false){
        return false;
      } else {
        if(isset($res[$piIdx]['name'])){
          return $res[$piIdx]['name'];
        } else {
          return '';
        }
      }
    }
    public function processlist_getMemoryPercent($piIdx){
      $res = $this->getProcessList();
      if($res === false){
        return false;
      } else {
        if(isset($res[$piIdx]['memory_percent'])){
          return $res[$piIdx]['memory_percent'];
        } else {
          return 0;
        }
      }
    }
    public function processlist_getCpuPercent($piIdx){
      $res = $this->getProcessList();
      if($res === false){
        return false;
      } else {
        if(isset($res[$piIdx]['cpu_percent'])){
          return $res[$piIdx]['cpu_percent'];
        } else {
          return 0;
        }
      }
    }
    public function processlist_getPid($piIdx){
      $res = $this->getProcessList();
      if($res === false){
        return false;
      } else {
        if(isset($res[$piIdx]['pid'])){
          return $res[$piIdx]['pid'];
        } else {
          return 0;
        }
      }
    }
    public function processlist_getIOCounters($piIdx){
      $res = $this->getProcessList();
      if($res === false){
        return false;
      } else {
        if(isset($res[$piIdx]['io_counters'])){
          return $res[$piIdx]['io_counters'];
        } else {
          return array();
        }
      }
    }
    public function processlist_getCommandLine($piIdx){
      $res = $this->getProcessList();
      if($res === false){
        return false;
      } else {
        if(isset($res[$piIdx]['cmdline'])){
          return $res[$piIdx]['cmdline'];
        } else {
          return '';
        }
      }
    }
    public function processlist_getMemoryInfo($piIdx){
      $res = $this->getProcessList();
      if($res === false){
        return false;
      } else {
        if(isset($res[$piIdx]['memory_info'])){
          return $res[$piIdx]['memory_info'];
        } else {
          return array();
        }
      }
    }
    public function processlist_getNice($piIdx){
      $res = $this->getProcessList();
      if($res === false){
        return false;
      } else {
        if(isset($res[$piIdx]['nice'])){
          return $res[$piIdx]['nice'];
        } else {
          return 0;
        }
      }
    }

    private function getSensors(){
      return json_decode($this->_api('getSensors'), true);
    }
    public function sensors_getCount(){
      $res = $this->getSensors();
      if($res === false){
        return false;
      } else {
        return count($res);
      }
    }
    public function sensors_getValue($piIdx){
      $res = $this->getSensors();
      if($res === false){
        return false;
      } else {
        if(isset($res[$piIdx]['value'])){
          return $res[$piIdx]['value'];
        } else {
          return '';
        }
      }
    }
    public function sensors_getLabel($piIdx){
      $res = $this->getSensors();
      if($res === false){
        return false;
      } else {
        if(isset($res[$piIdx]['label'])){
          return $res[$piIdx]['label'];
        } else {
          return '';
        }
      }
    }

    private function getSystem(){
      return json_decode($this->_api('getSystem'), true);
    }
    public function system_getLinuxDistro(){
      $res = $this->getSystem();
      if($res === false){
        return false;
      } else {
        if(isset($res['linux_distro'])){
          return $res['linux_distro'];
        } else {
          return '';
        }
      }
    }
    public function system_getPlatform(){
      $res = $this->getSystem();
      if($res === false){
        return false;
      } else {
        if(isset($res['platform'])){
          return $res['platform'];
        } else {
          return '';
        }
      }
    }
    public function system_getOSName(){
      $res = $this->getSystem();
      if($res === false){
        return false;
      } else {
        if(isset($res['os_name'])){
          return $res['os_name'];
        } else {
          return '';
        }
      }
    }
    public function system_getHostname(){
      $res = $this->getSystem();
      if($res === false){
        return false;
      } else {
        if(isset($res['hostname'])){
          return $res['hostname'];
        } else {
          return '';
        }
      }
    }
    public function system_getOSVersion(){
      $res = $this->getSystem();
      if($res === false){
        return false;
      } else {
        if(isset($res['os_version'])){
          return $res['os_version'];
        } else {
          return '';
        }
      }
    }
  }