<?php
require_once '../includes/header.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = trim($_POST['student_id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $parentEmail = trim($_POST['parent_email']);
    $class = trim($_POST['class']);
    
    // Validate inputs
    $errors = [];
    
    if (empty($studentId)) $errors[] = "Student ID is required";
    if (empty($name)) $errors[] = "Name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($parentEmail) || !filter_var($parentEmail, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid parent email is required";
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO students (student_id, name, email, parent_email, class) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$studentId, $name, $email, $parentEmail, $class]);
            
            $_SESSION['success'] = "Student added successfully";
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<div class="container">
    <h2>Add New Student</h2>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form method="post">
        <div class="form-group">
            <label for="student_id">Student ID</label>
            <input type="text" class="form-control" id="student_id" name="student_id" required>
        </div>
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="email">Student Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="parent_email">Parent Email</label>
            <input type="email" class="form-control" id="parent_email" name="parent_email" required>
        </div>
        <div class="form-group">
            <label for="class">Class</label>
            <input type="text" class="form-control" id="class" name="class" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Student</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>