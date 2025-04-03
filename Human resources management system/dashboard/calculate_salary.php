<?php
require_once 'config/database.php';

$stmt = $pdo->query("SELECT * FROM employees");
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($employees as $emp) {
    $salary = $emp['salary'];
    $overtime_payment = $emp['overtime_hours'] * 50;
    $night_shift_payment = $emp['night_hours'] * 30;
    $deductions = $salary * 0.15;
    $total_salary = $salary + $overtime_payment + $night_shift_payment - $deductions;

    $stmt = $pdo->prepare("INSERT INTO payroll (employee_id, month_year, base_salary, overtime_payment, night_shift_payment, deductions, total_salary) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$emp['id'], date('Y-m'), $salary, $overtime_payment, $night_shift_payment, $deductions, $total_salary]);
}

echo "Maaşlar hesaplandı!";
?>