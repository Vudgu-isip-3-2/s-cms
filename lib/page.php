<?php
class Page {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Получить все страницы для админки (включая черновики)
    public function getAllForAdmin() {
        $stmt = $this->db->query("SELECT * FROM pages ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Получить только опубликованные страницы для сайта
    public function getPublished() {
        $stmt = $this->db->prepare("
            SELECT * FROM pages 
            WHERE status = 'published' 
              AND (published_at IS NULL OR published_at <= NOW())
            ORDER BY published_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Сохранить страницу (создание или обновление)
    public function save($data) {
        $status = $data['status'] ?? 'draft';
        $publishedAt = !empty($data['published_at']) ? $data['published_at'] : null;

        if (!empty($data['id'])) {
            // Обновление
            $stmt = $this->db->prepare("
                UPDATE pages 
                SET title = :title, content = :content, status = :status, published_at = :published_at 
                WHERE id = :id
            ");
            $stmt->execute([
                ':title' => $data['title'],
                ':content' => $data['content'],
                ':status' => $status,
                ':published_at' => $publishedAt,
                ':id' => $data['id']
            ]);
        } else {
            // Создание
            $stmt = $this->db->prepare("
                INSERT INTO pages (title, content, status, published_at) 
                VALUES (:title, :content, :status, :published_at)
            ");
            $stmt->execute([
                ':title' => $data['title'],
                ':content' => $data['content'],
                ':status' => $status,
                ':published_at' => $publishedAt
            ]);
        }
    }
}