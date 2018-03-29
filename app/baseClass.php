<?php

class baseClass {

	private $db_server;
	private $db_user;
	private $db_pass;
	private $db_database;

	protected $conn  = null;

	
	public function __construct($action=null){

		$this->db_server = 'localhost';
		$this->db_user     = 'root';
		$this->db_pass     = 'toor';
		$this->db_database = 'cep';		

		$this->_connect();


		if(isset($action)){
			$this->_execAction($action);
		}


	}


	// Efetua conexão à base de dados
	protected function _connect(){
	
		try {
		//Conecta ao banco
		$this->conn = mysql_connect($this->db_server, $this->db_user, $this->db_pass);
		
		if (!$this->conn)
		{
			throw new Exception('MySQL Connection Database Error: ' . mysql_error());
		}
		
		
		//Seleciona o banco de dados desejado
		mysql_select_db($this->db_database,$this->conn);
		mysql_set_charset('utf8',$this->conn);
		
		} catch (Exception $e) {
		

			die (json_encode(array(
				"success" => false,
				"msg" => utf8_encode('Connection Database Error: '. mysql_error())
			)));
		
		
		}
	}

	
	/****
	 * Função que executa uma query no banco de dados, se um dia precisarmos mudar de banco teoricamente bastaria mudar
	 * as funções de manipulação do banco que estão abstraidas aqui, também facilita por não precisar passar 2 parâmetros
	 * passando apenas o sql
	****/
	
	/****
	 * Aqui temos uma facilidade, geralmente o que fazermos é fazer um select montar um array e imprimir em JSON,
	 * chamando esta função temos o resultado de um sql já em array pronta para ser codificado em JSON
	****/

	public function _fetch($query){
		$row = mysql_fetch_assoc($query);
		return $row;
	}


	public function _select_fetch_all($sql){
	
		return $this->_fetch_all($this->_select($sql));
	}

	
	public function _select_fetch($sql){
		return $this->_fetch($this->_select($sql));
	}

	public function _select($sql){
		$result = mysql_query($sql,$this->conn);
		return $result;
	}

	/****
	 * Como o mysql não nos prove uma função que retorne todos os registros em forma de array aqui crio uma que faz isso
	****/

	public function _fetch_all($query){
		$rows = array();
		while ($row = mysql_fetch_object($query)) {
			$rows[] = $row;
		}
		return $rows;
	}
	

	public function inicia_transacao(){
	
		$sql = "START TRANSACTION"; 
		$resultado = mysql_query($sql); 
		
		return $resultado;
		
	}
	
	public function rollback(){
		$sql = "ROLLBACK"; 
		$resultado = mysql_query($sql); 
		
		return $resultado; 
	}
	
	public function commit(){
	
		$sql = "COMMIT"; 
		$resultado = mysql_query($sql); 
		
		return $resultado; 
	}


	/****
	 * Esta função executa um método da classe pelo seu nome em STRING caso ele exista e esteja na lista
	 * de ações contiga em $actions, esta lista deve ser definida em cada classe filha
	****/
	public function _execAction($action){
		if((in_array($action, $this->actions))&&(method_exists($this, $action))){
			call_user_func(array($this, $action));
		}else{
			echo json_encode(array(
				'success' => false,
				'msg' => utf8_encode("Ação inválida: '$action'")
			));
		}
	}

}