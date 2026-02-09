<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';


// Get date range from URL parameters
$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-t');

// Fetch report data
$stmt = $pdo->prepare("
    SELECT s.student_id, s.name,
           SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) as present_count,
           SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) as absent_count,
           SUM(CASE WHEN a.status = 'Late' THEN 1 ELSE 0 END) as late_count
    FROM students s
    LEFT JOIN attendance a ON s.id = a.student_id AND a.date BETWEEN ? AND ?
    GROUP BY s.id
    ORDER BY s.name
");
$stmt->execute([$startDate, $endDate]);
$students = $stmt->fetchAll();

// Calculate total days
$totalDays = 0;
$current = strtotime($startDate);
$last = strtotime($endDate);
while ($current <= $last) {
    $totalDays++;
    $current = strtotime('+1 day', $current);
}

// Set headers for Excel file download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="attendance_report_'.date('Ymd_His').'.xls"');

// Generate Excel content
echo "Attendance Report (" . date('M j, Y', strtotime($startDate)) . " to " . date('M j, Y', strtotime($endDate)) . ")\n\n";
echo "Total Days: $totalDays\n";
echo "Total Students: " . count($students) . "\n\n";

echo "Student ID\tName\tPresent\tAbsent\tLate\tAttendance %\n";

foreach ($students as $student) {
    $totalRecords = $student['present_count'] + $student['absent_count'] + $student['late_count'];
    $attendancePercent = $totalDays > 0 ? 
        round(($student['present_count'] + $student['late_count'] * 0.5) / $totalDays * 100, 2) : 0;
    
    echo implode("\t", [
        $student['student_id'],
        $student['name'],
        $student['present_count'],
        $student['absent_count'],
        $student['late_count'],
        $attendancePercent . '%'
    ]) . "\n";
}

exit;