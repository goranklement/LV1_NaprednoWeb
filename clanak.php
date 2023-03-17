<?php

include ('simple_html_dom.php');


interface iNews {
    public function create($data);
    public function save();
    public function read();
}

class Clanak implements iNews {
    //pošto ima samo 2 različita teksta po članku imam samo 2 atributa
    public $dugi_naslov = NULL;
    public $kratki_naslov = NULL;

    function __construct($data){
        //funkcija koja generira jedinstveni ID
        $this->id = uniqid(); 
        $this->dugi_naslov = $data['naslov'];
        $this->kratki_naslov = $data['opis'];
    }

    function create($data){
        self::__construct($data);
    }

    function read(){
        $hostname = "localhost";
        $dbname = "dnevnik";
        $username = "root";
        $password = "";
        
        //otvaranje nove konekcije prema mysql bazi koja se kreira preko XAMPP-a
        $mysqli = new mysqli($hostname, $username, $password, $dbname);

        if ($mysqli->connect_error) {
            die('Failed to connect to MySQL: ' . $mysqli->connect_error);
        }
        

        $result = $mysqli->query("SELECT * FROM vijesti" );
            //dohvaćanje i ispis svih unosa iz baze podataka
            while($row = $result->fetch_assoc()){
                echo $row["ID"] . '<br> '.
                $row["prvi_naslov"] . '<br> '.
                $row["drugi_naslov"] . '<br> ';
            }                       
        
    }

    function save(){
        $hostname = "localhost";  
        $dbname = "dnevnik";
        $username = "root";
        $password = "";

        $mysqli = new mysqli($hostname, $username, $password, $dbname);
        $id = $this->id;

        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }

        $upit = "INSERT INTO vijesti (ID, prvi_naslov, drugi_naslov) VALUES ('$id', '$this->dugi_naslov', '$this->kratki_naslov')";

        if($mysqli->query($upit) === true) {
            $this->read();
        }
        $mysqli->close();
    }
}
//spremanje HTML koda u &html datoteku
$html = file_get_html('https://dnevnik.hr/vijesti/');

//pronalaženje točno određenih elemenata i klasa unutar kojih se nalaze naslov i podnaslov
foreach($html->find('div[class=title-holder]')as $holder){
    foreach($holder->find('h3[class=title]')as $title){}
    foreach($holder->find('span[class=subtitle]')as $subtitle){}

        $clanak = array('naslov' => $title->plaintext, 
        'opis' => $subtitle->plaintext
);

$unos = new Clanak($clanak);
$unos->save();

}
