<?php
require_once 'classes/Repository.php';
require_once 'includes/header.php';

$session = Session::getInstance();
$auth = new Auth(new UserRepository(Database::getInstance()->getPDO()), $session);
$auth->requireLogin();

$isAdmin = $session->isAdmin();
$user = $session->getUser();
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h1 class="card-title">Student Management System</h1>
                    <h4 class="text-muted">Welcome, <?= htmlspecialchars($user->username) ?> (<?= htmlspecialchars($user->role) ?>)</h4>
                    <p>This system allows you to manage students and sections.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Students</h5>
                    <p class="card-text">View and manage student records.</p>
                    <a href="students.php" class="btn btn-primary">Go to Students</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Sections</h5>
                    <p class="card-text">View and manage course sections.</p>
                    <a href="sections.php" class="btn btn-primary">Go to Sections</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
