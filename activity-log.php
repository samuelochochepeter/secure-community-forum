<?php

require_once '../config/config.php';
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$stmt = $pdo->prepare("
SELECT
activity,
ip_address,
created_at
FROM activity_logs
WHERE user_id=?
ORDER BY created_at DESC
LIMIT 100
");

$stmt->execute([
    $_SESSION['user_id']
]);

$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>

<title>Activity Log</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body class="bg-light">

<div class="container py-5">

<h2 class="mb-4">

<i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>

Activity History

</h2>

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>Activity</th>

<th>IP Address</th>

<th>Date</th>

</tr>

</thead>

<tbody>

<?php foreach($logs as $log): ?>

<tr>

<td>

<?= htmlspecialchars($log['activity']); ?>

</td>

<td>

<?= htmlspecialchars($log['ip_address']); ?>

</td>

<td>

<?= date("d M Y H:i", strtotime($log['created_at'])); ?>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</body>

</html>