<?php
require_once 'classes/Repository.php';

$session = Session::getInstance();
$auth = new Auth(new UserRepository(Database::getInstance()->getPDO()), $session);
$auth->requireAdmin();

$id = $_GET['id'] ?? 0;
if (!$id) {
    header('Location: sections.php');
    exit;
}

$sectionRepo = new SectionRepository(Database::getInstance()->getPDO());
$section = $sectionRepo->findById($id);

if (!$section) {
    header('Location: sections.php');
    exit;
}

$success = $sectionRepo->delete($id);
header('Location: sections.php?message=' . ($success ? 'Section deleted successfully' : 'Error deleting section'));
exit;
