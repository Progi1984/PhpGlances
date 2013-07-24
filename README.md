PHPGlances
==========

A PHP library for the Glances XML/RPC API<br>

What is Glances?
- [Glances](https://github.com/nicolargo/glances.git) is a CLI system monitor written in Python

What does this library do?
- If Glances is run as ```glances -s``` then information can be retrieved from it using an XML/RPC API

Read the specification of the Glances API:
- https://github.com/nicolargo/glances/wiki/The-Glances-API-How-To

Dependencies:
Per default, any extensions are useful.
But if extensions [PHP-Curl](http://php.net/manual/fr/book.curl.php), [PHP-JSON](http://php.net/manual/fr/book.json.php), [PHP-SimpleXML](http://php.net/manual/fr/book.simplexml.php) and [PHP-XML-RPC](http://php.net/manual/fr/book.xmlrpc.php) are load, then PHPGlances will be more optimal.


Example usage:
```php
  include_once '../PHPGlances/PHPGlances.php';

  $oGlances = new PHPGlances('http://127.0.0.1', 61209);
  $bAlive = $oGlances->pingServer();
  if(!$bAlive){
    echo 'Can\'t connect to the server';
  } else {
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
  }
```

Changelog
---------
**Version 0.10**
  - Initial Release

**Version 0.11** __(current)__
  - ADDED pingServer() which return a boolean to check if Glances server is available
  - ADDED Replacement for functions used in Curl / JSON / SimpleXML / XmlRPC (Issue [#3](https://github.com/Progi1984/PHPGlances/issues/3))