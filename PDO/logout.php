<?php
require_once 'classes/Repository.php';

$session = Session::getInstance();
$auth = new Auth(new UserRepository(Database::getInstance()->getPDO()), $session);
$auth->logout();

header('Location: login.php');
exit;
