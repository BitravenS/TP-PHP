<?php
require_once 'classes/Repository.php';
require_once 'includes/header.php';

$session = Session::getInstance();
$auth = new Auth(new UserRepository(Database::getInstance()->getPDO()), $session);
$auth->requireLogin();

$id = $_GET['id'] ?? 0;
$sectionRepo = new SectionRepository(Database::getInstance()->getPDO());
$studentRepo = new StudentRepository(Database::getInstance()->getPDO());

$section = $sectionRepo->findById($id);
if (!$section) {
    header('Location: sections.php');
    exit;
}

$students = $studentRepo->findBySection($section->id);
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Section Details</h2>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">ID:</div>
                        <div class="col-md-9"><?= $section->id ?></div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Designation:</div>
                        <div class="col-md-9"><?= htmlspecialchars($section->designation) ?></div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Description:</div>
                        <div class="col-md-9"><?= htmlspecialchars($section->description) ?></div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Student Count:</div>
                        <div class="col-md-9"><?= count($students) ?></div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="sections.php" class="btn btn-secondary">Back to List</a>
                    <?php if ($session->isAdmin()): ?>
                        <a href="section_edit.php?id=<?= $section->id ?>" class="btn btn-warning">Edit</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (!empty($students)): ?>
                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h3 class="mb-0">Students in this Section</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Photo</th>
                                        <th>Name</th>
                                        <th>Birthday</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $student): ?>
                                        <tr>
                                            <td><?= $student->id ?></td>
                                            <td>
                                                <img src="images/<?= htmlspecialchars($student->image) ?>" alt="<?= htmlspecialchars($student->name) ?>" 
                                                    class="rounded-circle" width="40" height="40">
                                            </td>
                                            <td><?= htmlspecialchars($student->name) ?></td>
                                            <td><?= htmlspecialchars($student->birthday) ?></td>
                                            <td>
                                                <a href="student_view.php?id=<?= $student->id ?>" class="btn btn-sm btn-info">View</a>
                                                <?php if ($session->isAdmin()): ?>
                                                    <a href="student_edit.php?id=<?= $student->id ?>" class="btn btn-sm btn-warning">Edit</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info mt-4">
                    No students are currently enrolled in this section.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
