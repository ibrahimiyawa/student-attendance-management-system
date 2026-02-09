<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

// Check if date is provided, otherwise use today
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Fetch all students
$stmt = $pdo->query("SELECT * FROM students ORDER BY name");
$students = $stmt->fetchAll();

// Check if attendance already taken for this date
$stmt = $pdo->prepare("SELECT COUNT(*) FROM attendance WHERE date = ?");
$stmt->execute([$date]);
$attendanceTaken = $stmt->fetchColumn() > 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete existing attendance for this date
    $pdo->prepare("DELETE FROM attendance WHERE date = ?")->execute([$date]);
    
    // Insert new attendance records
    $absentStudents = [];
    
    foreach ($_POST['attendance'] as $studentId => $status) {
        $notes = isset($_POST['notes'][$studentId]) ? $_POST['notes'][$studentId] : '';
        
        $stmt = $pdo->prepare("INSERT INTO attendance (student_id, date, status, notes) VALUES (?, ?, ?, ?)");
        $stmt->execute([$studentId, $date, $status, $notes]);
        
        // If absent, add to list for notification
        if ($status === 'Absent') {
            $stmt = $pdo->prepare("SELECT name, parent_email FROM students WHERE id = ?");
            $stmt->execute([$studentId]);
            $student = $stmt->fetch();
            
            $absentStudents[] = [
                'name' => $student['name'],
                'email' => $student['parent_email'],
                'notes' => $notes
            ];
        }
    }
    
    // Send notifications to parents of absent students
    require_once __DIR__ . '/../includes/mailer.php';
    
    foreach ($absentStudents as $absent) {
        sendAbsentNotification($absent['name'], $absent['email'], $date, $absent['notes']);
    }
    
    $_SESSION['success'] = "Attendance recorded successfully";
    header("Location: " . BASE_URL . "attendance/index.php");
    exit();
}
?>

<div class="container">
    <h2>Take Attendance - <?php echo date('F j, Y', strtotime($date)); ?></h2>
    
    <?php if ($attendanceTaken): ?>
        <div class="alert alert-info">
            Attendance already taken for this date. Submitting again will overwrite existing records.
        </div>
    <?php endif; ?>
    
    <div class="mb-3">
        <button id="select-all-present" class="btn btn-sm btn-success">Mark All Present</button>
        <button id="select-all-absent" class="btn btn-sm btn-danger">Mark All Absent</button>
    </div>
    
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?date=' . htmlspecialchars($date); ?>">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Notes (if absent)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): 
                    $stmt = $pdo->prepare("SELECT status, notes FROM attendance WHERE student_id = ? AND date = ?");
                    $stmt->execute([$student['id'], $date]);
                    $attendance = $stmt->fetch();
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                        <td>
                            <select name="attendance[<?php echo $student['id']; ?>]" class="form-control" required>
                                <option value="Present" <?php echo ($attendance && $attendance['status'] === 'Present') ? 'selected' : ''; ?>>Present</option>
                                <option value="Absent" <?php echo ($attendance && $attendance['status'] === 'Absent') ? 'selected' : ''; ?>>Absent</option>
                                <option value="Late" <?php echo ($attendance && $attendance['status'] === 'Late') ? 'selected' : ''; ?>>Late</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="notes[<?php echo $student['id']; ?>]" class="form-control" 
                                   value="<?php echo $attendance ? htmlspecialchars($attendance['notes']) : ''; ?>" 
                                   placeholder="Reason for absence">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <button type="submit" class="btn btn-primary">Submit Attendance</button>
        <a href="<?php echo BASE_URL; ?>attendance/index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>