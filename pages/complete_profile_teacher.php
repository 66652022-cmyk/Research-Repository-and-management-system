<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$database = new Database();
$pdo = $database->connect();
$userId = $_SESSION['user_id'];

// Fetch user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Only teachers/advisers/critique
$rolesAllowed = ['adviser','critique_english','critique_statistician'];
if (!in_array($user['role'], $rolesAllowed)) {
    header('Location: /THESIS/adminDash.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complete Teacher Profile</title>
    <link href="/THESIS/src/output.css" rel="stylesheet">
    <style>
        .bg-royal-blue { background-color: #4169E1; }
        .hover\:bg-royal-blue-dark:hover { background-color: #1E3A8A; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
<div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Complete Your Profile</h1>

    <form id="teacherProfileForm">
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

        <!-- Educational Attainment -->
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Educational Attainment</label>
            <select name="educational_attainment" required class="w-full border px-3 py-2 rounded">
                <option value="">Select</option>
                <?php foreach(['Bachelors','Masters','Doctorate','Other'] as $opt): ?>
                    <option value="<?= $opt ?>" <?= ($user['educational_attainment'] ?? '')==$opt?'selected':'' ?>><?= $opt ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Course They Teach -->
        <div class="mb-6">
            <label class="block mb-1 font-semibold">Course You Teach</label>
            <input type="text" name="course" required value="<?= $user['course'] ?? '' ?>" class="w-full border px-3 py-2 rounded">
        </div>

        <button type="submit" class="w-full bg-royal-blue hover:bg-royal-blue-dark text-white py-2 rounded font-semibold">
            Save Profile
        </button>
    </form>
</div>

<script>
document.getElementById('teacherProfileForm').addEventListener('submit', async (e)=>{
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target).entries());
    const res = await fetch('save_profile_teacher.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify(data)
    });
    const result = await res.json();
    if(result.success){ window.location.href = '/THESIS/adminDash.php'; }
    else { alert(result.message); }
});
</script>
</body>
</html>
