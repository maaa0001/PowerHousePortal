<?php

class Student {
    private $name;

    public function __construct($name){
        $this->name = $name;
    }

    public function getName(){
        return $this->name;
    }

    public function sayHello(){
        return "Hello, my name is " . $this->name;
    }

}

$student  = new Student("Rasmus");
echo $student->sayHello();