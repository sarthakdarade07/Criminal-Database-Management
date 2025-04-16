<?php
$servername = "localhost";
$username = "root";
$password = "sarthak@123?";
$dbname = "criminal";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$message = "";
$search_results = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $action = $_POST['action'] ?? '';
  $hearing_id = $_POST['hearing_id'] ?? '';

  if ($action == "add") {
    $case_id = $_POST['case_id'];
    $hearing_date = $_POST['hearing_date'];
    $judge_name = $_POST['judge_name'];

    $hearing_date_sql = empty($hearing_date) ? "NULL" : "'$hearing_date'";
    $sql = "INSERT INTO Court_Hearing (hearing_id, case_id, hearing_date, judge_name) 
            VALUES ('$hearing_id', '$case_id', $hearing_date_sql, '$judge_name')";
    $message = ($conn->query($sql) === TRUE) ? "Record added successfully!" : "Error: " . $conn->error;
  }

  if ($action == "update") {
    $case_id = $_POST['case_id'];
    $hearing_date = $_POST['hearing_date'];
    $judge_name = $_POST['judge_name'];

    $hearing_date_sql = empty($hearing_date) ? "NULL" : "'$hearing_date'";

    $sql = "SELECT * FROM Court_Hearing WHERE hearing_id='$hearing_id'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
      $sql = "UPDATE Court_Hearing SET case_id='$case_id', hearing_date=$hearing_date_sql, judge_name='$judge_name'
              WHERE hearing_id='$hearing_id'";
      $message = ($conn->query($sql) === TRUE) ? "Record updated successfully!" : "Error updating record: " . $conn->error;
    } else {
      $message = "Record not found!";
    }
  }

  if ($action == "delete") {
    $sql = "SELECT * FROM Court_Hearing WHERE hearing_id = '$hearing_id'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
      $sql = "DELETE FROM Court_Hearing WHERE hearing_id = '$hearing_id'";
      $message = ($conn->query($sql) === TRUE) ? "Record deleted successfully!" : "Error deleting record: " . $conn->error;
    } else {
      $message = "Record not found!";
    }
  }

  if ($action == "search") {
    $search_query = $_POST['search_query'];
    $sql = "SELECT * FROM Court_Hearing WHERE hearing_id = '$search_query' OR case_id LIKE '%$search_query%'";

    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
      $search_results = $result->fetch_all(MYSQLI_ASSOC);
    } else {
      $message = "No matching records found.";
    }
  }

  if ($action == "show_all") {
    $sql = "SELECT * FROM Court_Hearing";
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
      $sql = "SELECT * FROM Court_Hearing ORDER BY hearing_id ASC";
    } elseif ($filter == "date") {
      $sql = "SELECT * FROM Court_Hearing ORDER BY hearing_date ASC";
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
  <title>Court Hearing Management</title>
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
    <h1 id="center-heading" style="font-weight: bolder">Court Hearing Record Management</h1>

    <p id="center-msg">
      Efficiently manage and track court hearings linked with criminal cases. Add, update, and search hearings with ease. Keep your hearing records accurate and organized.
    </p>

    <!-- Success/Error Message -->
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

    <!-- Forms -->
    <div class="crudBtnDiv">
      <div class="box" style="border-left: none">
        <form action="" method="post">
          <label class="label1">Hearing ID*</label>
          <input type="text" name="hearing_id" placeholder="Hearing ID" required /><br /><br />
          <label class="label1">Case ID*</label>
          <input type="text" name="case_id" placeholder="Case ID" required /><br /><br />
          <label class="label1">Hearing Date</label>
          <input type="date" name="hearing_date" /><br /><br />
          <label class="label1">Judge Name*</label>
          <input type="text" name="judge_name" placeholder="Judge Name" required /><br /><br />
          <input type="submit" name="action" value="add" class="crudBtn" />
        </form>
      </div>

      <div class="box">
        <form action="" method="post">
          <label class="label1">Hearing ID*</label>
          <input type="text" name="hearing_id" placeholder="Hearing ID" required /><br /><br />
          <label class="label1">Case ID*</label>
          <input type="text" name="case_id" placeholder="Case ID" required /><br /><br />
          <label class="label1">Hearing Date</label>
          <input type="date" name="hearing_date" /><br /><br />
          <label class="label1">Judge Name*</label>
          <input type="text" name="judge_name" placeholder="Judge Name" required /><br /><br />
          <input type="submit" name="action" value="update" class="crudBtn" />
        </form>
      </div>

      <div class="box">
        <form action="" method="post">
          <label class="label1">Hearing ID*</label>
          <input type="text" name="hearing_id" placeholder="Hearing ID" required /><br /><br />
          <input type="submit" name="action" value="delete" class="crudBtn" />
        </form>
      </div>
    </div>

    <!-- Search Form -->
    <form class="search-form" method="post">
      <input type="text" name="search_query" placeholder="Enter ID or Case ID to search" required style="width:50vw" />
      <button type="submit" name="action" value="search">
        <i class="fa fa-search"></i>
      </button>
    </form>

    <!-- Footer Buttons -->
    <div style="justify-content: center; display: flex; gap: 10px; margin-top: 20px;">
      <form action="" class="search-form" method="post" style="margin-top: -5px;">
        <button type="submit" name="action" value="show_all" class="show-list"> Show All </button>
        <select name="filter_option" class="filter" onchange="this.form.submit()">
          <option value="">Filter</option>
          <option value="id">Sort by Hearing ID</option>
          <option value="date">Sort by Hearing Date</option>
        </select>
      </form>
    </div>

    <!-- Search Results Table -->
    <?php if (!empty($search_results)): ?>
      <h3>Search Results</h3>
      <table class="result_table" border="1">
        <thead>
          <tr style="background-color: #28a745;">
            <th>Hearing ID</th>
            <th>Case ID</th>
            <th>Hearing Date</th>
            <th>Judge Name</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($search_results as $row): ?>
            <tr>
              <td class="result_th"><?= htmlspecialchars($row['hearing_id'] ?? 'N/A') ?></td>
              <td class="result_th"><?= htmlspecialchars($row['case_id'] ?? 'N/A') ?></td>
              <td class="result_th"><?= htmlspecialchars($row['hearing_date'] ?? 'N/A') ?></td>
              <td class="result_th"><?= htmlspecialchars($row['judge_name'] ?? 'N/A') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</body>
</html>
