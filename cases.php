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
  $case_id = $_POST['case_id'] ?? '';

  if ($action == "add") {
    $criminal_id = $_POST['criminal_id'];
    $court_name = $_POST['court_name'];
    $judge_name = $_POST['judge_name'];
    $case_status = $_POST['case_status'];
    $police_id = $_POST['police_id'];

    $sql = "INSERT INTO Case_Details (case_id, criminal_id, court_name, judge_name, case_status, police_id) 
            VALUES ('$case_id', '$criminal_id', '$court_name', '$judge_name', '$case_status', '$police_id')";
    $message = ($conn->query($sql) === TRUE) ? "Record added successfully!" : "Error: " . $conn->error;
  }

  if ($action == "update") {
    $criminal_id = $_POST['criminal_id'];
    $court_name = $_POST['court_name'];
    $judge_name = $_POST['judge_name'];
    $case_status = $_POST['case_status'];
    $police_id = $_POST['police_id'];

    $sql = "SELECT * FROM Case_Details WHERE case_id='$case_id'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
      $sql = "UPDATE Case_Details SET criminal_id='$criminal_id', court_name='$court_name', judge_name='$judge_name', 
              case_status='$case_status', police_id='$police_id' WHERE case_id='$case_id'";
      $message = ($conn->query($sql) === TRUE) ? "Record updated successfully!" : "Error updating record: " . $conn->error;
    } else {
      $message = "Record not found!";
    }
  }

  if ($action == "delete") {
    $sql = "SELECT * FROM Case_Details WHERE case_id = '$case_id'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
      $sql = "DELETE FROM Case_Details WHERE case_id = '$case_id'";
      $message = ($conn->query($sql) === TRUE) ? "Record deleted successfully!" : "Error deleting record: " . $conn->error;
    } else {
      $message = "Record not found!";
    }
  }

  if ($action == "search") {
    $search_query = $_POST['search_query'];
    $sql = "SELECT * FROM Case_Details WHERE case_id = '$search_query' OR court_name LIKE '%$search_query%'";

    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
      $search_results = $result->fetch_all(MYSQLI_ASSOC);
    } else {
      $message = "No matching records found.";
    }
  }

  if ($action == "show_all") {
    $sql = "SELECT * FROM Case_Details";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
      $search_results = $result->fetch_all(MYSQLI_ASSOC);
    } else {
      $message = "No matching records found.";
    }
  }

  if ($action == "total_count") {
    $sql2 = "SELECT * FROM Case_Details";
    $result2 = $conn->query($sql2);

    if ($result2 && $result2->num_rows > 0) {
      $search_results = $result2->fetch_all(MYSQLI_ASSOC);
    }

    $sql = "SELECT COUNT(*) AS Total_Count FROM Case_Details";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $totalCount = "Total number of cases: " . $row['Total_Count'];
    }
  }

  if ($action == "filter" || isset($_POST['filter_option'])) {
    $filter = $_POST['filter_option'];

    if ($filter == "id") {
      $sql = "SELECT * FROM Case_Details ORDER BY case_id ASC";
    } elseif ($filter == "status") {
      $sql = "SELECT * FROM Case_Details ORDER BY case_status ASC";
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

<!-- HTML part below remains almost the same with updated field names -->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
  <link rel="stylesheet" href="cases-css.css" />
  <title>Case Management</title>
</head>

<body>
  <div id="container">
    <h1 id="center-heading" style="font-weight: bolder">Case Management</h1>
    <p id="center-msg">Easily manage and maintain criminal case records.</p>

    <?php if (!empty($message)): ?>
      <div class="w3-panel w3-green w3-padding" id="message-box">
        <p><?php echo $message; ?></p>
      </div>
      <script>
        setTimeout(() => {
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
          <label class="label1">Case ID*</label>
          <input type="text" name="case_id" required /><br /><br />
          <label class="label1">Criminal ID*</label>
          <input type="text" name="criminal_id" required /><br /><br />
          <label class="label1">Court Name*</label>
          <input type="text" name="court_name" required /><br /><br />
          <label class="label1">Judge Name*</label>
          <input type="text" name="judge_name" required /><br /><br />
          <label class="label1">Case Status*</label>
          <input type="text" name="case_status" required /><br /><br />
          <label class="label1">Police ID*</label>
          <input type="text" name="police_id" required /><br /><br />
          <input type="submit" name="action" value="add" class="crudBtn" />
        </form>
      </div>

      <div class="box">
        <form action="" method="post">
          <label class="label1">Case ID*</label>
          <input type="text" name="case_id" required /><br /><br />
          <label class="label1">Criminal ID*</label>
          <input type="text" name="criminal_id" required /><br /><br />
          <label class="label1">Court Name*</label>
          <input type="text" name="court_name" required /><br /><br />
          <label class="label1">Judge Name*</label>
          <input type="text" name="judge_name" required /><br /><br />
          <label class="label1">Case Status*</label>
          <input type="text" name="case_status" required /><br /><br />
          <label class="label1">Police ID*</label>
          <input type="text" name="police_id" required /><br /><br />
          <input type="submit" name="action" value="update" class="crudBtn" />
        </form>
      </div>

      <div class="box">
        <form action="" method="post">
          <label class="label1">Case ID*</label>
          <input type="text" name="case_id" required /><br /><br />
          <input type="submit" name="action" value="delete" class="crudBtn" />
        </form>
      </div>
    </div>

    <form class="search-form" method="post">
      <input type="text" name="search_query" placeholder="Enter ID or Court Name to search" required
        style="width:50vw" />
      <button type="submit" name="action" value="search"><i class="fa fa-search"></i></button>
    </form>

    <div style="justify-content: center; display: flex; gap: 10px; margin-top: 20px;">
      <form action="" class="search-form" method="post" style="margin-top: -5px;">
        <button type="submit" name="action" value="show_all" class="show-list">Show All</button>
        <button type="submit" name="action" value="total_count" class="show-list">Total Count</button>
        <select name="filter_option" class="filter" onchange="this.form.submit()">
          <option value="">Filter</option>
          <option value="id">Sort by ID</option>
          <option value="status">Sort by Status</option>
        </select>
      </form>
    </div>

    <?php if (!empty($search_results)): ?>
      <h3>Search Results</h3>
      <table class="result_table" border="1">
        <thead>
          <tr style="background-color: #28a745;">
            <th>Case ID</th>
            <th>Criminal ID</th>
            <th>Court Name</th>
            <th>Judge Name</th>
            <th>Case Status</th>
            <th>Police ID</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($search_results as $row): ?>
            <tr>
              <td><?= htmlspecialchars($row['case_id']) ?></td>
              <td><?= htmlspecialchars($row['criminal_id']) ?></td>
              <td><?= htmlspecialchars($row['court_name']) ?></td>
              <td><?= htmlspecialchars($row['judge_name']) ?></td>
              <td><?= htmlspecialchars($row['case_status']) ?></td>
              <td><?= htmlspecialchars($row['police_id']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

    <?php if (isset($totalCount)): ?>
      <h3>Total Case Count</h3>
      <table class="result_table" border="1" align="center">
        <thead>
          <tr style="background-color: #28a745;">
            <th>Total Number of Cases</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><?php echo htmlspecialchars($totalCount); ?></td>
          </tr>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</body>

</html>