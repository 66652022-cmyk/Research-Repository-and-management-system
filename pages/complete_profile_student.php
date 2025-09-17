<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$database = new Database();
$db = $database->connect();
$userId = $_SESSION['user_id'];

// Fetch user
$stmt = mysqli_prepare($db, "SELECT * FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Only for students
if ($user['role'] !== 'student') {
    header('Location: /THESIS/adminDash.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complete Student Profile</title>
    <link href="/THESIS/src/output.css" rel="stylesheet">
    <style>
        .bg-royal-blue { background-color: #4169E1; }
        .hover\:bg-royal-blue-dark:hover { background-color: #1E3A8A; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
<div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Complete Your Profile</h1>

    <form id="studentProfileForm">
        <input type="hidden" name="user_id" value="<?= $userId ?>">

        <!-- Gender -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Gender</label>
            <select name="gender" required class="w-full border px-3 py-2 rounded">
                <option value="">Select Gender</option>
                <option value="Male" <?= ($user['gender'] ?? '')=='Male'?'selected':'' ?>>Male</option>
                <option value="Female" <?= ($user['gender'] ?? '')=='Female'?'selected':'' ?>>Female</option>
            </select>
        </div>

        <!-- Year -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Year Level</label>
            <select name="year" required class="w-full border px-3 py-2 rounded">
                <option value="">Select Year</option>
                <?php for($y=1;$y<=4;$y++): ?>
                    <option value="<?= $y ?>" <?= ($user['year'] ?? '')==$y?'selected':'' ?>><?= $y ?> Year</option>
                <?php endfor; ?>
            </select>
        </div>

        <!-- Course -->
        <div class="mb-6">
            <label class="block mb-1 font-semibold">Course</label>
            <input type="text" name="course" required value="<?= $user['course'] ?? '' ?>" class="w-full border px-3 py-2 rounded">
        </div>

        <button type="submit" class="w-full bg-royal-blue hover:bg-royal-blue-dark text-white py-2 rounded font-semibold">
            Save Profile
        </button>
    </form>
</div>

<script>
document.getElementById('studentProfileForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const data = Object.fromEntries(new FormData(e.target).entries());

    try {
        const res = await fetch('/THESIS/classes/save_profile_student.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);

        const result = await res.json();

        if (result.success) {
            if (result.success && result.dashboard) {
                window.location.href = result.dashboard;
                alert('Profile saved but dashboard URL not provided.');
            }
        } else {
            alert(result.message);
        }
    } catch (err) {
        console.error('Error saving profile:', err);
        alert('An error occurred while saving your profile.');
    }
});
</script>

</body>
</html>
