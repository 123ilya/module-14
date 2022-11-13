<?php

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

    private function loadText()
    {
        //Выгружает содержимое из файла. И на основе выгруженного массива обновляет поля объекта.
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
