<?php
session_start();
include('connect.php');

// Check if user is logged in
if(!isset($_SESSION['userdata'])){
    header("Location: ../index.html");
    exit();
}

$user = $_SESSION['userdata'];

// Get POST data
$gid = $_POST['gid'] ?? null;
$gvotes = $_POST['gvotes'] ?? null;

if(!$gid || !isset($gvotes)){
    echo '<script>
        alert("Invalid request!");
        window.location = "../routes/dashboard.php";
    </script>';
    exit();
}

// Check if user has already voted
if($user['status'] == 1){
    echo '<script>
        alert("You have already voted!");
        window.location = "../routes/dashboard.php";
    </script>';
    exit();
}

// Increment votes for the selected group
$total_votes = $gvotes + 1;
$update_votes = mysqli_query($connect, "UPDATE user SET votes='$total_votes' WHERE id='$gid'");

// Mark the user as voted
$update_user_status = mysqli_query($connect, "UPDATE user SET status=1 WHERE id='{$user['id']}'");

if($update_votes && $update_user_status){
    // Update session data
    $_SESSION['userdata']['status'] = 1;

    // Refresh groups data in session
    $groups = mysqli_query($connect, "SELECT * FROM user WHERE role=2");
    $groupsdata = mysqli_fetch_all($groups, MYSQLI_ASSOC);
    $_SESSION['groupsdata']['groups'] = $groupsdata;

    echo '<script>
        alert("Voting Successful!");
        window.location = "../routes/dashboard.php";
    </script>';
} else {
    echo '<script>
        alert("Some error occurred while voting!");
        window.location = "../routes/dashboard.php";
    </script>';
}
?>