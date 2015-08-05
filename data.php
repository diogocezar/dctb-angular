<?php
class DataBase{
	private $type;
	private $host;
	private $base;
	private $user;
	private $pass;
	private $conn_string;
	private $conn;

	public function __construct(){
		$this->type = "mysql";
		$this->user = "root";
		$this->host = "localhost";
		$this->base = "****";
		$this->pass = "****";

		$this->conn_string  = $this->type;
		$this->conn_string .= ":host="   . $this->host;
		$this->conn_string .= ";dbname=" . $this->base;

		$this->conn = new PDO($this->conn_string, $this->user, $this->pass);
	}

	// Other Examples

	public function get_types(){
		$sql = "SELECT id, name FROM type";
		$rs  = $this->conn->query($sql);
		$i   = 0;
		while($data = $rs->fetch(PDO::FETCH_OBJ)){
			$array[$i]['id']   = $data->id;
			$array[$i]['name'] = $data->name;
			$i++;
		}
		echo "{ \"records\" : " . json_encode($array) . " } ";
	}

	// User Example

	public function get_user(){
		$sql = "SELECT id, name, email, date_update, date_create FROM user WHERE id = ?";
		$rs  = $this->conn->prepare($sql);
		$rs->bindParam(1, (empty($_GET['id']) ? 1 : $_GET['id']));
		if($rs->execute()){
			if($rs->rowCount() > 0){
				while($data = $rs->fetch(PDO::FETCH_OBJ)){
					$array['Id']         = $data->id;
					$array['Name']       = $data->name;
					$array['Email']      = $data->email;
					$array['DateCreate'] = $data->date_create;
					$array['DateUpdate'] = $data->date_update;
				}
			}
		}
		echo "{ \"record\" : " . json_encode($array) . " } ";
	}

	public function get_user_list(){
		$sql = "SELECT id, name, email, date_update, date_create FROM user where removed = 0";
		$rs  = $this->conn->query($sql);
		$i   = 0;
		if($rs->execute()){
			if($rs->rowCount() > 0){
				while($data = $rs->fetch(PDO::FETCH_OBJ)){
					$array[$i]['Id']         = $data->id;
					$array[$i]['Name']       = $data->name;
					$array[$i]['Email']      = $data->email;
					$array[$i]['DateCreate'] = $data->date_create;
					$array[$i]['DateUpdate'] = $data->date_update;
					$i++;
				}
			}
		}
		echo "{ \"record\" : " . json_encode($array) . " } ";
	}

	public function show_query($query, $params){
		$keys = array();
		$values = array();
		foreach ($params as $key=>$value){
			if (is_string($key)){
				$keys[] = '/:'.$key.'/';
			}
			else{
				$keys[] = '/[?]/';
			}
			if(is_numeric($value)){
				$values[] = intval($value);
			}
			else{
				$values[] = '"'.$value .'"';
			}
		}
		$query = preg_replace($keys, $values, $query, 1, $count);
		return $query;
	}

	public function remove(){
		$id = $_POST['Id'];
		$sql = "UPDATE USER SET removed = 1 WHERE id = ?";
		$rs = $this->conn->prepare($sql);
		$rs->bindParam(1, $id);
		$rs->execute();
		echo "Registro removido com sucesso!";
	}

	public function save(){
		$id    = $_POST['Id'];
		$name  = $_POST['Name'];
		$email = $_POST['Email'];
		$pwd   = $_POST['Pwd1'];
		if($id == 0){
			$date_create = date('Y-m-d H:i:s');
			$date_update = date('Y-m-d H:i:s');
			$sql = "INSERT INTO `user` (`id`, `id_user_type`, `name`, `email`, `password`, `picture`, `guid`, `id_user_modified`, `date_update`, `date_create`, `removed`) VALUES (NULL, 1, ?, ?, ?, NULL, NULL, 1, ?, ?, 0)";
			$rs = $this->conn->prepare($sql);
			$rs->bindParam(1, $name);
			$rs->bindParam(2, $email);
			$rs->bindParam(3, $pwd);
			$rs->bindParam(4, $date_update);
			$rs->bindParam(5, $date_create);
			$rs->execute();
		}
		else{
			$date_update = date('Y-m-d H:i:s');
			$sql = "UPDATE user SET name = ?, email = ?, password = ?, date_update = ? WHERE id = ?";
			$rs = $this->conn->prepare($sql);
			$rs->bindParam(1, $name);
			$rs->bindParam(2, $email);
			$rs->bindParam(3, $pwd);
			$rs->bindParam(4, $date_update);
			$rs->bindParam(5, $id);
			$rs->execute();
		}
		echo "Registro salvo com sucesso!";
	}
}

if(isset($_GET['method'])){
	$method = $_GET['method'];
	if(!empty($method)){
		$obj = new DataBase();
		$obj->{$method}();
	}
}
?>
