<?php

class Book {
    protected $title;

    public function __construct($title){
        $this->title = $title;
    }

    public function getTitle(){
        return $this->title;
    }

    public function describe(){
        return "This book is called " . $this->title;
    }
}



class Ebook extends Book {
    public function downlad(){
        return "You are downloading " . $this->title;
    }
}

$book = new Book("Harry Potter");
echo $book->describe();

echo "<br>";

$ebook = new EBook("Harry Potter");
echo $ebook->downlad();