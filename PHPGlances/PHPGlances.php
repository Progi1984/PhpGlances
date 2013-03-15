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
  }