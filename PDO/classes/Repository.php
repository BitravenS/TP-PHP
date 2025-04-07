<?php
class Repository {
    protected $pdo;
    protected $table;
    protected $entityClass;

    public function __construct(PDO $pdo, string $table, string $entityClass = null) {
        $this->pdo = $pdo;
        $this->table = $table;
        $this->entityClass = $entityClass;
    }

    public function findAll(int $limit = null, int $offset = null): array {
        $sql = "SELECT * FROM {$this->table}";
        
        if ($limit !== null) {
            $sql .= " LIMIT :limit";
            if ($offset !== null) {
                $sql .= " OFFSET :offset";
            }
        }
        
        $stmt = $this->pdo->prepare($sql);
        
        if ($limit !== null) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            if ($offset !== null) {
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            }
        }
        
        $stmt->execute();
        
        if ($this->entityClass) {
            return $stmt->fetchAll(PDO::FETCH_CLASS, $this->entityClass);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($this->entityClass) {
            $stmt->setFetchMode(PDO::FETCH_CLASS, $this->entityClass);
        }
        
        return $stmt->fetch();
    }

    public function create(array $data) {
        $columns = array_keys($data);
        $placeholders = array_map(function($column) {
            return ":$column";
        }, $columns);
        
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
        
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($data as $column => $value) {
            $stmt->bindValue(":$column", $value);
        }
        
        if ($stmt->execute()) {
            return $this->pdo->lastInsertId();
        }
        
        return false;
    }

    public function delete(int $id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function count(): int {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM {$this->table}");
        return (int)$stmt->fetchColumn();
    }
    
    public function findBy(string $column, $value) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE {$column} = :value");
        $stmt->bindValue(':value', $value);
        $stmt->execute();
        
        if ($this->entityClass) {
            $stmt->setFetchMode(PDO::FETCH_CLASS, $this->entityClass);
        }
        
        return $stmt->fetchAll();
    }
}

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        require_once 'config.php';
        $this->pdo = new PDO(
            "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
            $db_user,
            $db_pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPDO() {
        return $this->pdo;
    }
}

class User {
    public $id;
    public $username;
    public $email;
    public $password;
    public $role;

    public function isAdmin() {
        return $this->role === 'admin';
    }
}

class UserRepository extends Repository {
    public function __construct(PDO $pdo) {
        parent::__construct($pdo, 'users', User::class);
    }

    public function findByUsername(string $username) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE username = :username");
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, $this->entityClass);
        return $stmt->fetch();
    }
    
    public function checkCredentials(string $username, string $password) {
        $user = $this->findByUsername($username);
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return false;
    }
}

class Student {
    public $id;
    public $name;
    public $birthday;
    public $image;
    public $section_id;
}

class StudentRepository extends Repository {
    public function __construct(PDO $pdo) {
        parent::__construct($pdo, 'students', Student::class);
    }
    
    public function findBySection(int $sectionId) {
        return $this->findBy('section_id', $sectionId);
    }
    
    public function findByName(string $name) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE name LIKE :name");
        $stmt->bindValue(':name', "%$name%");
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, $this->entityClass);
        return $stmt->fetchAll();
    }
}

class Section {
    public $id;
    public $designation;
    public $description;
}

class SectionRepository extends Repository {
    public function __construct(PDO $pdo) {
        parent::__construct($pdo, 'sections', Section::class);
    }
}

class Session {
    private static $instance = null;

    private function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public function has($key) {
        return isset($_SESSION[$key]);
    }

    public function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public function destroy() {
        session_destroy();
    }
    
    public function getUser() {
        return $this->get('user');
    }
    
    public function isLoggedIn() {
        return $this->has('user');
    }
    
    public function isAdmin() {
        $user = $this->getUser();
        return $user && $user->role === 'admin';
    }
}

class Auth {
    private $userRepository;
    private $session;

    public function __construct(UserRepository $userRepository, Session $session) {
        $this->userRepository = $userRepository;
        $this->session = $session;
    }

    public function login(string $username, string $password) {
        $user = $this->userRepository->checkCredentials($username, $password);
        if ($user) {
            $this->session->set('user', $user);
            return true;
        }
        return false;
    }

    public function logout() {
        $this->session->remove('user');
    }

    public function isLoggedIn() {
        return $this->session->isLoggedIn();
    }

    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }

    public function requireAdmin() {
        $this->requireLogin();
        if (!$this->session->isAdmin()) {
            header('Location: index.php?error=unauthorized');
            exit;
        }
    }
}
