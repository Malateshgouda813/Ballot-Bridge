<?php
session_start();
include("connect.php");

// Get login data from form
$mobile = $_POST['mobile'] ?? '';
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';

// Validate input
if(empty($mobile) || empty($password)){
    echo "<script>
        alert('Please enter mobile and password!');
        window.location='../index.html';
    </script>";
    exit();
}

// Check if user exists in DB
$query = "SELECT * FROM user WHERE mobile='$mobile' AND password='$password' AND role='$role'";
$result = mysqli_query($connect, $query);

if(mysqli_num_rows($result) > 0){
    // Fetch user data
    $userdata = mysqli_fetch_assoc($result);

    // Fetch all groups (role = 2)
    $groups = mysqli_query($connect, "SELECT * FROM user WHERE role=2");
    $groupsdata = mysqli_fetch_all($groups, MYSQLI_ASSOC);

    // Store user data and groups in session
    $_SESSION['userdata'] = $userdata;
    $_SESSION['groupsdata']['groups'] = $groupsdata;

    // Redirect to dashboard
    header("Location: ../routes/dashboard.php");
    exit();
} else {
    echo "<script>
        alert('Invalid credentials or user not found!');
        window.location='../index.html';
    </script>";
}
?>