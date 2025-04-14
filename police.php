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
  $police_id = $_POST['police_id'] ?? '';

  if ($action == "add") {
    $name = $_POST['name'];
    $rank = $_POST['police_rank'];

    $sql = "INSERT INTO police (police_id, name, police_rank) VALUES ('$police_id', '$name', '$rank')";
    $message = ($conn->query($sql) === TRUE) ? "Record added successfully!" : "Error: " . $conn->error;
  }

  if ($action == "update") {
    $name = $_POST['name'];
    $rank = $_POST['police_rank'];

    $sql = "SELECT * FROM police WHERE police_id='$police_id'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
      $sql = "UPDATE police SET name='$name', police_rank='$rank' WHERE police_id='$police_id'";
      $message = ($conn->query($sql) === TRUE) ? "Record updated successfully!" : "Error updating record: " . $conn->error;
    } else {
      $message = "Record not found!";
    }
  }

  if ($action == "delete") {
    $sql = "SELECT * FROM police WHERE police_id = '$police_id'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
      $sql = "DELETE FROM police WHERE police_id = '$police_id'";
      $message = ($conn->query($sql) === TRUE) ? "Record deleted successfully!" : "Error deleting record: " . $conn->error;
    } else {
      $message = "Record not found!";
    }
  }

  if ($action == "search") {
    $search_query = $_POST['search_query'];
    $sql = "SELECT * FROM police WHERE police_id = '$search_query' OR name LIKE '%$search_query%'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
      $search_results = $result->fetch_all(MYSQLI_ASSOC);
    } else {
      $message = "No matching records found.";
    }
  }

  if ($action == "show_all") {
    $sql = "SELECT * FROM police";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
      $search_results = $result->fetch_all(MYSQLI_ASSOC);
    } else {
      $message = "No matching records found.";
    }
  }

  if ($action == "sort_id") {
    $sql1 = "SELECT * FROM police ORDER BY police_id ASC";
  } elseif ($action == "sort_name") {
    $sql1 = "SELECT * FROM police ORDER BY name ASC";
  }

  if (!empty($sql1) && empty($search_results)) {
    $result = $conn->query($sql1);
    if ($result && $result->num_rows > 0) {
      $search_results = $result->fetch_all(MYSQLI_ASSOC);
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
  <title>Police Record Management</title>
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
    <h1 id="center-heading" style="font-weight: bolder">Police Record Management</h1>

    <p id="center-msg">
      Manage and maintain police records with ease. Add, update, search, and view officer information on our centralized
      system.
    </p>

    <!-- Success/Error Message -->
    <?php if (!empty($message)): ?>
      <div class="w3-panel w3-green w3-padding" id="message-box">
        <p><?php echo htmlspecialchars($message); ?></p>
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
          <label class="label1">Police ID*</label>
          <input type="text" name="police_id" placeholder="Police ID" required /><br /><br />
          <label class="label1">Name*</label>
          <input type="text" name="name" placeholder="Name" required /><br /><br />
          <label class="label1">Rank*</label>
          <input type="text" name="police_rank" placeholder="Rank" required /><br /><br />
          <input type="submit" name="action" value="add" class="crudBtn" />
        </form>
      </div>

      <div class="box">
        <form action="" method="post">
          <label class="label1">Police ID*</label>
          <input type="text" name="police_id" placeholder="Police ID" required /><br /><br />
          <label class="label1">Name*</label>
          <input type="text" name="name" placeholder="Name" required /><br /><br />
          <label class="label1">Rank*</label>
          <input type="text" name="police_rank" placeholder="Rank" required /><br /><br />
          <input type="submit" name="action" value="update" class="crudBtn" />
        </form>
      </div>

      <div class="box">
        <form action="" method="post">
          <label class="label1">Police ID*</label>
          <input type="text" name="police_id" placeholder="Police ID" required /><br /><br />
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
        <button type="submit" name="action" value="sort_id" class="show-list">Sort by ID</button>
        <button type="submit" name="action" value="sort_name" class="show-list">Sort by Name</button>
      </form>
    </div>

    <!-- Search Results Table -->
    <?php if (!empty($search_results)): ?>
      <h3>Search Results</h3>
      <table class="result_table" border="1">
        <thead>
          <tr style="background-color: #28a745;">
            <th>Police ID</th>
            <th>Name</th>
            <th>Rank</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($search_results as $row): ?>
            <tr>
              <td class="result_th"><?= htmlspecialchars($row['police_id'] ?? 'N/A') ?></td>
              <td class="result_th"><?= htmlspecialchars($row['name'] ?? 'N/A') ?></td>
              <td class="result_th"><?= htmlspecialchars($row['police_rank'] ?? 'N/A') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</body>

</html>