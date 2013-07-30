<?php

  include_once '../PHPGlances/PHPGlances.php';

  define('USE_CACHE', true);

  $oGlances = new PHPGlances('http://127.0.0.1', 61209);
  $bAlive = $oGlances->pingServer();
  if(!$bAlive){
    echo 'Can\'t connect to the server';
  } else {
    $oGlances->setCacheStatus(USE_CACHE);

    $res = $oGlances->listMethods();
    echo 'listMethods : ';
    echo '<ul>';
    foreach($res as $item){
      echo '<li>'.$item.'</li>';
    }
    echo '</ul>';

    echo 'getCore : <br />';
    echo 'Core : '.$oGlances->getCore().'<br />';
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

    echo 'getAllLimits : <br />';
    echo '> CPU_IOWait : '.json_encode($oGlances->limit_getCPU_IOWait()).'<br />';
    echo '> CPUSystem : '.json_encode($oGlances->limit_getCPUSystem()).'<br />';
    echo '> CPUUser : '.json_encode($oGlances->limit_getCPUUser()).'<br />';
    echo '> FS : '.json_encode($oGlances->limit_getFS()).'<br />';
    echo '> Load : '.json_encode($oGlances->limit_getLoad()).'<br />';
    echo '> Mem : '.json_encode($oGlances->limit_getMem()).'<br />';
    echo '> ProcessCPU : '.json_encode($oGlances->limit_getProcessCPU()).'<br />';
    echo '> ProcessMem : '.json_encode($oGlances->limit_getProcessMem()).'<br />';
    echo '> STD : '.json_encode($oGlances->limit_getSTD()).'<br />';
    echo '> SWAP : '.json_encode($oGlances->limit_getSWAP()).'<br />';
    echo '> Temp : '.json_encode($oGlances->limit_getTemp()).'<br />';
    echo '<br />';

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

    echo 'getNetwork : <br />';
    $numNet = $oGlances->network_getCount();
    echo ' > count : '.$numNet.'<br />';
    for($inc = 0 ; $inc < $numNet ; $inc++){
      echo ' >> Interface Name : '.utf8_decode($oGlances->network_getInterfaceName($inc)).'<br />';
      echo ' >> RX : '.$oGlances->network_getRX($inc).'<br />';
      echo ' >> TX : '.$oGlances->network_getTX($inc).'<br />';
      echo '<br />';
    }

    echo 'getNow : <br />';
    echo 'Now : '.$oGlances->getNow().'<br />';
    echo '<br />';

    echo 'getProcessCount : <br />';
    echo ' > Running : '.$oGlances->processcount_getRunning().'<br />';
    echo ' > Sleeping : '.$oGlances->processcount_getSleeping().'<br />';
    echo ' > Total : '.$oGlances->processcount_getTotal().'<br />';
    echo ' > Zombie : '.$oGlances->processcount_getZombie().'<br />';
    echo '<br />';

    echo 'getProcessList : <br />';
    $numProcess = $oGlances->processlist_getCount();
    echo ' > count : '.$numProcess.'<br />';
    for($inc = 0 ; $inc < $numProcess ; $inc++){
      echo ' >> User Name : '.$oGlances->processlist_getUserName($inc).'<br />';
      echo ' >> Status : '.$oGlances->processlist_getStatus($inc).'<br />';
      echo ' >> CPU Times : '.json_encode($oGlances->processlist_getCpuTimes($inc)).'<br />';
      echo ' >> Name : '.$oGlances->processlist_getName($inc).'<br />';
      echo ' >> Memory Percent : '.$oGlances->processlist_getMemoryPercent($inc).'<br />';
      echo ' >> CPU Percent : '.$oGlances->processlist_getCpuPercent($inc).'<br />';
      echo ' >> PID : '.$oGlances->processlist_getPid($inc).'<br />';
      echo ' >> IO Counters : '.json_encode($oGlances->processlist_getIOCounters($inc)).'<br />';
      echo ' >> CommandLine : '.$oGlances->processlist_getCommandLine($inc).'<br />';
      echo ' >> MemoryInfo : '.json_encode($oGlances->processlist_getMemoryInfo($inc)).'<br />';
      echo ' >> Nice : '.$oGlances->processlist_getNice($inc).'<br />';
      echo '<br />';
    }

    echo 'getSensors : <br />';
    $numSensors = $oGlances->sensors_getCount();
    echo ' > count : '.$numSensors.'<br />';
    for($inc = 0 ; $inc < $numSensors ; $inc++){
      echo ' >> Label : '.$oGlances->sensors_getLabel($inc).'<br />';
      echo ' >> Value : '.$oGlances->sensors_getValue($inc).'<br />';
      echo '<br />';
    }
    echo '<br />';

    echo 'getSystem : <br />';
    echo ' > Hostname : '.$oGlances->system_getHostname().'<br />';
    echo ' > Linux Distrib : '.$oGlances->system_getLinuxDistro().'<br />';
    echo ' > OSName : '.$oGlances->system_getOSName().'<br />';
    echo ' > OSVersion : '.$oGlances->system_getOSVersion().'<br />';
    echo ' > Platform : '.$oGlances->system_getPlatform().'<br />';
    echo '<br />';
  }