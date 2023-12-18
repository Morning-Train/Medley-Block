<?php

function _debug(...$vars)
{
    fwrite(STDERR, print_r(["DEBUG" => $vars], true));
}

uses()->group('unit')->in('Unit');
