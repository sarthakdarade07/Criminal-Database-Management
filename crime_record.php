<?php
$servername = "localhost";
$username = "root";
$password = "sarthak@123?";
$dbname = "criminal";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$message = "";
$search_results = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $action = $_POST['action'] ?? '';
  $crime_id = $_POST['crime_id'] ?? '';

    if ($action == "add") {
        $crime_id = $_POST['crime_id'];
        $criminal_id = $_POST['criminal_id'];
        $crime_type = $_POST['crime_type'];
        $crime_date = $_POST['crime_date'];
        $victim_id = $_POST['victim_id'];

        // Use the stored procedure
        $stmt = $conn->prepare("CALL AddCrimeRecord(?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $crime_id, $criminal_id, $crime_type, $crime_date, $victim_id);

        if ($stmt->execute()) {
            $message = "Crime record added via stored procedure!";
        } else {
            $message = "Error adding crime record: " . $conn->error;
        }

        $stmt->close();
    }

  if ($action == "update") {
    $criminal_id = $_POST['criminal_id'];
    $crime_type = $_POST['crime_type'];
    $crime_date = $_POST['crime_date'];
    $victim_id = $_POST['victim_id'];

    $crime_date_sql = empty($crime_date) ? "NULL" : "'$crime_date'";

    $sql = "SELECT * FROM Crime_Record WHERE crime_id='$crime_id'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
      $sql = "UPDATE Crime_Record SET criminal_id='$criminal_id', crime_type='$crime_type', crime_date=$crime_date_sql, victim_id='$victim_id' WHERE crime_id='$crime_id'";
      $message = ($conn->query($sql) === TRUE) ? "Record updated successfully!" : "Error updating record: " . $conn->error;
    } else {
      $message = "Record not found!" . $conn->error;
    }
  }

  if ($action == "delete") {
    $sql = "SELECT * FROM Crime_Record WHERE crime_id = '$crime_id'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
      $sql = "DELETE FROM Crime_Record WHERE crime_id = '$crime_id'";
      $message = ($conn->query($sql) === TRUE) ? "Record deleted successfully!" : "Error deleting record: " . $conn->error;
    } else {
      $message = "Record not found!";
    }
  }

  if ($action == "search") {
    $search_query = $_POST['search_query'];
    $sql = "SELECT * FROM Crime_Record WHERE crime_id = '$search_query' OR crime_type LIKE '%$search_query%'";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
      $search_results = $result->fetch_all(MYSQLI_ASSOC);
    } else {
      $message = "No matching records found.";
    }
  }

  if ($action == "show_all") {
    $sql = "SELECT * FROM Crime_Record";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
      $search_results = $result->fetch_all(MYSQLI_ASSOC);
    } else {
      $message = "No matching records found.";
    }
  }

  if ($action == "total_count") {
    $sql2 = "SELECT * FROM Crime_Record";
    $result2 = $conn->query($sql2);

    if ($result2 && $result2->num_rows > 0) {
      $search_results = $result2->fetch_all(MYSQLI_ASSOC);
    }

    $sql = "SELECT COUNT(*) AS Total_Count FROM Crime_Record";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $totalCount = "Total number of crime records: " . $row['Total_Count'];
    }
  }

  if ($action == "filter" || isset($_POST['filter_option'])) {
    $filter = $_POST['filter_option'];

    if ($filter == "id") {
      $sql = "SELECT * FROM Crime_Record ORDER BY crime_id ASC";
    } elseif ($filter == "date") {
      $sql = "SELECT * FROM Crime_Record ORDER BY crime_date ASC";
    }

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
      $search_results = $result->fetch_all(MYSQLI_ASSOC);
    } else {
      $message = "No records found.";
    }
  }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Crime Record Management</title>
  <style>
    body {
      font-family: Arial;
      background-color: #f4f4f4;
      padding: 20px;
    }
    h2 {
      color: #333;
    }
    form {
      margin-bottom: 20px;
      background-color: white;
      padding: 15px;
      border-radius: 8px;
    }
    input[type=text], input[type=date] {
      padding: 6px;
      margin: 4px;
      width: 200px;
    }
    input[type=submit], button {
      padding: 6px 12px;
      margin: 4px;
      background-color: #007BFF;
      color: white;
      border: none;
      cursor: pointer;
    }
    input[type=submit]:hover, button:hover {
      background-color: #0056b3;
    }
    table {
      border-collapse: collapse;
      width: 100%;
      background-color: white;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 8px;
      text-align: left;
    }
    th {
      background-color: #eee;
    }
    .message {
      margin: 10px 0;
      color: green;
    }
  </style>
</head>
<body>

<h2>Crime Record Management</h2>

<div class="message"><?php echo $message; ?></div>

<form method="post">
  <input type="hidden" name="action" value="add">
  <h3>Add Crime Record</h3>
  Crime ID: <input type="text" name="crime_id" required>
  Criminal ID: <input type="text" name="criminal_id" required>
  Crime Type: <input type="text" name="crime_type" required>
  Crime Date: <input type="date" name="crime_date">
  Victim ID: <input type="text" name="victim_id" required>
  <input type="submit" value="Add">
</form>

<form method="post">
  <input type="hidden" name="action" value="update">
  <h3>Update Crime Record</h3>
  Crime ID (to update): <input type="text" name="crime_id" required>
  New Criminal ID: <input type="text" name="criminal_id">
  New Crime Type: <input type="text" name="crime_type">
  New Crime Date: <input type="date" name="crime_date">
  New Victim ID: <input type="text" name="victim_id">
  <input type="submit" value="Update">
</form>

<form method="post">
  <input type="hidden" name="action" value="delete">
  <h3>Delete Crime Record</h3>
  Crime ID: <input type="text" name="crime_id" required>
  <input type="submit" value="Delete">
</form>

<form method="post">
  <input type="hidden" name="action" value="search">
  <h3>Search Crime Record</h3>
  Search by ID or Crime Type: <input type="text" name="search_query">
  <input type="submit" value="Search">
</form>

<form method="post" style="display:inline-block;">
  <input type="hidden" name="action" value="show_all">
  <input type="submit" value="Show All Records">
</form>

<form method="post" style="display:inline-block;">
  <input type="hidden" name="action" value="total_count">
  <input type="submit" value="Show Total Count">
</form>

<form method="post" style="display:inline-block;">
  <input type="hidden" name="action" value="filter">
  Filter:
  <select name="filter_option">
    <option value="id">By ID</option>
    <option value="date">By Date</option>
  </select>
  <input type="submit" value="Apply Filter">
</form>

<?php if (!empty($totalCount)) { echo "<p>$totalCount</p>"; } ?>

<?php if (!empty($search_results)): ?>
  <h3>Results:</h3>
  <table>
    <tr>
      <th>Crime ID</th>
      <th>Criminal ID</th>
      <th>Crime Type</th>
      <th>Crime Date</th>
      <th>Victim ID</th>
    </tr>
    <?php foreach ($search_results as $row): ?>
      <tr>
        <td><?= $row['crime_id'] ?></td>
        <td><?= $row['criminal_id'] ?></td>
        <td><?= $row['crime_type'] ?></td>
        <td><?= $row['crime_date'] ?></td>
        <td><?= $row['victim_id'] ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>

</body>
</html>
