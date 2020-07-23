<?php

require_once('./base/modelo.php');

class UserLogin extends modelo{

    public function __construct(){
        parent::__construct();
    }

    public function getUserJson($email,$pass){
        $result = $this->_db->query("SELECT id  FROM distribuidor 
        WHERE email='".$email."' AND password='".$pass."' AND estado=true ");
        while ($row = $result->fetch_assoc()) {
            $arr = array("id" => $row['id']);
        }
        $result->free();
        $this->disconnect();
        return $arr;
    }

}
/*$foo = file_get_contents("php://input");
http_response_code(500);
$r=json_decode($foo,true);*/

$email = $_POST['email'];//"matias@presisconsultores.com";//$_POST['email'];
$pass = $_POST['password'];//"123456";//$_POST['password'];

//die($r['email']."-".$r['password']);

$user = new UserLogin();

$distribuidor_id = $user->getUserJson($email,$pass);

if(!$distribuidor_id){
    http_response_code(500);
}else{
    echo json_encode($distribuidor_id);
}


?>