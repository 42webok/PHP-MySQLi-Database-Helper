<?php
include("config.php");

class DatabaseHelper {
    private $connection;

    public function __construct($conn) {
        $this->connection = $conn;
    }

    // Get all data from table
    public function getAll($tableName) {
        $sql = "SELECT * FROM $tableName";
        $result = mysqli_query($this->connection, $sql);
        return $this->fetchAll($result);
    }

    // Get specific columns from table
    public function getColumns($tableName, $columns = ['*']) {
        $columnList = implode(",", $columns);
        $sql = "SELECT $columnList FROM $tableName";
        $result = mysqli_query($this->connection, $sql);
        return $this->fetchAll($result);
    }

    // Get data with WHERE condition
    public function getWhere($tableName, $condition) {
        $sql = "SELECT * FROM $tableName WHERE $condition";
        $result = mysqli_query($this->connection, $sql);
        return $this->fetchAll($result);
    }

    // Get columns with WHERE condition
    public function getColumnsWhere($tableName, $columns, $condition) {
        $columnList = implode(",", $columns);
        $sql = "SELECT $columnList FROM $tableName WHERE $condition";
        $result = mysqli_query($this->connection, $sql);
        return $this->fetchAll($result);
    }

    // Search data with LIKE
    public function search($tableName, $column, $value) {
        $sql = "SELECT * FROM $tableName WHERE $column LIKE '%".$value."%'";
        $result = mysqli_query($this->connection, $sql);
        return $this->fetchAll($result);
    }

    // Get first row from table
    public function first($tableName) {
        $sql = "SELECT * FROM $tableName LIMIT 1";
        $result = mysqli_query($this->connection, $sql);
        return mysqli_fetch_assoc($result);
    }

    // Get latest records
    public function latest($tableName, $limit = 10, $orderBy = 'id') {
        $sql = "SELECT * FROM $tableName ORDER BY $orderBy DESC LIMIT $limit";
        $result = mysqli_query($this->connection, $sql);
        return $this->fetchAll($result);
    }

    // Get data between dates
    public function betweenDates($tableName, $dateColumn, $startDate, $endDate) {
        $sql = "SELECT * FROM $tableName WHERE $dateColumn BETWEEN '$startDate' AND '$endDate'";
        $result = mysqli_query($this->connection, $sql);
        return $this->fetchAll($result);
    }

    // Pagination support
    public function paginate($tableName, $perPage = 10, $page = 1) {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM $tableName LIMIT $offset, $perPage";
        $result = mysqli_query($this->connection, $sql);
        return $this->fetchAll($result);
    }

    // Group data
    public function groupBy($tableName, $groupColumn, $columns = ['*']) {
        $columnList = implode(",", $columns);
        $sql = "SELECT $columnList FROM $tableName GROUP BY $groupColumn";
        $result = mysqli_query($this->connection, $sql);
        return $this->fetchAll($result);
    }

    // Join tables
    public function join($table1, $table2, $joinCondition, $columns = ['*']) {
        $columnList = implode(",", $columns);
        $sql = "SELECT $columnList FROM $table1 JOIN $table2 ON $joinCondition";
        $result = mysqli_query($this->connection, $sql);
        return $this->fetchAll($result);
    }

    // Having clause
    public function having($tableName, $groupColumn, $havingCondition) {
        $sql = "SELECT * FROM $tableName GROUP BY $groupColumn HAVING $havingCondition";
        $result = mysqli_query($this->connection, $sql);
        return $this->fetchAll($result);
    }

    // IN clause
    public function in($tableName, $column, $values) {
        $valueList = "'" . implode("','", $values) . "'";
        $sql = "SELECT * FROM $tableName WHERE $column IN ($valueList)";
        $result = mysqli_query($this->connection, $sql);
        return $this->fetchAll($result);
    }

    // Raw query execution
    public function raw($sql) {
        $result = mysqli_query($this->connection, $sql);
        return $this->fetchAll($result);
    }

    // Helper method to fetch all rows
    private function fetchAll($result) {
        $data = [];
        while($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }
}

// Initialize the database helper
$db = new DatabaseHelper($conn);

/*******************************/
/* SAMPLE USAGE WITH TEST DATA */
/*******************************/

// 1. Get all users
$allUsers = $db->getAll('users');
echo "<pre>All Users: "; print_r($allUsers); echo "</pre>";

// 2. Get specific columns
$userEmails = $db->getColumns('users', ['id', 'email']);
echo "<pre>User Emails: "; print_r($userEmails); echo "</pre>";

// 3. Get with condition
$activeUsers = $db->getWhere('users', 'status = "active"');
echo "<pre>Active Users: "; print_r($activeUsers); echo "</pre>";

// 4. Search users
$searchResults = $db->search('users', 'username', 'john');
echo "<pre>Search Results: "; print_r($searchResults); echo "</pre>";

// 5. Get first user
$firstUser = $db->first('users');
echo "<pre>First User: "; print_r($firstUser); echo "</pre>";

// 6. Get latest 5 posts
$latestPosts = $db->latest('posts', 5, 'created_at');
echo "<pre>Latest Posts: "; print_r($latestPosts); echo "</pre>";

// 7. Get orders between dates
$recentOrders = $db->betweenDates('orders', 'order_date', '2023-01-01', '2023-01-31');
echo "<pre>January Orders: "; print_r($recentOrders); echo "</pre>";

// 8. Paginate products
$page2Products = $db->paginate('products', 10, 2);
echo "<pre>Page 2 Products: "; print_r($page2Products); echo "</pre>";

// 9. Group products by category
$productsByCategory = $db->groupBy('products', 'category_id');
echo "<pre>Products by Category: "; print_r($productsByCategory); echo "</pre>";

// 10. Join users with orders
$userOrders = $db->join('users', 'orders', 'users.id = orders.user_id');
echo "<pre>User Orders: "; print_r($userOrders); echo "</pre>";

// 11. Having clause example
$popularProducts = $db->having('order_items', 'product_id', 'COUNT(*) > 10');
echo "<pre>Popular Products: "; print_r($popularProducts); echo "</pre>";

// 12. IN clause example
$specificUsers = $db->in('users', 'id', [1, 5, 10]);
echo "<pre>Specific Users: "; print_r($specificUsers); echo "</pre>";

// 13. Raw query example
$customData = $db->raw("SELECT username, COUNT(*) as order_count 
                       FROM users JOIN orders ON users.id = orders.user_id 
                       GROUP BY users.id");
echo "<pre>Custom Query Results: "; print_r($customData); echo "</pre>";
?>
