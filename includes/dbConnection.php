<?php
//db connection.php
class dbConnection {
    private $connection;
    private $db_type;
    private $db_host;
    private $db_port;
    private $db_user;
    private $db_pass;
    private $db_name;

    public function __construct($db_type, $db_host, $db_port, $db_user, $db_pass, $db_name) {
        $this->db_type = $db_type;
        $this->db_host = $db_host;
        $this->db_port = $db_port;
        $this->db_user = $db_user;
        $this->db_pass = $db_pass;
        $this->db_name = $db_name;
        $this->db_name = 'bootstrap';

        $this->connection = $this->connect();
        if ($this->connection === null) {
            die("Failed to establish a database connection.");
        }
    }

    // Function to establish the connection
    private function connect() {
        switch ($this->db_type) {
            case 'PDO':
                try {
                    $dsn = "mysql:host={$this->db_host};dbname={$this->db_name}";
                    if ($this->db_port !== null) {
                        $dsn .= ";port={$this->db_port}";
                    }
                    $pdo = new PDO($dsn, $this->db_user, $this->db_pass);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    return $pdo;
                } catch (PDOException $e) {
                    echo "PDO connection failed: " . $e->getMessage();
                    return null;
                }

            case 'MySQLi':
                $mysqli = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name, $this->db_port);
                if ($mysqli->connect_error) {
                    echo "MySQLi connection failed: " . $mysqli->connect_error;
                    return null;
                }
                return $mysqli;

            default:
                echo "Unsupported database type.";
                return null;
        }
    }

    /**************************************************************************************************
     * Escape values method
     ***************************************************************************************************/
    public function escape_values($posted_values): string {
        if ($this->connection === null) {
            die("No database connection available.");
        }

        switch ($this->db_type) {
            case 'PDO':
                return addslashes($posted_values);
            case 'MySQLi':
                return $this->connection->real_escape_string($posted_values);
        }
    }

    /**************************************************************************************************
     * Count returned results method
     ***************************************************************************************************/
    public function count_results($sql) {
        if ($this->connection === null) {
            die("No database connection available.");
        }

        switch ($this->db_type) {
            case 'PDO':
                $stmt = $this->connection->prepare($sql);
                $stmt->execute();
                return $stmt->rowCount();
            case 'MySQLi':
                $result = $this->connection->query($sql);
                if (is_object($result)) {
                    return $result->num_rows;
                } else {
                    echo "Error: " . $this->connection->error;
                }
        }
    }

    /**************************************************************************************************
     * Insert query method
     ***************************************************************************************************/
    public function insert($table, $data) {
        ksort($data);
        $fieldNames = implode('`, `', array_keys($data));
        $fieldValues = implode("', '", array_values($data));
        $sql = "INSERT INTO `$table` (`$fieldNames`) VALUES ('$fieldValues')";
        return $this->execute_query($sql);
    }

    /**************************************************************************************************
     * Select query method
     ***************************************************************************************************/
    public function select($sql) {
        if ($this->connection === null) {
            die("No database connection available.");
        }

        switch ($this->db_type) {
            case 'PDO':
                $stmt = $this->connection->prepare($sql);
                $stmt->execute();
                return $stmt->fetch(PDO::FETCH_ASSOC);
            case 'MySQLi':
                $result = $this->connection->query($sql);
                return $result->fetch_assoc();
        }
    }

    /**************************************************************************************************
     * Select while loop query method
     ***************************************************************************************************/
    public function select_while($sql) {
        if ($this->connection === null) {
            die("No database connection available.");
        }

        switch ($this->db_type) {
            case 'PDO':
                $stmt = $this->connection->prepare($sql);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            case 'MySQLi':
                $result = $this->connection->query($sql);
                $data = [];
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                return $data;
        }
    }

    /**************************************************************************************************
     * Update query method
     ***************************************************************************************************/
    public function update($table, $data, $where) {
        ksort($data);
        $fieldDetails = implode(', ', array_map(fn($key, $value) => "$key='$value'", array_keys($data), array_values($data)));

        $whereClause = is_array($where) ? implode(' AND ', array_map(fn($key, $value) => "$key='$value'", array_keys($where), array_values($where))) : $where;

        $sql = "UPDATE `$table` SET $fieldDetails WHERE $whereClause";
        return $this->execute_query($sql);
    }

    /**************************************************************************************************
     * Delete query method
     ***************************************************************************************************/
    public function delete($table, $where) {
        $whereClause = is_array($where) ? implode(' AND ', array_map(fn($key, $value) => "$key='$value'", array_keys($where), array_values($where))) : $where;

        $sql = "DELETE FROM `$table` WHERE $whereClause";
        return $this->execute_query($sql);
    }

    /**************************************************************************************************
     * Truncate table method
     ***************************************************************************************************/
    public function truncate($table) {
        $sql = "TRUNCATE TABLE `$table`";
        return $this->execute_query($sql);
    }

    /**************************************************************************************************
     * Get last inserted ID method
     ***************************************************************************************************/
    public function last_id() {
        if ($this->db_type == 'PDO') {
            return $this->connection->lastInsertId();
        } elseif ($this->db_type == 'MySQLi') {
            return $this->connection->insert_id;
        }
    }

    /**************************************************************************************************
     * Execute query helper method
     ***************************************************************************************************/
    private function execute_query($sql) {
        if ($this->connection === null) {
            die("No database connection available.");
        }

        switch ($this->db_type) {
            case 'PDO':
                try {
                    $stmt = $this->connection->prepare($sql);
                    $stmt->execute();
                    return true;
                } catch (PDOException $e) {
                    echo "Error executing query: " . $e->getMessage();
                    return false;
                }

            case 'MySQLi':
                if ($this->connection->query($sql) === true) {
                    return true;
                } else {
                    echo "Error: " . $this->connection->error;
                    return false;
                }
        }
    }
}
