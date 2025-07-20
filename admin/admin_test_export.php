<?php
require_once 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$test_id = isset($_GET['test_id']) ? (int)$_GET['test_id'] : 0;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$passed_filter = isset($_GET['passed']) ? sanitize($_GET['passed']) : 'all';

// Получаем информацию о тесте
$test_stmt = $pdo->prepare("SELECT * FROM tests WHERE id = ?");
$test_stmt->execute([$test_id]);
$test = $test_stmt->fetch();

if (!$test) {
    $_SESSION['error'] = "Тест не найден";
    header("Location: admin_tests.php");
    exit;
}

// Получаем результаты для экспорта
$query = "SELECT ut.*, l.fio, l.email 
          FROM user_tests ut
          JOIN listeners l ON ut.user_id = l.id
          WHERE ut.test_id = ?";

if (!empty($search)) {
    $query .= " AND (l.fio LIKE :search OR l.email LIKE :search)";
}

if ($passed_filter === 'passed') {
    $query .= " AND ut.passed = 1";
} elseif ($passed_filter === 'failed') {
    $query .= " AND ut.passed = 0";
}

$query .= " ORDER BY ut.completed_at DESC";

$stmt = $pdo->prepare($query);

$params = [$test_id];
if (!empty($search)) {
    $search_param = "%$search%";
    $stmt->bindParam(':search', $search_param, PDO::PARAM_STR);
}

$stmt->execute($params);
$results = $stmt->fetchAll();

// Генерируем Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="results_'.$test['name'].'_'.date('Y-m-d').'.xls"');

echo "<table border='1'>";
echo "<tr>
        <th>ID</th>
        <th>ФИО</th>
        <th>Email</th>
        <th>Баллы</th>
        <th>Статус</th>
        <th>Дата прохождения</th>
        <th>Попытка</th>
      </tr>";

foreach ($results as $result) {
    echo "<tr>";
    echo "<td>".$result['id']."</td>";
    echo "<td>".htmlspecialchars($result['fio'])."</td>";
    echo "<td>".htmlspecialchars($result['email'])."</td>";
    echo "<td>".$result['score']." / ".$result['max_score']."</td>";
    echo "<td>".($result['passed'] ? "Пройден" : "Не пройден")."</td>";
    echo "<td>".date('d.m.Y H:i', strtotime($result['completed_at']))."</td>";
    echo "<td>".$result['attempt_number']."</td>";
    echo "</tr>";
}

echo "</table>";
exit;