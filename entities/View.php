<?php

    //2. Абстрактный класс для представления
abstract class View
{
    public object $storage;


    abstract public function displayTextById($id);//Выводит текст по id

    abstract public function displayTextByUrl($url);//Выводитт текст по url
}
