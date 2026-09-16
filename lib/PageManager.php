<?php
/**
 * Класс DraftManager
 * 
 * Реализует задачу #244: "Черновики и отложенная публикация".
 * Позволяет сохранять страницы как черновики и публиковать их по расписанию.
 * 
 * @package s-cms
 * @subpackage lib
 */
class DraftManager
{
    /**
     * @var PDO Объект подключения к БД
     */
    private $db;

    /**
     * Конструктор. Принимает соединение с БД.
     *
     * @param PDO $db
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Сохраняет страницу с учетом статуса и отложенной даты.
     *
     * @param array $data Массив с полями: id, title, content, status, published_at
     * @return bool
     */
    public function savePage(array $data): bool
    {
        $id          = $data['id'] ?? null;
        $title       = $data['title'] ?? '';
        $content     = $data['content'] ?? '';
        $status      = $data['status'] ?? 'draft';
        $publishedAt = !empty($data['published_at']) ? $data['published_at'] : null;

        // Валидация статуса (защита от подмены в форме)
        if (!in_array($status, ['draft', 'published'], true)) {
            $status = 'draft';
        }

        // Если дата в прошлом — сбрасываем её, чтобы страница опубликовалась сразу
        if ($publishedAt !== null && strtotime($publishedAt) <= time()) {
            $publishedAt = null;
        }

        if ($id) {
            // Обновление существующей страницы
            $sql = "UPDATE pages 
                    SET title = :title, 
                        content = :content, 
                        status = :status, 
                        published_at = :published_at 
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':title'        => $title,
                ':content'      => $content,
                ':status'       => $status,
                ':published_at' => $publishedAt,
                ':id'           => $id,
            ]);
        }

        // Создание новой страницы
        $sql = "INSERT INTO pages (title, content, status, published_at) 
                VALUES (:title, :content, :status, :published_at)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title'        => $title,
            ':content'      => $content,
            ':status'       => $status,
            ':published_at' => $publishedAt,
        ]);
    }

    /**
     * Возвращает только те страницы, которые можно показывать на сайте:
     * статус "published" и (дата публикации пуста ИЛИ уже наступила).
     *
     * @return array
     */
    public function getVisiblePages(): array
    {
        $sql = "SELECT * FROM pages 
                WHERE status = 'published' 
                  AND (published_at IS NULL OR published_at <= NOW())
                ORDER BY published_at DESC, id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Возвращает все страницы для админки (включая черновики и отложенные).
     *
     * @return array
     */
    public function getAllPagesForAdmin(): array
    {
        $sql = "SELECT * FROM pages ORDER BY id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Автоматически публикует страницы, у которых наступила дата отложенной публикации.
     * Можно вызывать по Cron или при заходе в админку.
     *
     * @return int Количество опубликованных страниц
     */
    public function publishScheduled(): int
    {
        $sql = "UPDATE pages 
                SET status = 'published', published_at = NULL 
                WHERE status = 'published' 
                  AND published_at IS NOT NULL 
                  AND published_at <= NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Проверяет, является ли страница черновиком.
     *
     * @param array $page
     * @return bool
     */
    public function isDraft(array $page): bool
    {
        return ($page['status'] ?? 'draft') === 'draft';
    }

    /**
     * Проверяет, отложена ли публикация страницы.
     *
     * @param array $page
     * @return bool
     */
    public function isScheduled(array $page): bool
    {
        return ($page['status'] ?? '') === 'published'
            && !empty($page['published_at'])
            && strtotime($page['published_at']) > time();
    }
}