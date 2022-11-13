<?php

    require_once '../interfaces/EventListenerInterface.php';
    require_once '../interfaces/LoggerInterface.php';
    //3.Абстрактный класс User
abstract class User
{
    protected string $id, $name, $role;

    abstract protected function getTextToEdit();//Выводит список текстов, доступных пользователю для редактирования
}
