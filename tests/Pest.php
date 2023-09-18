<?php

function isUnitTest()
{
    return ! empty($GLOBALS['argv']) && $GLOBALS['argv'][1] === '--group=unit';
}

function _debug(...$vars)
{
    fwrite(STDERR, print_r(["DEBUG" => $vars], true));
}

uses()->group('integration')->in('Integration');
uses()->group('unit')->in('Unit');
