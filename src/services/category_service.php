<?php
useService('db');
global $pdo;

function getCategories() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCategoryById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createCategory($name) {
    global $pdo;
    $slug = generateCategorySlug($name);
    
    // Check if exists
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ? OR slug = ?");
    $stmt->execute([$name, $slug]);
    if ($stmt->fetch()) return false;

    $stmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
    $stmt->execute([$name, $slug]);
    return $pdo->lastInsertId();
}

function updateCategory($id, $name) {
    global $pdo;
    $slug = generateCategorySlug($name);
    $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ? WHERE id = ?");
    return $stmt->execute([$name, $slug, $id]);
}

function deleteCategory($id) {
    global $pdo;
    // Check if courses use this category
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE category_id = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) return false;

    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    return $stmt->execute([$id]);
}

function generateCategorySlug($name) {
    // Simple slugify that handles Thai better by just replacing spaces and special chars
    $slug = mb_strtolower($name, 'UTF-8');
    $slug = str_replace([' ', '/', '\\', '&', '?', '#', '%', '+'], '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

function getCourseCountByCategory($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE category_id = ?");
    $stmt->execute([$id]);
    return (int)$stmt->fetchColumn();
}
