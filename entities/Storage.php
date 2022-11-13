<?php

    require_once './interfaces/EventListenerInterface.php';
    require_once './interfaces/LoggerInterface.php';

abstract class Storage implements LoggerInterface, EventListenerInterface  //1.Абстрактный класс для хранилища
{
    public function attachEvent($methodName, $function)
    {
        // TODO: Implement attachEvent() method.
    }

    public function detouchEvent($methodName)
    {
        // TODO: Implement detouchEvent() method.
    }

    public function lastMessages($numOfMessages): array
    {
        // Не понимаю, как получить список последних сообщений из лога, если все сообщения пишуться
        //в одну строку. В итоге лог - это одна большая строка. Между строками отсутствуют разделители
    }

    public function logMessage(
        $errorText
    ) {
        error_log($errorText, 3, 'error_log');//1-й аргумент сообщение об ошибке, которое должно быть логировано
        //2-й аргумент определяет, куда отправлять ошибку. (3 - применяется к указанному в destination файлу.)
        //3-й аргумент назначение (файл, куда записываются ошибки)
    }

    abstract public function create(&$object);//создает объект в хранилище

    abstract public function read($slug): object;//получает объект из хранилища

    abstract public function update($slug, $object);//обновляет существующий объект в хранилище

    abstract public function delete($slug);//удаляет объект из хранмилища

    abstract public function list_(): array;//возвращает массив всех объектов в хранилище
}
