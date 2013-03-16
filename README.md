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
- [PHP-Curl](http://php.net/manual/fr/book.curl.php)
- [PHP-XML-RPC](http://php.net/manual/fr/book.xmlrpc.php)

Example usage:
```php
  include_once '../PHPGlances/PHPGlances.php';

  $oGlances = new PHPGlances('http://127.0.0.1', 61209);
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
```
