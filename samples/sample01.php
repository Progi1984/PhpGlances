<?php

  include_once '../PHPGlances/PHPGlances.php';

  $oGlances = new PHPGlances('http://127.0.0.1', 61209);
  $bAlive = $oGlances->pingServer();
  if(!$bAlive){
    echo 'Can\'t connect to the server';
  } else {
    $res = $oGlances->listMethods();
    echo 'listMethods : ';
    if($res === false){
      echo 'ERROR : "'.$oGlances->getError().'"';
    } else {
      echo '<ul>';
      foreach($res as $item){
        echo '<li>'.$item.'</li>';
      }
      echo '</ul>';
    }
  }