<?php 
require_once "config.php";

class modelo
{
    protected $_db;

    public function __construct()
    {
        $this->_db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $this->_db->set_charset(DB_CHARSET);

        if ( $this->_db->connect_errno )
        {
            echo "Fallo al conectar a MySQL: ". $this->_db->connect_error;
            return;    
        }
    }
	
	public function disconnect(){
		$this->_db->close();
	}
}
?> 