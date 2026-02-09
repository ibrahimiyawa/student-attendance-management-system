<?php
require_once '../includes/header.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';

requireAdmin(); // Only admin can manage students

// Fetch all students
$stmt = $pdo->query("SELECT * FROM students ORDER BY name");
$students = $stmt->fetchAll();
?>

<div class="container">
    <h2>Student Management</h2>
    <a href="add.php" class="btn btn-primary mb-3">Add New Student</a>
    <div class="mb-3">
    <input type="text" id="student-search" class="form-control" placeholder="Search students...">
</div>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Student ID</th>
                <th>Email</th>
                <th>Parent Email</th>
                <th>Class</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
                <tr class="student-row">
                    <td><?php echo $student['id']; ?></td>
                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                    <td><?php echo htmlspecialchars($student['parent_email']); ?></td>
                    <td><?php echo htmlspecialchars($student['class']); ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="delete.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-danger delete-btn">Delete</a>
        </td>
    </tr>
<?php endforeach; ?>