<?php
// lib/Comment.php
require_once __DIR__ . '/DataBase.php';

class Comment {
    private $db;

    public function __construct() {
        // Параметры взяты из Main.php для корректной работы в Docker
        $this->db = DataBase::getInstance('mysql', 's-cms', 's-cms', 'secret'); 
    }

    // Получить одобренные комментарии
    public function getApprovedByPostId($postId) {
        $sql = "SELECT c.*, u.username 
                FROM comments c 
                LEFT JOIN user u ON c.user_id = u.id 
                WHERE c.post_id = :post_id AND c.status = 'approved' 
                ORDER BY c.created_at ASC";
        return $this->db->query($sql, [':post_id' => $postId]);
    }

    // Создать комментарий
    public function create($postId, $content, $userId = null, $authorName = null) {
        $sql = "INSERT INTO comments (post_id, user_id, author_name, content, status) 
                VALUES