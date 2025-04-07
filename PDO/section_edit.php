<?php
require_once 'classes/Repository.php';
require_once 'includes/header.php';

$session = Session::getInstance();
$auth = new Auth(new UserRepository(Database::getInstance()->getPDO()), $session);
$auth->requireAdmin();

$id = $_GET['id'] ?? 0;
$sectionRepo = new SectionRepository(Database::getInstance()->getPDO());

$section = null;
$isEdit = false;

if ($id) {
    $section = $sectionRepo->findById($id);
    if (!$section) {
        header('Location: sections.php');
        exit;
    }
    $isEdit = true;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $designation = trim($_POST['designation'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    if (empty($designation)) {
        $errors[] = 'Designation is required';
    }
    
    if (empty($errors)) {
        $data = [
            'designation' => $designation,
            'description' => $description
        ];
        
        if ($isEdit) {
            $stmt = $sectionRepo->pdo->prepare("UPDATE sections SET designation = :designation, description = :description WHERE id = :id");
            $stmt->bindValue(':id', $id);
            $stmt->bindValue(':designation', $designation);
            $stmt->bindValue(':description', $description);
            $success = $stmt->execute();
        } else {
            $success = $sectionRepo->create($data);
        }
        
        if ($success) {
            header('Location: sections.php?message=' . ($isEdit ? 'Section updated successfully' : 'Section added successfully'));
            exit;
        } else {
            $errors[] = 'Error ' . ($isEdit ? 'updating' : 'adding') . ' section';
        }
    }
}
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0"><?= $isEdit ? 'Edit' : 'Add' ?> Section</h2>
                </div>
                
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post">
                        <div class="mb-3">
                            <label for="designation" class="form-label">Designation</label>
                            <input type="text" class="form-control" id="designation" name="designation" value="<?= htmlspecialchars($section->designation ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($section->description ?? '') ?></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="sections.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Update' : 'Add' ?> Section</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
