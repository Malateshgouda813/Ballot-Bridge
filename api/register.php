<?php
include("connect.php");

// Check if form is submitted
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $name = $_POST['name'] ?? '';
    $mobile = $_POST['mobile'] ?? '';
    $password = $_POST['password'] ?? '';
    $cpassword = $_POST['cpassword'] ?? '';
    $address = $_POST['address'] ?? '';
    $role = $_POST['role'] ?? '';

    // Validate required fields
    if(empty($name) || empty($mobile) || empty($password) || empty($cpassword)){
        echo '<script>
            alert("Please fill all required fields!");
            window.location = "../routes/register.html";
        </script>';
        exit();
    }

    // Check password match
    if($password !== $cpassword){
        echo '<script>
            alert("Password and Confirm Password do not match!");
            window.location = "../routes/register.html";
        </script>';
        exit();
    }

    // Default image
    $image = "male.png";

    // Handle file upload
    if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0){

        $image = $_FILES['photo']['name'];
        $tmp_name = $_FILES['photo']['tmp_name'];

        // Ensure uploads folder exists
        if(!is_dir("../uploads")){
            mkdir("../uploads", 0777, true);
        }

        move_uploaded_file($tmp_name, "../uploads/".$image);
    }

    // Insert user into DB
    $insert = mysqli_query($connect, "INSERT INTO user (name, mobile, address, password, photo, role, status, votes) 
        VALUES ('$name', '$mobile', '$address', '$password', '$image', '$role', 0, 0)");

    if($insert){
        echo '<script>
            alert("Registration Successful!");
            window.location = "../index.html";
        </script>';
    } else {
        echo '<script>
            alert("Error! Could not register.");
            window.location = "../routes/register.html";
        </script>';
    }

} else {
    echo '<script>
        alert("Invalid request!");
        window.location = "../routes/register.html";
    </script>';
}
?>