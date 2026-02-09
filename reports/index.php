<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';



// Get date range (default: current month)
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// Fetch all students with their attendance counts
$stmt = $pdo->prepare("
    SELECT s.id, s.name, s.student_id,
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
?>

<div class="container">
    <h2>Attendance Reports</h2>
    
    <form method="get" class="mb-4">
        <div class="form-row">
            <div class="col-md-4">
                <label for="start_date">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" 
                       value="<?php echo $startDate; ?>">
            </div>
            <div class="col-md-4">
                <label for="end_date">End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" 
                       value="<?php echo $endDate; ?>">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Generate Report</button>
            </div>
        </div>
    </form>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5>Summary (<?php echo date('M j, Y', strtotime($startDate)); ?> to <?php echo date('M j, Y', strtotime($endDate)); ?>)</h5>
        </div>
        <div class="card-body">
            <p>Total Days: <?php echo $totalDays; ?></p>
            <p>Total Students: <?php echo count($students); ?></p>
        </div>
    </div>
    
    <?php if (count($students) > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Present</th>
                    <th>Absent</th>
                    <th>Late</th>
                    <th>Attendance %</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): 
                    $totalRecords = $student['present_count'] + $student['absent_count'] + $student['late_count'];
                    $attendancePercent = $totalDays > 0 ? round(($student['present_count'] + $student['late_count'] * 0.5) / $totalDays * 100, 2) : 0;
                    
                    $rowClass = '';
                    if ($attendancePercent < 70) $rowClass = 'table-danger';
                    elseif ($attendancePercent < 80) $rowClass = 'table-warning';
                    elseif ($attendancePercent >= 80) $rowClass = 'table-success';
                ?>
                    <tr class="<?php echo $rowClass; ?>">
                        <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                        <td><?php echo $student['present_count']; ?></td>
                        <td><?php echo $student['absent_count']; ?></td>
                        <td><?php echo $student['late_count']; ?></td>
                        <td><?php echo $attendancePercent; ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="mt-3">
            <a href="export.php?start_date=<?php echo urlencode($startDate); ?>&end_date=<?php echo urlencode($endDate); ?>" 
               class="btn btn-success">
                Export to Excel
            </a>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No attendance records found for the selected date range.</div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>