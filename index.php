<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login-style.css">
    <title>Criminal Database</title>
    <script>
        // Show alert if there is an error message and remove it from the URL
        window.onload = function () {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('error')) {
                alert(urlParams.get('error'));
                // Remove error parameter from URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        };
    </script>
</head>

<body>
    <div class="container">
        <div class="text-section">
            <h1> Every record tells a <span class="highlight">story</span>—every story leads to <span
                    class="highlight">justice</span></h1>

            <div class="ratings">
                ★★★★★
                <p>Trusted by Law Enforcement and Investigators Nationwide.</p>
            </div>
        </div>
        <div class="form-section">
            <h2>Criminal Database</h2>
            <form method="post" action="login.php">
                <label for="login_id">LOGIN ID*</label>
                <input type="text" id="login_id" name="id" required>

                <label for="password">PASSWORD*</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">SUBMIT</button>
            </form>
        </div>
    </div>
</body>

</html>