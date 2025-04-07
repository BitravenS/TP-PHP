<?php
require_once 'classes/Repository.php';
require_once 'includes/header.php';

$session = Session::getInstance();
$auth = new Auth(new UserRepository(Database::getInstance()->getPDO()), $session);
$auth->requireAdmin();

$id = $_GET['id'] ?? 0;
$studentRepo = new StudentRepository(Database::getInstance()->getPDO());
$sectionRepo = new SectionRepository(Database::getInstance()->getPDO());

$student = null;
$isEdit = false;

if ($id) {
    $student = $studentRepo->findById($id);
    if (!$student) {
        header('Location: students.php');
        exit;
    }
    $isEdit = true;
}

$sections = $sectionRepo->findAll();
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $birthday = trim($_POST['birthday'] ?? '');
    $section_id = (int)($_POST['section_id'] ?? 0);
    
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($birthday)) {
        $errors[] = 'Birthday is required';
    }
    
    if ($section_id <= 0) {
        $errors[] = 'Valid section is required';
    }
    
    $image = $student->image ?? 'avatar-default.png';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $uploadName = $_FILES['image']['name'];
        $uploadTmpName = $_FILES['image']['tmp_name'];
        $uploadSize = $_FILES['image']['size'];
        $uploadError = $_FILES['image']['error'];
        
        $extension = strtolower(pathinfo($uploadName, PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowed)) {
            $errors[] = 'Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.';
        } elseif ($uploadSize > 5000000) {
            $errors[] = 'File size exceeds the maximum limit (5MB).';
        } else {
            $newFilename = uniqid() . '.' . $extension;
            $destination = 'images/' . $newFilename;
            
            if (move_uploaded_file($uploadTmpName, $destination)) {
                $image = $newFilename;
            } else {
                $errors[] = 'Failed to upload the image.';
            }
        }
    }
    
    if (empty($errors)) {
        $data = [
            'name' => $name,
            'birthday' => $birthday,
            'section_id' => $section_id,
            'image' => $image
        ];
        
        if ($isEdit) {
            $stmt = $studentRepo->pdo->prepare("UPDATE students SET name = :name, birthday = :birthday, section_id = :section_id, image = :image WHERE id = :id");
            $stmt->bindValue(':id', $id);
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':birthday', $birthday);
            $stmt->bindValue(':section_id', $section_id);
            $stmt->bindValue(':image', $image);
            $success = $stmt->execute();
        } else {
            $success = $studentRepo->create($data);
        }
        
        if ($success) {
            header('Location: students.php?message=' . ($isEdit ? 'Student updated successfully' : 'Student added successfully'));
            exit;
        } else {
            $errors[] = 'Error ' . ($isEdit ? 'updating' : 'adding') . ' student';
        }
    }
}
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0"><?= $isEdit ? 'Edit' : 'Add' ?> Student</h2>
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
                    
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($student->name ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="birthday" class="form-label">Birthday</label>
                            <input type="date" class="form-control" id="birthday" name="birthday" value="<?= htmlspecialchars($student->birthday ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="section_id" class="form-label">Section</label>
                            <select class="form-select" id="section_id" name="section_id" required>
                                <option value="">Select Section</option>
                                <?php foreach ($sections as $section): ?>
                                    <option value="<?= $section->id ?>" <?= (isset($student->section_id) && $student->section_id == $section->id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($section->designation) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Profile Image</label>
                            <?php if (isset($student->image)): ?>
                                <div class="mb-2">
                                    <img src="images/<?= htmlspecialchars($student->image) ?>" alt="Current Image" class="img-thumbnail" style="max-width: 100px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" id="image" name="image">
                            <small class="text-muted">Leave empty to keep current image</small>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="students.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Update' : 'Add' ?> Student</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
