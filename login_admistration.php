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
    $id = $_POST['id'] ?? '';  // changed from login_id to id

    if ($action == "add") {
        $password = $_POST['password'];
        $sql = "INSERT INTO login (id, password) VALUES ('$id', '$password')";
        $message = ($conn->query($sql) === TRUE) ? "Login added successfully!" : "Error: " . $conn->error;
    }

    if ($action == "update") {
        $password = $_POST['password'];
        $sql = "SELECT * FROM login WHERE id='$id'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows == 1) {
            $sql = "UPDATE login SET password='$password' WHERE id='$id'";
            $message = ($conn->query($sql) === TRUE) ? "Login updated successfully!" : "Error updating login: " . $conn->error;
        } else {
            $message = "Login ID not found!";
        }
    }

    if ($action == "delete") {
        $sql = "SELECT * FROM login WHERE id = '$id'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows == 1) {
            $sql = "DELETE FROM login WHERE id = '$id'";
            $message = ($conn->query($sql) === TRUE) ? "Login deleted successfully!" : "Error deleting login: " . $conn->error;
        } else {
            $message = "Login ID not found!";
        }
    }

    if ($action == "search") {
        $search_query = $_POST['search_query'];
        $sql = "SELECT * FROM login WHERE id = '$search_query'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $search_results = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $message = "No matching login found.";
        }
    }

    if ($action == "show_all") {
        $sql = "SELECT * FROM login";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $search_results = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $message = "No login records found.";
        }
    }

    if ($action == "filter" || isset($_POST['filter_option'])) {
        $filter = $_POST['filter_option'];

        if ($filter == "id") {
            $sql = "SELECT * FROM login ORDER BY id ASC";
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
    <title>Login Management</title>
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
        <h1 id="center-heading" style="font-weight: bolder">Login Management</h1>

        <p id="center-msg">
            Securely manage police login credentials. Add, update, and view login information in one place to ensure
            smooth and protected access to the system.
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
                    <label class="label1">Login ID*</label>
                    <input type="text" name="id" placeholder="Login ID" required /><br /><br />
                    <label class="label1">Password*</label>
                    <input type="password" name="password" placeholder="Password" required /><br /><br />
                    <input type="submit" name="action" value="add" class="crudBtn" />
                </form>
            </div>

            <div class="box">
                <form action="" method="post">
                    <label class="label1">Login ID*</label>
                    <input type="text" name="id" placeholder="Login ID" required /><br /><br />
                    <label class="label1">Password*</label>
                    <input type="password" name="password" placeholder="Password" required /><br /><br />
                    <input type="submit" name="action" value="update" class="crudBtn" />
                </form>
            </div>

            <div class="box">
                <form action="" method="post">
                    <label class="label1">Login ID*</label>
                    <input type="text" name="id" placeholder="Login ID" required /><br /><br />
                    <input type="submit" name="action" value="delete" class="crudBtn" />
                </form>
            </div>
        </div>

        <form class="search-form" method="post">
            <input type="text" name="search_query" placeholder="Enter Login ID to search" required style="width:50vw" />
            <button type="submit" name="action" value="search">
                <i class="fa fa-search"></i>
            </button>
        </form>

        <div style="justify-content: center; display: flex; gap: 10px; margin-top: 20px;">
            <form action="" class="search-form" method="post" style="margin-top: -5px;">
                <button type="submit" name="action" value="show_all" class="show-list"> Show All </button>
                <select name="filter_option" class="filter" onchange="this.form.submit()">
                    <option value="">Filter</option>
                    <option value="id">Sort by Login ID</option>
                </select>
            </form>
        </div>

        <?php if (!empty($search_results)): ?>
            <h3>Search Results</h3>
            <table class="result_table" border="1">
                <thead>
                    <tr style="background-color: #28a745;">
                        <th>Login ID</th>
                        <th>Password</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($search_results as $row): ?>
                        <tr>
                            <td class="result_th"><?= htmlspecialchars($row['id'] ?? 'N/A') ?></td>
                            <td class="result_th"><?= htmlspecialchars($row['password'] ?? 'N/A') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>

</html>