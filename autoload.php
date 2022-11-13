<?php

function loaderEntities($className)
{
    require_once 'entities' . '/' . $className . '.php';
}

    spl_autoload_register('loaderEntities');
