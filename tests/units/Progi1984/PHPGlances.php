<?php

namespace tests\units\Progi1984;

require_once __DIR__ . '/../../../src/Progi1984/PHPGlances.php';

use \mageekguy\atoum;
use Progi1984;

class PHPGlances extends atoum\test
{
    public function testConstruct()
    {
        $this
            ->if($oPHPGlances = new Progi1984\PHPGlances('', ''))
            ->then
                ->object($oPHPGlances);
    }
}
