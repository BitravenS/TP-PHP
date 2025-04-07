<?php
$db_host = 'localhost';
$db_name = 'student_management';
$db_user = 'root';
$db_pass = '';

// Create the database and tables
try {
    $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $db_name");
    $pdo->exec("USE $db_name");
    
    // Create users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'user') NOT NULL DEFAULT 'user'
        )
    ");
    
    // Create sections table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sections (
            id INT AUTO_INCREMENT PRIMARY KEY,
            designation VARCHAR(100) NOT NULL,
            description TEXT
        )
    ");
    
    // Create students table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS students (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            birthday DATE,
            image VARCHAR(255),
            section_id INT,
            FOREIGN KEY (section_id) REFERENCES sections(id)
        )
    ");
    
    // Insert admin user if not exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES ('admin', 'admin@example.com', :password, 'admin')");
        $stmt->bindValue(':password', $hashedPassword);
        $stmt->execute();
    }
    
    // Insert user if not exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'malek'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $hashedPassword = password_hash('malektababi', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES ('user', 'user@example.com', :password, 'user')");
        $stmt->bindValue(':password', $hashedPassword);
        $stmt->execute();
    }
    
    // Insert sample sections
    $sections = [
        ['designation' => 'Computer Science', 'description' => 'For students interested in programming and software development'],
        ['designation' => 'Engineering', 'description' => 'For students interested in building and designing solutions'],
        ['designation' => 'Business', 'description' => 'For students interested in management and entrepreneurship']
    ];
    
    foreach ($sections as $section) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM sections WHERE designation = :designation");
        $stmt->bindValue(':designation', $section['designation']);
        $stmt->execute();
        
        if ($stmt->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO sections (designation, description) VALUES (:designation, :description)");
            $stmt->bindValue(':designation', $section['designation']);
            $stmt->bindValue(':description', $section['description']);
            $stmt->execute();
        }
    }
    
    // Insert sample students
    $students = [
        ['name' => 'John Doe', 'birthday' => '2000-01-15', 'image' => 'avatar1.png', 'section_id' => 1],
        ['name' => 'Jane Smith', 'birthday' => '2001-05-22', 'image' => 'avatar2.png', 'section_id' => 1],
        ['name' => 'Mike Johnson', 'birthday' => '1999-11-30', 'image' => 'avatar3.png', 'section_id' => 2],
        ['name' => 'Sarah Williams', 'birthday' => '2002-09-18', 'image' => 'avatar4.png', 'section_id' => 2],
        ['name' => 'David Brown', 'birthday' => '2001-03-25', 'image' => 'avatar5.png', 'section_id' => 3]
    ];
    
    foreach ($students as $student) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE name = :name AND birthday = :birthday");
        $stmt->bindValue(':name', $student['name']);
        $stmt->bindValue(':birthday', $student['birthday']);
        $stmt->execute();
        
        if ($stmt->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO students (name, birthday, image, section_id) VALUES (:name, :birthday, :image, :section_id)");
            $stmt->bindValue(':name', $student['name']);
            $stmt->bindValue(':birthday', $student['birthday']);
            $stmt->bindValue(':image', $student['image']);
            $stmt->bindValue(':section_id', $student['section_id']);
            $stmt->execute();
        }
    }
    
    echo "Database and tables created successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
