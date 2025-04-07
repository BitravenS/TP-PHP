<?php
require_once 'classes/Repository.php';
require_once 'includes/header.php';

$session = Session::getInstance();
$auth = new Auth(new UserRepository(Database::getInstance()->getPDO()), $session);
$auth->requireLogin();

$isAdmin = $session->isAdmin();
$sectionRepo = new SectionRepository(Database::getInstance()->getPDO());
$studentRepo = new StudentRepository(Database::getInstance()->getPDO());

$sections = $sectionRepo->findAll();

$message = '';
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Sections</h1>
        <?php if ($isAdmin): ?>
            <a href="section_edit.php" class="btn btn-primary">Add New Section</a>
        <?php endif; ?>
    </div>
    
    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    
    <div class="card shadow-sm">
        <div class="card-body">
            <table id="sections-table" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Designation</th>
                        <th>Description</th>
                        <th>Students Count</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sections as $section): ?>
                        <?php 
                            $students = $studentRepo->findBySection($section->id);
                            $studentCount = count($students);
                        ?>
                        <tr>
                            <td><?= $section->id ?></td>
                            <td><?= htmlspecialchars($section->designation) ?></td>
                            <td><?= htmlspecialchars($section->description) ?></td>
                            <td><?= $studentCount ?></td>
                            <td>
                                <a href="section_view.php?id=<?= $section->id ?>" class="btn btn-sm btn-info">View</a>
                                <?php if ($isAdmin): ?>
                                    <a href="section_edit.php?id=<?= $section->id ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="section_delete.php?id=<?= $section->id ?>" class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this section? This may affect associated students.')">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#sections-table').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[0, 'asc']]
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
