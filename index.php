<?php

Interface CRUD {
public function create();

static function read($id);

public function update();

public function delete();
}

class Entity {
    protected static $conn; // shared connection
    protected $id;          // all records should have an id

    static function init() {
        self::$conn = new mysqli('mariaDB', 'gorilla', 'code') or die(Person::$conn->connect_error);
        self::$conn->select_db('gorilla') or die('database niet geselecteerd');
    }
}
class Product implements CRUD {

    public $name;
    public $description;

    public function create();

    static function read($id);

    public function update();

    public function delete();    
}
class Person extends Entity implements CRUD {


    public $name;
    protected $birthday;
    private $phone;



    function __construct($name, $birthday, $phone, $id=-1) {
        $this->id = $id;
        $this->name = $name;
        $this->birthday = $birthday;
        $this->phone = $phone;
    }

    function get_id() {
        return $this->id;
    }

    function create() {
        if($id !=-1) {
        $stmt = self::$conn->prepare('insert into person (name, birthday, phone) values (?, ?, ?)');
        $stmt->bind_param("sss", $this->name, $this->birthday, $this->phone);
        $stmt->execute();
        $this->id = $stmt->insert_id;
        return $stmt;
    }    
    return false;
}
    static function read($id) {
        $result = self::$conn->query("select * from person where id=$id") or die(self::$conn->error);
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return new Person($row['name'], $row['birthday'], $row['phone'], $row['id']);
        } else {
            return null;
        }
    }

    function update() {
        if ($id !=-1){
        $stmt = self::$conn->prepare('update person set name=?, birthday=?, phone=? where id=?');
        $stmt->bind_param("sssi", $this->name, $this->birthday, $this->phone, $this->id);
        $stmt->execute();
        return $stmt;
    }
    return false;
}

    function delete() {
        if ($id != -1) {
        return self::$conn->query("delete from person where id=$this->id") or die(self::$conn->error);
    }
    return false;
}
}

Person::init();

echo "<h1>CRUD</h1>Demontratie of Create, Read, Update, Delete";

echo '<h2>Construct</h2>';
$jan = new Person("Jan", "1990-12-20", "0612345678");
print_r($jan);

echo '<h2>Create</h2>';
$jan->create();
print_r($jan);

echo '<h2>Select</h2>';
print_r(Person::read($jan->get_id()));

echo '<h2>Update</h2>';
$jan->name = 'Janet';
$jan->update();
print_r($jan);

echo '<h2>Delete</h2>';
$previous = Person::read($jan->get_id()-1);
if($previous) {
    print_r($previous);
    echo "<p>";
    print_r($previous->delete());
} else {
    echo "Nothing to delete";
}
