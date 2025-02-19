<?php
session_start();
require "connexion.php";
// Check if the user is logged in
if (!isset($_SESSION["name"]) || !isset($_SESSION["role"]) || !isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

// Calculate time spent in the current session
if (isset($_SESSION["login_time"])) {
    $current_time = time();
    $time_spent = $current_time - $_SESSION["login_time"];
    $_SESSION["total_time_spent"] += $time_spent;
    $_SESSION["login_time"] = $current_time;
}

// Convert total time spent to hours and minutes
$hours = floor($_SESSION["total_time_spent"] / 3600);
$minutes = floor(($_SESSION["total_time_spent"] % 3600) / 60);

// Check if a description has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["description"])) {
    $_SESSION["description"] = $_POST["description"];
}

$description = isset($_SESSION["description"]) ? $_SESSION["description"] : ""; 

// Check if a description has been submitted
// Retrieve existing description from the database if not set in the session
if (!isset($_SESSION["description"])) {
    $query = "SELECT description FROM profile WHERE email = :email";
    $stmt = $connexion->prepare($query);
    $stmt->bindValue(':email', $_SESSION["email"], PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION["description"] = $result ? $result['description'] : '';
}

// Update description in the database and session on form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["description"])) {
    $description = $_POST["description"];
    $_SESSION["description"] = $description; // Store in session

    // Update the database
    $query = "UPDATE profile SET description = :description WHERE email = :email";
    $stmt = $connexion->prepare($query);
    $stmt->bindValue(':description', $description, PDO::PARAM_STR);
    $stmt->bindValue(':email', $_SESSION["email"], PDO::PARAM_STR);
    $stmt->execute();
}


// Set description for display
$description = $_SESSION["description"];

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بطاقة الملف الشخصي</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-toggler:focus{
            box-shadow: none !important;
            
        }
        .navbar-toggler{
            border: none !important;
        }
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Amiri', serif; /* Optional: Add an Arabic font */
            direction: rtl;
            text-align: right;
        }
        .bg-image {
            background-image: url(IMGG/mos2.jpg);
            background-size: cover;
            background-repeat: no-repeat ;
            background-position: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
    text-align: right;
    background-color: rgba(255, 255, 255, 0); /* Fully transparent background */
    backdrop-filter: blur(5px); /* Blur effect */
    border-radius: 10px; /* Optional: rounded corners */
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.3); /* Optional: shadow */
}

        .form-control {
            text-align: right;
        }
        .card-body h5,p{
            color: white;
        }
    </style>
</head>
<body>
    <div class="bg-image">
        <div class="card w-50 shadow">
            <div class="card-header bg-info text-white">
                <a href="indexAr.php" class="text-white d-flex align-items-center">
                    <img style="width: 25px; height: 25px; margin-left: 8px;" src="./IMGG/enter.png" alt="Homepage">
                </a>
                <h4 class="mb-0 d-flex align-items-center">بطاقة الملف الشخصي</h4>
            </div>
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($_SESSION["name"]); ?>: الاسم </h5>
                <p class="card-text">الدور: <?php echo htmlspecialchars($_SESSION["role"]); ?></p>
                <p class="card-text">البريد الإلكتروني: <?php echo htmlspecialchars($_SESSION["email"]); ?></p>
                <p class="card-text">الوقت الكلي المستغرق: <?php echo "$hours ساعة و $minutes دقيقة"; ?></p>
                <h5 class="mt-4">الوصف</h5>
                <form method="post">
                    <div class="form-group">
                        <textarea name="description" id="textarea" class="card form-control" rows="4"><?php echo htmlspecialchars($description); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-secondary">حفظ الوصف</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
