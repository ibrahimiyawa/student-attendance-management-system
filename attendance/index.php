<?php
require_once '../includes/header.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';

// Get current month and year
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// Calculate previous and next month
$prevMonth = $month - 1;
$prevYear = $year;
if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear--;
}

$nextMonth = $month + 1;
$nextYear = $year;
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}

// Fetch all students
$stmt = $pdo->query("SELECT id, name, student_id FROM students ORDER BY name");
$students = $stmt->fetchAll();

// Fetch attendance for the month
$startDate = "$year-$month-01";
$endDate = date('Y-m-t', strtotime($startDate));

$stmt = $pdo->prepare("
    SELECT a.student_id, a.date, a.status 
    FROM attendance a 
    WHERE a.date BETWEEN ? AND ?
    ORDER BY a.date
");
$stmt->execute([$startDate, $endDate]);
$attendanceRecords = $stmt->fetchAll();

// Organize attendance by student and date
$attendanceData = [];
foreach ($attendanceRecords as $record) {
    $attendanceData[$record['student_id']][$record['date']] = $record['status'];
}
?>

<div class="container">
    <h2>Attendance Overview</h2>
    
    <div class="mb-3">
        <a href="take.php" class="btn btn-primary">Take Attendance</a>
        <a href="view.php" class="btn btn-secondary">View Detailed Report</a>
    </div>
    
    <div class="month-navigation mb-3">
        <a href="?month=<?php echo $prevMonth; ?>&year=<?php echo $prevYear; ?>" class="btn btn-sm btn-outline-primary">&lt; Prev</a>
        <span class="mx-3"><strong><?php echo date('F Y', strtotime($startDate)); ?></strong></span>
        <a href="?month=<?php echo $nextMonth; ?>&year=<?php echo $nextYear; ?>" class="btn btn-sm btn-outline-primary">Next &gt;</a>
    </div>
    
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Student</th>
                    <?php
                    $current = strtotime($startDate);
                    $last = strtotime($endDate);
                    
                    while ($current <= $last) {
                        $day = date('j', $current);
                        $weekday = date('D', $current);
                        echo "<th title='$weekday'>$day</th>";
                        $current = strtotime('+1 day', $current);
                    }
                    ?>
                    <th>Present</th>
                    <th>Absent</th>
                    <th>Late</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): 
                    $presentCount = 0;
                    $absentCount = 0;
                    $lateCount = 0;
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                        <?php
                        $current = strtotime($startDate);
                        $last = strtotime($endDate);
                        
                        while ($current <= $last) {
                            $date = date('Y-m-d', $current);
                            $status = $attendanceData[$student['id']][$date] ?? '';
                            
                            // Count statuses
                            if ($status === 'Present') $presentCount++;
                            elseif ($status === 'Absent') $absentCount++;
                            elseif ($status === 'Late') $lateCount++;
                            
                            // Display status with color coding
                            $class = '';
                            if ($status === 'Present') $class = 'bg-success text-white';
                            elseif ($status === 'Absent') $class = 'bg-danger text-white';
                            elseif ($status === 'Late') $class = 'bg-warning';
                            
                            echo "<td class='text-center $class'>" . substr($status, 0, 1) . "</td>";
                            $current = strtotime('+1 day', $current);
                        }
                        ?>
                        <td class="text-center"><?php echo $presentCount; ?></td>
                        <td class="text-center"><?php echo $absentCount; ?></td>
                        <td class="text-center"><?php echo $lateCount; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>