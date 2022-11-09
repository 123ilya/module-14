<?php

interface LoggerInterface //Интерфейс логирования.
{
    public function logMessage($errorText);//Записать сообщения в лог. На вход текст ошибки.

    public function lastMessages($numOfMessages): array;//Получить список последних сообщений из лога
    // На вход количество, сообщений, которые необходимо получить. Возвращает массив сообщений
}

interface EventListenerInterface//Интерфейс для обработки событий
{
    public function attachEvent($methodName, $function);//Присвоить событию обработчик
    //На вход имя метода класса, при выполнении которого он должен быть вызван и колбек функцию, которая вызывается
    //при возникновении события

    public function detouchEvent($methodName);//Убрать обработчик события. Приенимает на вход имя метода класа,
    //при выполнении которого вызывается обработчик события

}


class TelegraphText
{
    private string $text; //Сам текст
    private string $title; //Заголовок текста
    private string $author; //Автор
    private string $published; //Дата создания объекта
    private string $slug; //Имя файла

    public function __construct($author, $slug)
    {
        $this->author = $author;
        $this->slug = $slug;
        $this->published = date('Y-m-d');
    }

// 'Волшебный сеттер' для полей 'author', 'slug', 'published'.
    public function __set($name, $value)
    {
        if ($name == 'author') {
            if (strlen($value) <= 120) {
                $this->author = $value; //Значение устанавливается только, если его длинна не превышает 120 символов
            }
        }
        if ($name == 'slug') {
            if (preg_match('[\w]', $value)) {
                $this->slug = $value;//Значение устанавливается, только если символы его составляющие это буквы, цыфры либо "_"
            }
        }
        if ($name == 'published') {
            $newPublishedDate = str_replace('-', '', $value);
            $currentPublishedDate = str_replace('-', '', $this->published);
            if ($newPublishedDate >= $currentPublishedDate) {
                $this->published = $value;//Значение устанавливается, только если дата равна либо позже текущей даты.
            }
        }
        if ($name == 'text') {
            $this->text = $value;
            $this->storeText();
            echo '111';
        }
    }

    public function __get($name)
    {
        if ($name == 'author') {
            return $this->author;
        }
        if ($name == 'slug') {
            return $this->slug;
        }
        if ($name == 'published') {
            return $this->published;
        }
        if ($name == 'text') {
            return $this->loadText();
        }
    }

    private function storeText(): void // На основе полей объекта формирует массив, серриализует его, а затем
        //записывает в файл.
    {
        $post = [
            'title' => $this->title,
            'text' => $this->text,
            'author' => $this->author,
            'published' => $this->published
        ];
        $serializedPost = serialize($post);
        file_put_contents($this->slug, $serializedPost);
    }

    private function loadText(
    ) //Выгружает содержимое из файла. И на основе выгруженного массива обновляет поля объекта.
    {
        $loadedPost = unserialize(file_get_contents($this->slug));
        if (filesize($this->slug) !== 0) {
            $this->title = $loadedPost['title'];
            $this->text = $loadedPost['text'];
            $this->author = $loadedPost['author'];
            $this->published = $loadedPost['published'];
            return $this->text;
        }
    }

    public function editText($title, $text): void//Изменяет содержимое полей объекта title и text
    {
        $this->title = $title;
        $this->text = $text;
    }
}


//1.Абстрактный класс для хранилища
abstract class Storage implements LoggerInterface, EventListenerInterface
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

    public
    function logMessage(
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

//2. Абстрактный класс для представления
abstract class View
{
    public object $storage;


    abstract public function displayTextById($id);//Выводит текст по id

    abstract public function displayTextByUrl($url);//Выводитт текст по url

}

//3.Абстрактный класс User
abstract class User
{
    protected string $id, $name, $role;

    abstract protected function getTextToEdit();//Выводит список текстов, доступных пользователю для редактирования
}

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

    public function list_(
    ): array //Возвращает массив объектов, полученных при дессиаризации содержимого файлов в дирректории.
    {
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

//--------------------------------------------------------------------------
$test = new TelegraphText('ilya','test');
//$test->editText('ilya','gjdfgjldigjldifjgldfgjldfgjldgj');
//$test->text='rrr';
echo $test->text;