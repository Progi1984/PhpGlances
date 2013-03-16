<?php

  include_once '../PHPGlances/PHPGlances.php';

  $oGlances = new PHPGlances('http://127.0.0.1', 61209);
  $res = $oGlances->listMethods();
  echo 'listMethods : ';
  echo '<ul>';
  foreach($res as $item){
    echo '<li>'.$item.'</li>';
  }
  echo '</ul>';

  $res = $oGlances->getCore();
  echo 'getCore : <br />';
  echo 'Core : '.$res.'<br />';
  echo '<br />';

  echo 'getCpu : <br />';
  echo ' > IOWait : '.$oGlances->cpu_getIOWait().'<br />';
  echo ' > System : '.$oGlances->cpu_getSystem().'<br />';
  echo ' > Idle : '.$oGlances->cpu_getIdle().'<br />';
  echo ' > User : '.$oGlances->cpu_getUser().'<br />';
  echo ' > IRQ : '.$oGlances->cpu_getIRQ().'<br />';
  echo ' > Nice : '.$oGlances->cpu_getNice().'<br />';
  echo '<br />';

  echo 'getDiskIO : <br />';
  $numDisk = $oGlances->diskIO_getCount();
  echo ' > count : '.$numDisk.'<br />';
  for($inc = 0 ; $inc < $numDisk ; $inc++){
    echo ' >> Disk Name : '.$oGlances->diskIO_getDiskName($inc).'<br />';
    echo ' >> Bytes Read : '.$oGlances->diskIO_getReadBytes($inc).'<br />';
    echo ' >> Bytes Write : '.$oGlances->diskIO_getWriteBytes($inc).'<br />';
    echo '<br />';
  }

  echo 'getFs : <br />';
  $numFS = $oGlances->fs_getCount();
  echo ' > count : '.$numFS.'<br />';
  for($inc = 0 ; $inc < $numFS ; $inc++){
    echo ' >> Mount Point : '.$oGlances->fs_getMountPoint($inc).'<br />';
    echo ' >> Device Name : '.$oGlances->fs_getDeviceName($inc).'<br />';
    echo ' >> File System Type : '.$oGlances->fs_getFileSystemType($inc).'<br />';
    echo ' >> Used : '.$oGlances->fs_getUsed($inc).'<br />';
    echo ' >> Available : '.$oGlances->fs_getAvailable($inc).'<br />';
    echo ' >> Size : '.$oGlances->fs_getSize($inc).'<br />';
    echo '<br />';
  }

  echo 'getLoad : <br />';
  echo ' > Min1 : '.$oGlances->load_getMin1().'<br />';
  echo ' > Min5 : '.$oGlances->load_getMin5().'<br />';
  echo ' > Min15 : '.$oGlances->load_getMin15().'<br />';
  echo '<br />';

  echo 'getMem : <br />';
  echo ' > Active : '.$oGlances->mem_getActive().'<br />';
  echo ' > Buffers : '.$oGlances->mem_getBuffers().'<br />';
  echo ' > Cached : '.$oGlances->mem_getCached().'<br />';
  echo ' > Free : '.$oGlances->mem_getFree().'<br />';
  echo ' > Inactive : '.$oGlances->mem_getInactive().'<br />';
  echo ' > Percent : '.$oGlances->mem_getPercent().'<br />';
  echo ' > Total : '.$oGlances->mem_getTotal().'<br />';
  echo ' > Used : '.$oGlances->mem_getUsed().'<br />';
  echo '<br />';

  echo 'getMemSwap : <br />';
  echo ' > Percent : '.$oGlances->memswap_getPercent().'<br />';
  echo ' > Total : '.$oGlances->memswap_getTotal().'<br />';
  echo ' > Used : '.$oGlances->memswap_getUsed().'<br />';
  echo '<br />';