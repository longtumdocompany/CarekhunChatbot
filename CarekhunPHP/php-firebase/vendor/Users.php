<?php

require_once 'autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class Users {

    protected $database;
    protected $dbname = 'users';

    public function __construct(){
        $acc = ServiceAccount::fromJsonFile('C:\xampp\htdocs\php-firebase\secrete\php-carekhun-65ae90d8b361.json');
        $firebase = (new Factory)->withServiceAccount($acc)->create();

        $this->database = $firebase->getDatabase();
    }

    public function get(int $userID = NULL){
        if(empty($userID) || !isset($userID)) {return FALSE; }

        if($this->database->getReference($this->dbname)->getChild($userID)){
            return $this->database->getReference($this->dbname)->getChild($userID)->getValue();
        } else {
            return FALSE;
        }
    }

    public function insert(array $data){
        if(empty($data) || !isset($data)) { return FALSE; }

        foreach($data as $key => $value){
            $this->database->getReference($this->dbname)->getChild($key)->set($value);
        }

        return TRUE;

    }

    public function delete(int $userID){
        if(empty($data) || !isset($data)) { return FALSE; }

        if($this->database->getReference($this->dbname)->getSnapshot()->hasChild($userID)){
            $this->database->getReference($this->dbname)->getChild($userID)->remove();
            return TRUE;
        } else {
            return FALSE;
        }
    }

}

$users = new Users();

var_dump($user->insert([
    '1' => 'top', '2' => 'foey'
]));