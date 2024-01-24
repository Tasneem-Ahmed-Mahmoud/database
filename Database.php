<?php



class Database
{


    private $connection;


    function __construct($hostname, $username, $password, $database)
    {

        $this->connection = mysqli_connect($hostname, $username, $password, $database);

        if (!$this->connection) {

            $this->showConnectionError();
        }
    }

    function insert($table, $data)
    {
        if (empty($table) || !is_array($data) || empty($data)) {
            $this->showError("Invalid input for insert");
            return false;
        }
        $columns = implode(",", array_keys($data));
        $values = "'" . implode("','", array_values($data)) . "'";

        $sql = "INSERT INTO $table($columns)VALUES($values)";
        if (mysqli_query($this->connection, $sql)) {
            return true;
        } else {
            $this->showError(mysqli_error($this->connection));
        }
    }

    function update($table, $data, $where)
    {
        if (empty($table) || !is_array($data) || empty($data) || empty($where)) {
            $this->showError("Invalid input for update");
            return false;
        }
        $setClause = '';
        foreach ($data as $key => $value) {
            $setClause .= "`$key`='$value',";
        }
        $setClause = rtrim($setClause, ',');

        $whereClause = '';
        foreach ($where as $key => $value) {
            $whereClause .= "`$key`='$value'AND";
        }
        $whereClause = rtrim($whereClause, 'AND');
        $sql = "UPDATE $table SET $setClause WHERE $whereClause";
        if (mysqli_query($this->connection, $sql)) {
            return true;
        } else {
            $this->showError(mysqli_error($this->connection));
        }
    }

    public function delete($table, $where)
    {
        if (empty($table)  || empty($where)) {
            $this->showError("Invalid input for delete");
            return false;
        }

        $whereClause = '';
        foreach ($where as $key => $value) {
            $whereClause .= "`$key`='$value'AND";
        }
        $whereClause = rtrim($whereClause, 'AND');
        $sql = "DELETE FROM `$table` WHERE  $whereClause";
        if (mysqli_query($this->connection, $sql)) {
            return true;
        } else {
            $this->showError(mysqli_error($this->connection));
        }
    }

    public function getRow($table, $where)
    {

        if (empty($table) || empty($where)) {
            $this->showError("Invalid input for getRow");
            return null;
        }
        $whereClause = '';
        foreach ($where as $key => $value) {
            $whereClause .= "`$key`='$value'AND";
        }
        $whereClause = rtrim($whereClause, 'AND');
        $sql = "SELECT * FROM `$table` WHERE $whereClause LIMIT 1";
        $result=mysqli_query($this->connection, $sql);
        if ($result === false) {
            $this->showError($this->connection->error);
            return null;
        }

       return  $result->fetch_assoc();
       
    }

    public function lastInsertedID() {
        return $this->connection->insert_id;
    }

    public function getAll($table) {
        if (empty($table)) {
            $this->showError("Invalid input for getAll");
            return null;
        }
        $sql = "SELECT * FROM `$table`";
        $result=mysqli_query($this->connection, $sql);
        if ($result === false) {
            $this->showError($this->connection->error);
            return null;
        }
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
    
    private function showConnectionError()
    {
        die("Connect failed: " . mysqli_connect_error());
    }
    public function showError($error)
    {
        echo "Error: " . $error;
    }
}

$database = new Database("localhost", "root", "", "oop_test");

$data = [
    "name" => "zara",
"email"=>"zara@.com",
    "salary" => 20009
];
$where = [
    "id" => 4,

];
// $database->insert("users",$data);
// $database->update("users", $data, $where);
// $database->delete("users",$where);
// print_r($database->getRow("users",$where));
// echo $database->lastInsertedID();

print_r($database->getAll("users"));