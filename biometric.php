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

$uploadDir = "uploads/";
if (!file_exists($uploadDir)) {
  mkdir($uploadDir, 0777, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $action = $_POST['action'] ?? '';
  $fingerprint_id = $_POST['fingerprint_id'] ?? '';

  if ($action == "add") {
    $b_date = $_POST['b_date'];
    $criminal_id = $_POST['criminal_id'];
    $image_path = "";

    if (!empty($_FILES['image']['name'])) {
      $image_name = basename($_FILES['image']['name']);
      $target_file = $uploadDir . time() . "_" . $image_name;
      if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $image_path = $target_file;
      }
    }

    $sql = "INSERT INTO Biometric (fingerprint_id, b_date, criminal_id, image_path)
            VALUES ('$fingerprint_id', '$b_date', '$criminal_id', '$image_path')";
    $message = ($conn->query($sql) === TRUE) ? "Biometric record added!" : "Error: " . $conn->error;
  }

  if ($action == "update") {
    $b_date = $_POST['b_date'];
    $criminal_id = $_POST['criminal_id'];
    $image_path = "";

    if (!empty($_FILES['image']['name'])) {
      $image_name = basename($_FILES['image']['name']);
      $target_file = $uploadDir . time() . "_" . $image_name;
      if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $image_path = $target_file;
        $sql = "UPDATE Biometric SET b_date='$b_date', criminal_id='$criminal_id', image_path='$image_path' WHERE fingerprint_id='$fingerprint_id'";
      } else {
        $sql = "UPDATE Biometric SET b_date='$b_date', criminal_id='$criminal_id' WHERE fingerprint_id='$fingerprint_id'";
      }
    } else {
      $sql = "UPDATE Biometric SET b_date='$b_date', criminal_id='$criminal_id' WHERE fingerprint_id='$fingerprint_id'";
    }

    $message = ($conn->query($sql) === TRUE) ? "Biometric updated!" : "Error updating: " . $conn->error;
  }

  if ($action == "delete") {
    $sql = "DELETE FROM Biometric WHERE fingerprint_id='$fingerprint_id'";
    $message = ($conn->query($sql) === TRUE) ? "Biometric record deleted!" : "Error deleting: " . $conn->error;
  }

  if ($action == "search") {
    $search_query = $_POST['search_query'];
    $sql = "SELECT * FROM Biometric WHERE fingerprint_id='$search_query' OR criminal_id LIKE '%$search_query%'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
      $search_results = $result->fetch_all(MYSQLI_ASSOC);
    } else {
      $message = "No matching records found.";
    }
  }

  if ($action == "show_all") {
    $sql = "SELECT * FROM Biometric";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
      $search_results = $result->fetch_all(MYSQLI_ASSOC);
    } else {
      $message = "No biometric records available.";
    }
  }

  if ($action == "sort_id" || $action == "sort_criminal") {
    $sql = ($action == "sort_id")
      ? "SELECT * FROM Biometric ORDER BY fingerprint_id ASC"
      : "SELECT * FROM Biometric ORDER BY criminal_id ASC";
    $result = $conn->query($sql);
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
  <link rel="stylesheet" href="biomertic-css.css" />
  <title>Biometric Record Management</title>
  <style>
   <style>
  #message-box {
    transition: opacity 1s step-end;
  }

  .search-form {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 60px 0;
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
    width: 60vw;
    margin: 0px auto;
    border-collapse: collapse;
    font-size: 16px;
    background-color: #f9f9f9;
  }

  .result_table th,
  .result_table td {
    border: 1px solid #ccc;
    padding: 12px 15px;
    text-align: center;
  }

  .result_table th {
    background-color: #28a745;
    color: white;
    font-size: 18px;
  }

  .result_table img {
    width: 120px;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  }

  h3 {
    text-align: center;
    margin-top: 30px;
    font-size: 24px;
    color: #333;
  }
</style>

  </style>
</head>

<body>
  <div id="container">
    <h1 id="center-heading" style="font-weight: bolder">Biometric Record Management</h1>
    <p id="center-msg">
      Securely store and manage biometric data linked with criminal profiles.
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
        <form action="" method="post" enctype="multipart/form-data">
          <label class="label1">Fingerprint ID*</label>
          <input type="text" name="fingerprint_id" required /><br /><br />
          <label class="label1">Biometric Date*</label>
          <input type="date" name="b_date" required /><br /><br />
          <label class="label1">Criminal ID*</label>
          <input type="text" name="criminal_id" required /><br /><br />
          <label class="label1">Biometric Image</label>
          <input type="file" name="image" accept="image/*" /><br /><br />
          <input type="submit" name="action" value="add" class="crudBtn" />
        </form>
      </div>

      <div class="box">
        <form action="" method="post" enctype="multipart/form-data">
          <label class="label1">Fingerprint ID*</label>
          <input type="text" name="fingerprint_id" required /><br /><br />
          <label class="label1">Biometric Date*</label>
          <input type="date" name="b_date" required /><br /><br />
          <label class="label1">Criminal ID*</label>
          <input type="text" name="criminal_id" required /><br /><br />
          <label class="label1">Biometric Image (optional)</label>
          <input type="file" name="image" accept="image/*" /><br /><br />
          <input type="submit" name="action" value="update" class="crudBtn" />
        </form>
      </div>

      <div class="box">
        <form action="" method="post">
          <label class="label1">Fingerprint ID*</label>
          <input type="text" name="fingerprint_id" required /><br /><br />
          <input type="submit" name="action" value="delete" class="crudBtn" />
        </form>
      </div>
    </div>

    <form class="search-form" method="post">
      <input type="text" name="search_query" placeholder="Fingerprint ID or Criminal ID" required style="width:50vw" />
      <button type="submit" name="action" value="search"><i class="fa fa-search"></i></button>
    </form>

    <div style="justify-content: center; display: flex; gap: 10px; margin-top: 20px;">
      <form action="" class="search-form" method="post">
        <button type="submit" name="action" value="show_all" class="show-list">Show All</button>
        <button type="submit" name="action" value="sort_id" class="show-list">Sort by ID</button>
        <button type="submit" name="action" value="sort_criminal" class="show-list">Sort by Criminal ID</button>
      </form>
    </div>

    <?php if (!empty($search_results)): ?>
      <h3>Biometric Records</h3>
      <table class="result_table" border="1">
        <thead>
          <tr style="background-color: #28a745;">
            <th>Fingerprint ID</th>
            <th>Biometric Date</th>
            <th>Criminal ID</th>
            <th>Image</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($search_results as $row): ?>
            <tr>
              <td><?= htmlspecialchars($row['fingerprint_id']) ?></td>
              <td><?= htmlspecialchars($row['b_date']) ?></td>
              <td><?= htmlspecialchars($row['criminal_id']) ?></td>
              <td>
                <?php if (!empty($row['image_path'])): ?>
                  <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="Biometric Image" />
                <?php else: ?>
                  No image
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</body>

</html>