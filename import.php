<?php

Class Import_Sql_Multicall{
	private $start_time;
	private $db;
	// MySQL host
	private $host           = 'localhost';
	// MySQL username
	private $username       = 'root';
	// MySQL password
	private $password       = 'root';
	// Database name
	private $database       = 'test';
	// database file path
	private	$file           = 'file.sql';
	//max execution time
	private	$import_timeout = 30;

	public function __construct(){
		$this->set_flags();
		$this->choose_action();
	}

	private  function set_flags() {
		$this->start_time = time();
	}

	private  function choose_action() {
		$this->init_db();
		$offset = !empty($_POST['offset']) ? $_POST['offset'] : 0;
		$result = $this->import_sql_file($offset);
		die("<LOTUS_START>".json_encode($result)."<LOTUS_END>");
	}

	private function init_db(){
		// Connect to MySQL server
		$this->db = mysqli_connect($this->host, $this->username, $this->password, $this->database);

		if (mysqli_connect_errno()){
			return array('status' => 'error', 'msg' => "Failed to connect to MySQL: " . mysqli_connect_error());
		}
	}

	public function import_sql_file($offset){

		$handle = fopen($this->file, "rb");

		if (empty($handle)) {
			return array('status' => 'error', 'msg' => 'Cannot open database file');
		}

		$offset = empty($offset) ? 0 : $offset;

		$current_query = '';

		$this_lines_count = $loop_iteration = 0;

		while ( ( $line = fgets( $handle ) ) !== false ) {

			$loop_iteration++;

			if ($loop_iteration <= $offset ) {
				continue; //check index; if it is previously written ; then continue;
			}

			$this_lines_count++;

			if (substr($line, 0, 2) == '--' || $line == '' || substr($line, 0, 3) == '/*!') {
				continue; // Skip it if it's a comment
			}

			$current_query .= $line;

			// If it does not have a semicolon at the end, then it's not the end of the query
			if (substr(trim($line), -1, 1) != ';') {
				continue;
			}

			if (!mysqli_query($this->db, $current_query)) {
				print('Error performing query \'<strong>' . $current_query . '\': ' . mysqli_error($this->db) . '<br /><br />');
				mysqli_query($this->db, 'UNLOCK TABLES;');
				return array('status' => 'continue', 'offset' => $loop_iteration);
			}

			$current_query = $line = '';

			//check timeout after every 10 queries executed
			if ($this_lines_count <= 10) {
				continue;
			}

			$this_lines_count = 0;

			if( !$this->is_timed_out() ){
				continue;
			}

			mysqli_query($this->db, "UNLOCK TABLES;");
			fclose($handle);
			return array('status' => 'continue', 'offset' => $loop_iteration);
		}

		mysqli_query($this->db, "UNLOCK TABLES;");

		return array('status' => 'completed', 'msg' => 'Imported successfully!');
	}

	public function is_timed_out() {

		if (( time() - $this->start_time ) >= $this->import_timeout) {
			return true;
		}

		return false;
	}
}

new Import_Sql_Multicall();
