<?php

    require_once 'Storage.php';
class FileStorage extends Storage // Метод серриализует и записывает в файл, объект класса TelegraphText
{
    public function create(&$object): string
    {
        $count = 1;
        $fileName = $object->slug . '_' . date('Y-m-d');
        $name = $fileName;
        while (file_exists($name)) {
            $name = $fileName . '_' . $count;
            $count++;
        }
        $object->slug = $name;
        $serializedObject = serialize($object);
        file_put_contents($object->slug, $serializedObject);
        return $object->slug;
    }

    public function delete($slug) // Удаляет файл с именем $slug
    {
        unlink($slug);
    }

    public function list_(): array
    {
        //Возвращает массив объектов, полученных при дессиаризации содержимого файлов в дирректории.
        $resultList = [];//Результирующий массив
        $list = scandir('./');//Перечень всех файлов и папок, находящихся в дирректории
        foreach ($list as $item) {
            if ($item !== '.' && $item !== '..' && !is_dir($item) && $item !== 'index.php') {
                $content = file_get_contents($item);
                $resultList[] = unserialize($content);
            }
        }
        return $resultList;
    }

    public function read($slug): object //Возвращает дессиаризованный объект из файла с именем $slug
    {
        return unserialize(file_get_contents($slug));
    }

    public function update($slug, $object) //Перезаписывает файл с именем $slug серриализованным объектом $object
    {
        $serializedObject = serialize($object);
        file_put_contents($slug, $serializedObject);
    }
}
