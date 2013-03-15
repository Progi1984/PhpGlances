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
  echo 'Core : '.$res.'<br /><br />';

  echo 'getCpu : <br />';
  echo ' > IOWait : '.$oGlances->cpu_getIOWait().'<br />';
  echo ' > System : '.$oGlances->cpu_getSystem().'<br />';
  echo ' > Idle : '.$oGlances->cpu_getIdle().'<br />';
  echo ' > User : '.$oGlances->cpu_getUser().'<br />';
  echo ' > IRQ : '.$oGlances->cpu_getIRQ().'<br />';
  echo ' > Nice : '.$oGlances->cpu_getNice().'<br />';

  echo 'getDiskIO : <br />';
  $numDisk = $oGlances->diskIO_getCount();
  echo ' > count :'.$numDisk.'<br />';
  for($inc = 0 ; $inc < $numDisk ; $inc++){
    echo ' >> Disk Name : '.$oGlances->diskIO_getDiskName($inc).'<br />';
    echo ' >> Bytes Read : '.$oGlances->diskIO_getReadBytes($inc).'<br />';
    echo ' >> Bytes Write : '.$oGlances->diskIO_getWriteBytes($inc).'<br />';
  }
