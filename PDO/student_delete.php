<?php
require_once 'classes/Repository.php';

$session = Session::getInstance();
$auth = new Auth(new UserRepository(Database::getInstance()->getPDO()), $session);
$auth->requireAdmin();

$id = $_GET['id'] ?? 0;
if (!$id) {
    header('Location: students.php');
    exit;
}

$studentRepo = new StudentRepository(Database::getInstance()->getPDO());
$student = $studentRepo->findById($id);

if (!$student) {
    header('Location: students.php');
    exit;
}

$success = $studentRepo->delete($id);
header('Location: students.php?message=' . ($success ? 'Student deleted successfully' : 'Error deleting student'));
exit;
