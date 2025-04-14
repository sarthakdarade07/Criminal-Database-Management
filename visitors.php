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

$message = ""; // To store success/error messages
$search_results = []; // To store search result rows

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $action = $_POST['action'] ?? '';
  $visitor_id = $_POST['visitor_id'] ?? '';

  if ($action == "add") {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $visit_date = $_POST['visit_date'];
    $criminal_id = $_POST['criminal_id'];

    $visit_date_sql = empty($visit_date) ? "NULL" : "'$visit_date'";
    $sql = "INSERT INTO visitors (visitor_id, name,contact,visit_date,criminal_id) VALUES ('$visitor_id', '$name','$contact', $visit_date_sql, '$criminal_id')";
    $message = ($conn->query($sql) === TRUE) ? "Record added successfully!" : "Error: " . $conn->error;
  }

  if ($action == "update") {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $visit_date = $_POST['visit_date'];
    $criminal_id = $_POST['criminal_id'];

    $visit_date_sql = empty($visit_date) ? "NULL" : "'$visit_date'";

    $sql = "SELECT * FROM visitors WHERE visitor_id='$visitor_id'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
      $sql = "UPDATE visitors SET name='$name',contact='$contact',visit_date=$visit_date_sql, criminal_id='$criminal_id' WHERE visitor_id='$visitor_id'";
      $message = ($conn->query($sql) === TRUE) ? "Record updated successfully!" : "Error updating record: " . $conn->error;
    } else {
      $message = "Record not found!" . $conn->error;
    }
  }

  if ($action == "delete") {
    $sql = "SELECT * FROM visitors WHERE visitor_id = '$visitor_id'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
      $sql = "DELETE FROM visitors WHERE visitor_id = '$visitor_id'";
      $message = ($conn->query($sql) === TRUE) ? "Record deleted successfully!" : "Error deleting record: " . $conn->error;
    } else {
      $message = "Record not found!";
    }
  }

  if ($action == "search") {
    $search_query = $_POST['search_query'];
    $sql = "SELECT * FROM visitors WHERE visitor_id = '$search_query' OR name LIKE '%$search_query%'";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
      $search_results = $result->fetch_all(MYSQLI_ASSOC);
    } else {
      $message = "No matching records found.";
    }
  }

  if ($action == "show_all") {
    $sql = "SELECT * FROM visitors";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
      $search_results = $result->fetch_all(MYSQLI_ASSOC);
    } else {
      $message = "No matching records found.";
    }
  }

  if ($action == "total_count") {
    // Fetch all records (like show_all)
    $sql2 = "SELECT * FROM visitors";
    $result2 = $conn->query($sql2);

    if ($result2 && $result2->num_rows > 0) {
      $search_results = $result2->fetch_all(MYSQLI_ASSOC); // For search table
    }

    // Fetch total count
    $sql = "SELECT COUNT(*) AS Total_Count FROM visitors";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $totalCount = "Total number of criminals: " . $row['Total_Count']; // For count table
    }
  }



  if ($action == "recent_visitors") {
    $sql = "SELECT * FROM visitors WHERE TIMESTAMPDIFF(DAY, visit_date, CURDATE()) < 8";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
      $search_results = $result->fetch_all(MYSQLI_ASSOC);
    } else {
      $message = "No matching records found.";
    }
  }

  if($action=="today_visitors"){
    $sql="SELECT * FROM visitors WHERE TIMESTAMPDIFF(DAY,visit_date,CURDATE())=0";
    $result=$conn->query(($sql));
    if ($result && $result->num_rows > 0) {
      $search_results = $result->fetch_all(MYSQLI_ASSOC);
    } else {
      $message = "No matching records found.";
    }
  }

  if ($action == "filter" || isset($_POST['filter_option'])) {
    $filter = $_POST['filter_option'];

    if ($filter == "id") {
      $sql = "SELECT * FROM visitors ORDER BY visitor_id ASC";
    } elseif ($filter == "age") {
      $sql = "SELECT * FROM visitors ORDER BY visit_date ASC";
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
  <title>Criminal Record Management</title>
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
    <h1 id="center-heading" style="font-weight: bolder">Criminal Record Management</h1>

    <p id="center-msg">
      Easily manage and maintain criminal records with our secure and
      efficient platform. Add, update, and retrieve case details, offender
      profiles, and investigation reports—all in one place. Streamline
      record-keeping and ensure accurate data management with ease.
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
          <label class="label1">Visitor ID*</label>
          <input type="text" name="visitor_id" placeholder="Visitor ID" required /><br /><br />
          <label class="label1">Name*</label>
          <input type="text" name="name" placeholder="Name" required /><br /><br />
            <label class="label1">Contact*</label>
          <input type="number" name="contact" placeholder="contact" required /><br /><br />
          <label class="label1">Visit-Date</label>
          <input type="date" name="visit_date" /><br /><br />
          <label class="label1">Criminal ID*</label>
          <input type="text" name="criminal_id" placeholder="Criminal ID" /><br /><br />
          <input type="submit" name="action" value="add" class="crudBtn" />
        </form>
      </div>

      <div class="box">
        <form action="" method="post">
          <label class="label1">Visitor ID*</label>
          <input type="text" name="visitor_id" placeholder="Visitor ID" required /><br /><br />
          <label class="label1">Name*</label>
          <input type="text" name="name" placeholder="Name" required /><br /><br />
          <label class="label1">Contact*</label>
          <input type="number" name="contact" placeholder="contact" required /><br /><br />
          <label class="label1">Visit-Date</label>
          <input type="date" name="visit_date" /><br /><br />
          <label class="label1">Criminal ID*</label>
          <input type="text" name="criminal_id" placeholder="Criminal ID" /><br /><br />
          <input type="submit" name="action" value="update" class="crudBtn" />
        </form>
      </div>

      <div class="box">
        <form action="" method="post">
           <label class="label1">Visitor ID*</label>
          <input type="text" name="visitor_id" placeholder="Visitor ID" required /><br /><br />
          <input type="submit" name="action" value="delete" class="crudBtn" />
        </form>
      </div>
    </div>

    <!-- Search Form -->
    <form class="search-form" method="post">
      <input type="text" name="search_query" placeholder="Enter ID or Name to search" required style="width:50vw" />
      <button type="submit" name="action" value="search">
        <i class="fa fa-search"></i>
      </button>
    </form>

    <!-- Footer Buttons -->
    <div style="justify-content: center; display: flex; gap: 10px; margin-top: 20px;">


      <form action="" class="search-form" method="post" style="margin-top: -5px;">
        <button type="submit" name="action" value="show_all" class="show-list"> Show All </button>
        <button type="submit" name="action" value="recent_visitors" class="show-list">Recent Visitor</button>
        <button type="submit" name="action" value="total_count" class="show-list">Total Count of Criminal</button>
        <button type="submit" name="action" class="show-list" value="today_visitors">Today's Visitor</button>


        <select name="filter_option" class="filter" onchange="this.form.submit()">
          <option value="">Filter</option>
          <option value="id">Sort by ID</option>
          <option value="age">Sort by Visit Date</option>
        </select>

      </form>
    </div>

    <!-- Search Results Table -->
    <?php if (!empty($search_results)): ?>
      <h3>Search Results</h3>
      <table class="result_table" border="1">
        <thead>
          <tr style="background-color: #28a745;">
            <th>Visitor ID</th>
            <th>Name</th>
            <th>Contact No</th>
            <th>Visit Date </th>
            <th>Criminal ID</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($search_results as $row): ?>
            <tr>
              <td class="result_th"><?= htmlspecialchars($row['visitor_id'] ?? 'N/A') ?></td>
              <td class="result_th"><?= htmlspecialchars($row['name'] ?? 'N/A') ?></td>
               <td class="result_th"><?= htmlspecialchars($row['contact'] ?? 'N/A') ?></td>
              <td class="result_th"><?= htmlspecialchars($row['visit_date'] ?? 'N/A') ?></td>
              <td class="result_th"><?= htmlspecialchars($row['criminal_id'] ?? 'N/A') ?></td>
            </tr>
          <?php endforeach; ?>

        </tbody>
      </table>

    <?php endif; ?>

    <?php if (isset($totalCount)): ?>
      <h3>Total Criminal Count</h3>
      <table class="result_table" border="1" align="center">
        <thead>
          <tr style="background-color: #28a745;">
            <th>Total Number of Visitors</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="result_th"><?php echo htmlspecialchars($totalCount); ?></td>
          </tr>
        </tbody>
      </table>
    <?php endif; ?>


  </div>


</body>

</html>