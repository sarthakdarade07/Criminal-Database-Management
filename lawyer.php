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
  $lawyer_id = $_POST['lawyer_id'] ?? '';

  if ($action == "add") {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $case_id = $_POST['case_id'];

    $sql = "INSERT INTO Lawyer (lawyer_id, name, contact, case_id) VALUES ('$lawyer_id', '$name', '$contact', '$case_id')";
    $message = ($conn->query($sql) === TRUE) ? "Record added successfully!" : "Error: " . $conn->error;
  }

  if ($action == "update") {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $case_id = $_POST['case_id'];

    $sql = "SELECT * FROM Lawyer WHERE lawyer_id='$lawyer_id'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
      $sql = "UPDATE Lawyer SET name='$name', contact='$contact', case_id='$case_id' WHERE lawyer_id='$lawyer_id'";
      $message = ($conn->query($sql) === TRUE) ? "Record updated successfully!" : "Error updating record: " . $conn->error;
    } else {
      $message = "Record not found!";
    }
  }

  if ($action == "delete") {
    $sql = "SELECT * FROM Lawyer WHERE lawyer_id = '$lawyer_id'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
      $sql = "DELETE FROM Lawyer WHERE lawyer_id = '$lawyer_id'";
      $message = ($conn->query($sql) === TRUE) ? "Record deleted successfully!" : "Error deleting record: " . $conn->error;
    } else {
      $message = "Record not found!";
    }
  }

  if ($action == "search") {
    $search_query = $_POST['search_query'];
    $sql = "SELECT * FROM Lawyer WHERE lawyer_id = '$search_query' OR name LIKE '%$search_query%'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
      $search_results = $result->fetch_all(MYSQLI_ASSOC);
    } else {
      $message = "No matching records found.";
    }
  }

  if ($action == "show_all") {
    $sql = "SELECT * FROM Lawyer";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
      $search_results = $result->fetch_all(MYSQLI_ASSOC);
    } else {
      $message = "No matching records found.";
    }
  }

  if ($action == "filter" || isset($_POST['filter_option'])) {
    $filter = $_POST['filter_option'];

    if ($filter == "id") {
      $sql = "SELECT * FROM Lawyer ORDER BY lawyer_id ASC";
    } elseif ($filter == "name") {
      $sql = "SELECT * FROM Lawyer ORDER BY name ASC";
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
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  <link rel="stylesheet" href="criminal-css.css" />
  <title>Lawyer Record Management</title>
  <style>
    #message-box {
      transition: opacity 1s step-end;
    }

    .search-form {
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 20px 0;
      gap: 10px;
    }

    .search-form input {
      padding: 5px;
    }

    .search-form button {
      padding: 6px 10px;
      cursor: pointer;
    }

    .result_table {
      padding: auto;
    }

    .result_th {
      width: 60vw;
    }
  </style>
</head>
<body>
  <div id="container">
    <h1 id="center-heading" style="font-weight: bolder">Lawyer Record Management</h1>

    <p id="center-msg">
      Manage all lawyer information in one place. Add, update, delete, and search records easily to streamline legal case tracking.
    </p>

    <?php if (!empty($message)): ?>
      <div class="w3-panel w3-green w3-padding" id="message-box">
        <p><?php echo $message; ?></p>
      </div>
      <script>
        setTimeout(function () {
          var msg = document.getElementById("message-box");
          if (msg) {
            msg.style.opacity = "0";
            setTimeout(() => msg.style.display = "none", 500);
          }
        }, 3000);
      </script>
    <?php endif; ?>

    <div class="crudBtnDiv">
      <div class="box" style="border-left: none">
        <form action="" method="post">
          <label class="label1">Lawyer ID*</label>
          <input type="text" name="lawyer_id" placeholder="Lawyer ID" required /><br /><br />
          <label class="label1">Name*</label>
          <input type="text" name="name" placeholder="Name" required /><br /><br />
          <label class="label1">Contact*</label>
          <input type="text" name="contact" placeholder="Contact" required /><br /><br />
          <label class="label1">Case ID*</label>
          <input type="text" name="case_id" placeholder="Case ID" required /><br /><br />
          <input type="submit" name="action" value="add" class="crudBtn" />
        </form>
      </div>

      <div class="box">
        <form action="" method="post">
          <label class="label1">Lawyer ID*</label>
          <input type="text" name="lawyer_id" placeholder="Lawyer ID" required /><br /><br />
          <label class="label1">Name*</label>
          <input type="text" name="name" placeholder="Name" required /><br /><br />
          <label class="label1">Contact*</label>
          <input type="text" name="contact" placeholder="Contact" required /><br /><br />
          <label class="label1">Case ID*</label>
          <input type="text" name="case_id" placeholder="Case ID" required /><br /><br />
          <input type="submit" name="action" value="update" class="crudBtn" />
        </form>
      </div>

      <div class="box">
        <form action="" method="post">
          <label class="label1">Lawyer ID*</label>
          <input type="text" name="lawyer_id" placeholder="Lawyer ID" required /><br /><br />
          <input type="submit" name="action" value="delete" class="crudBtn" />
        </form>
      </div>
    </div>

    <form class="search-form" method="post">
      <input type="text" name="search_query" placeholder="Enter ID or Name to search" required style="width:50vw" />
      <button type="submit" name="action" value="search">
        <i class="fa fa-search"></i>
      </button>
    </form>

    <div style="justify-content: center; display: flex; gap: 10px; margin-top: 20px;">
      <form action="" class="search-form" method="post" style="margin-top: -5px;">
        <button type="submit" name="action" value="show_all" class="show-list">Show All</button>
        <select name="filter_option" class="filter" onchange="this.form.submit()">
          <option value="">Filter</option>
          <option value="id">Sort by ID</option>
          <option value="name">Sort by Name</option>
        </select>
      </form>
    </div>

    <?php if (!empty($search_results)): ?>
      <h3>Search Results</h3>
      <table class="result_table" border="1">
        <thead>
          <tr style="background-color: #28a745;">
            <th>Lawyer ID</th>
            <th>Name</th>
            <th>Contact</th>
            <th>Case ID</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($search_results as $row): ?>
            <tr>
              <td class="result_th"><?= htmlspecialchars($row['lawyer_id'] ?? 'N/A') ?></td>
              <td class="result_th"><?= htmlspecialchars($row['name'] ?? 'N/A') ?></td>
              <td class="result_th"><?= htmlspecialchars($row['contact'] ?? 'N/A') ?></td>
              <td class="result_th"><?= htmlspecialchars($row['case_id'] ?? 'N/A') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</body>
</html>
