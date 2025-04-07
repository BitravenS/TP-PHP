<?php
require_once 'classes/Repository.php';
require_once 'includes/header.php';

$session = Session::getInstance();
$auth = new Auth(new UserRepository(Database::getInstance()->getPDO()), $session);
$auth->requireLogin();

$isAdmin = $session->isAdmin();
$studentRepo = new StudentRepository(Database::getInstance()->getPDO());
$sectionRepo = new SectionRepository(Database::getInstance()->getPDO());

$sections = $sectionRepo->findAll();
$sectionsMap = [];
foreach ($sections as $section) {
    $sectionsMap[$section->id] = $section->designation;
}

$filter = $_GET['filter'] ?? '';
if (!empty($filter)) {
    $students = $studentRepo->findByName($filter);
} else {
    $students = $studentRepo->findAll();
}

$message = '';
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Students</h1>
        <?php if ($isAdmin): ?>
            <a href="student_edit.php" class="btn btn-primary">Add New Student</a>
        <?php endif; ?>
    </div>
    
    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="mb-3">
                <form method="get" class="d-flex">
                    <input type="text" name="filter" class="form-control me-2" placeholder="Search by name..." value="<?= htmlspecialchars($filter) ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <?php if ($filter): ?>
                        <a href="students.php" class="btn btn-secondary ms-2">Clear</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <table id="students-table" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Birthday</th>
                        <th>Section</th>
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
                            <td><?= htmlspecialchars($sectionsMap[$student->section_id] ?? 'N/A') ?></td>
                            <td>
                                <a href="student_view.php?id=<?= $student->id ?>" class="btn btn-sm btn-info">View</a>
                                <?php if ($isAdmin): ?>
                                    <a href="student_edit.php?id=<?= $student->id ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="student_delete.php?id=<?= $student->id ?>" class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="mt-3">
                <button id="export-excel" class="btn btn-success">Export to Excel</button>
                <button id="export-csv" class="btn btn-info">Export to CSV</button>
                <button id="export-pdf" class="btn btn-danger">Export to PDF</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var table = $('#students-table').DataTable({
        responsive: true,
        dom: 'Bfrtip',
        pageLength: 10,
        order: [[0, 'asc']]
    });
    
    $('#export-excel').on('click', function() {
        table.button('.buttons-excel').trigger();
    });
    
    $('#export-csv').on('click', function() {
        table.button('.buttons-csv').trigger();
    });
    
    $('#export-pdf').on('click', function() {
        table.button('.buttons-pdf').trigger();
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
