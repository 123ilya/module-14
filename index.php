<?php

    require_once 'autoload.php';

//--------------------------------------------------------------------------
    $test = new TelegraphText('ilya', 'eee');
    $test->editText('new title', 'fffffffffffffffffff');
    $storageText = new FileStorage();
    $storageText->create($test);
    echo 'it is working';
