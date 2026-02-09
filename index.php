<?php
require_once 'includes/header.php';
require_once 'includes/auth.php';
?>

<div class="text-center">
    <h1 class="display-4">College Attendance System</h1>
    <p class="lead">Manage student attendance and generate reports</p>
    
    <?php if (isLoggedIn()): ?>
        <div class="mt-5">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Take Attendance</h5>
                            <p class="card-text">Record daily attendance for your class</p>
                            <a href="<?php echo BASE_URL; ?>attendance/take.php" class="btn btn-primary">Go</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">View Reports</h5>
                            <p class="card-text">Generate attendance reports and statistics</p>
                            <a href="<?php echo BASE_URL; ?>reports/" class="btn btn-primary">Go</a>
                        </div>
                    </div>
                </div>
                <?php if (isAdmin()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Manage Students</h5>
                                <p class="card-text">Add, edit or remove students</p>
                                <a href="<?php echo BASE_URL; ?>students/" class="btn btn-primary">Go</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="mt-5">
            <p>Please log in to access the attendance system.</p>
            <a href="<?php echo BASE_URL; ?>auth/login.php" class="btn btn-primary">Login</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>