<?php
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['studentName'] ?? '';
    $email = $_POST['studentEmail'] ?? '';
    $course = $_POST['studentCourse'] ?? '';
    $role  = "student";
    $status = "active";

    if (empty($name) || empty($email) || empty($course)) {
        echo json_encode(['success' => false, 'error' => 'Name, email, and course are required.']);
        exit;
    }

    $check_stmt = mysqli_prepare($conn, 'SELECT * FROM users WHERE email = ?');
    if (!$check_stmt) {
        echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . mysqli_error($conn)]);
        exit;
    }

    mysqli_stmt_bind_param($check_stmt, 's', $email);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);
    mysqli_stmt_close($check_stmt);

    if (mysqli_num_rows($result) > 0) {
        echo json_encode(['success' => false, 'error' => 'Email already exists.']);
        exit;
    }

    $defaultPassword = password_hash('password123', PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($conn, 'INSERT INTO users (name, email, password, role, course, status) VALUES (?, ?, ?, ?, ?, ?)');
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . mysqli_error($conn)]);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "ssssss", $name, $email, $defaultPassword, $role, $course, $status);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Execute failed: ' . mysqli_error($conn)]);
    }

    mysqli_stmt_close($stmt);
}
