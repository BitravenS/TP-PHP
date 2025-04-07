<?php
require_once 'classes/Repository.php';
require_once 'includes/header.php';

$session = Session::getInstance();
$auth = new Auth(new UserRepository(Database::getInstance()->getPDO()), $session);
$auth->requireLogin();

$id = $_GET['id'] ?? 0;
$studentRepo = new StudentRepository(Database::getInstance()->getPDO());
$sectionRepo = new SectionRepository(Database::getInstance()->getPDO());

$student = $studentRepo->findById($id);
if (!$student) {
    header('Location: students.php');
    exit;
}

$section = $sectionRepo->findById($student->section_id);
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Student Details</h2>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="images/<?= htmlspecialchars($student->image) ?>" alt="<?= htmlspecialchars($student->name) ?>" 
                             class="rounded-circle img-thumbnail" style="width: 150px; height: 150px;">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">ID:</div>
                        <div class="col-md-9"><?= $student->id ?></div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Name:</div>
                        <div class="col-md-9"><?= htmlspecialchars($student->name) ?></div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Birthday:</div>
                        <div class="col-md-9"><?= htmlspecialchars($student->birthday) ?></div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Section:</div>
                        <div class="col-md-9">
                            <?= $section ? htmlspecialchars($section->designation) : 'N/A' ?>
                        </div>
                    </div>
                    
                    <?php if ($section): ?>
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Section Description:</div>
                        <div class="col-md-9"><?= htmlspecialchars($section->description) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="students.php" class="btn btn-secondary">Back to List</a>
                    <?php if ($session->isAdmin()): ?>
                        <a href="student_edit.php?id=<?= $student->id ?>" class="btn btn-warning">Edit</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
