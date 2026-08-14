<?php

session_start();

require_once '../config/config.php';
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['role'] != 'admin') {
    $_SESSION['error'] = "Access Denied!";
    header("Location: ../dashboard/index.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| VALIDATE REQUEST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: users.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| GET FORM DATA
|--------------------------------------------------------------------------
*/

$userID          = (int)$_POST['user_id'];
$fullname        = trim($_POST['fullname']);
$username        = trim($_POST['username']);
$email           = trim($_POST['email']);
$bio             = trim($_POST['bio']);
$role            = trim($_POST['role']);
$status          = trim($_POST['status']);
$email_verified  = (int)$_POST['email_verified'];
$password        = trim($_POST['password']);

/*
|--------------------------------------------------------------------------
| REQUIRED VALIDATION
|--------------------------------------------------------------------------
*/

if (
    empty($fullname) ||
    empty($username) ||
    empty($email)
) {

    $_SESSION['error'] = "Please fill in all required fields.";

    header("Location: edit-user.php?id=".$userID);

    exit();

}

/*
|--------------------------------------------------------------------------
| VALID EMAIL
|--------------------------------------------------------------------------
*/

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    $_SESSION['error']="Invalid email address.";

    header("Location: edit-user.php?id=".$userID);

    exit();

}

/*
|--------------------------------------------------------------------------
| CHECK USER EXISTS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
SELECT *
FROM users
WHERE id=?
LIMIT 1
");

$stmt->execute([$userID]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {

    $_SESSION['error']="User not found.";

    header("Location: users.php");

    exit();

}

/*
|--------------------------------------------------------------------------
| DUPLICATE USERNAME
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
SELECT id
FROM users
WHERE username=?
AND id != ?
LIMIT 1
");

$stmt->execute([
    $username,
    $userID
]);

if ($stmt->rowCount() > 0) {

    $_SESSION['error']="Username already exists.";

    header("Location: edit-user.php?id=".$userID);

    exit();

}

/*
|--------------------------------------------------------------------------
| DUPLICATE EMAIL
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
SELECT id
FROM users
WHERE email=?
AND id != ?
LIMIT 1
");

$stmt->execute([
    $email,
    $userID
]);

if ($stmt->rowCount() > 0) {

    $_SESSION['error']="Email already exists.";

    header("Location: edit-user.php?id=".$userID);

    exit();

}

/*
|--------------------------------------------------------------------------
| READY FOR IMAGE UPLOAD
|--------------------------------------------------------------------------
*/

$profilePicture = $user['profile_picture'];


/*
|--------------------------------------------------------------------------
| UPLOAD NEW PROFILE PICTURE
|--------------------------------------------------------------------------
*/

if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {

    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    $extension = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedTypes)) {

        $_SESSION['error'] = "Only JPG, JPEG, PNG, GIF and WEBP images are allowed.";

        header("Location: edit-user.php?id=" . $userID);
        exit();

    }

    if ($_FILES['profile_picture']['size'] > 2 * 1024 * 1024) {

        $_SESSION['error'] = "Profile picture must not exceed 2MB.";

        header("Location: edit-user.php?id=" . $userID);
        exit();

    }

    $newFileName = uniqid('avatar_') . '.' . $extension;

    $destination = "../uploads/avatars/" . $newFileName;

    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $destination)) {

        if (
            !empty($user['profile_picture']) &&
            file_exists("../uploads/avatars/" . $user['profile_picture'])
        ) {

            unlink("../uploads/avatars/" . $user['profile_picture']);

        }

        $profilePicture = $newFileName;

    } else {

        $_SESSION['error'] = "Unable to upload profile picture.";

        header("Location: edit-user.php?id=" . $userID);
        exit();

    }

}

/*
|--------------------------------------------------------------------------
| HASH PASSWORD IF PROVIDED
|--------------------------------------------------------------------------
*/

$hashedPassword = '';

if (!empty($password)) {

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

}

/*
|--------------------------------------------------------------------------
| PREPARE UPDATE QUERY
|--------------------------------------------------------------------------
*/

if (!empty($password)) {

    $sql = "
        UPDATE users SET
            fullname = ?,
            username = ?,
            email = ?,
            bio = ?,
            role = ?,
            status = ?,
            email_verified = ?,
            profile_picture = ?,
            password = ?,
            updated_at = NOW()
        WHERE id = ?
    ";

    $params = [
        $fullname,
        $username,
        $email,
        $bio,
        $role,
        $status,
        $email_verified,
        $profilePicture,
        $hashedPassword,
        $userID
    ];

} else {

    $sql = "
        UPDATE users SET
            fullname = ?,
            username = ?,
            email = ?,
            bio = ?,
            role = ?,
            status = ?,
            email_verified = ?,
            profile_picture = ?,
            updated_at = NOW()
        WHERE id = ?
    ";

    $params = [
        $fullname,
        $username,
        $email,
        $bio,
        $role,
        $status,
        $email_verified,
        $profilePicture,
        $userID
    ];

}

$stmt = $pdo->prepare($sql);

/*
|--------------------------------------------------------------------------
| EXECUTE UPDATE
|--------------------------------------------------------------------------
*/

try {

    $stmt->execute($params);

} catch (PDOException $e) {

    $_SESSION['error'] = "Failed to update user.";

    header("Location: edit-user.php?id=" . $userID);

    exit();

}

/*
|--------------------------------------------------------------------------
| ACTIVITY LOG
|--------------------------------------------------------------------------
*/

$activity = "Updated user account: " . $username;

$ipAddress = $_SERVER['REMOTE_ADDR'];

$log = $pdo->prepare("
    INSERT INTO activity_logs
    (
        user_id,
        activity,
        ip_address,
        created_at
    )
    VALUES
    (
        ?,
        ?,
        ?,
        NOW()
    )
");

$log->execute([
    $_SESSION['user_id'],
    $activity,
    $ipAddress
]);

/*
|--------------------------------------------------------------------------
| SUCCESS MESSAGE
|--------------------------------------------------------------------------
*/

$_SESSION['success'] = "User updated successfully.";

/*
|--------------------------------------------------------------------------
| REDIRECT
|--------------------------------------------------------------------------
*/

header("Location: user-details.php?id=" . $userID);
exit();