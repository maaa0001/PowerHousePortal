<?php 

class User {
    private $name;
    private $email;
    private $password;

    public function __construct($name, $email, $password) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    public function getname(){
        return $this->name;
    }

    public function getemail(){
        return $this->email;
    }

    public function CheckPassword($password){
        return $this->password;
    }
}


$user = new User("Emil", "emil@example.com", "password123");
?>