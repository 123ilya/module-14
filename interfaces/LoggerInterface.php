<?php

interface LoggerInterface //Интерфейс логирования.
{
    public function logMessage($errorText);//Записать сообщения в лог. На вход текст ошибки.

    public function lastMessages($numOfMessages): array;//Получить список последних сообщений из лога
    // На вход количество, сообщений, которые необходимо получить. Возвращает массив сообщений
}
