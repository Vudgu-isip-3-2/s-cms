<?php
/**
 * Модель комментариев.
 * Работает в глобальном namespace — как DataBase, Config, Router, Main.
 * Namespace не используется намеренно: в проекте ручная автозагрузка.
 */
class Comment
{
    private DataBase $db;

    public function __construct(DataBase $db)
    {
        $this->db = $db;
    }

    public function getApproved(string $type, int $entityId): array
    {
        return $this->db->query(
            "SELECT c.*, u.username AS user_name
             FROM comments c
             LEFT JOIN `user` u ON u.id = c.user_id
             WHERE c.entity_type = :type
               AND c.entity_id   = :eid
               AND c.is_approved = 1
             ORDER BY c.created_at ASC",
            ['type' => $type, 'eid' => $entityId]
        );
    }

    public function getAllForAdmin(): array
    {
        return $this->db->query(
            "SELECT c.*, u.username AS user_name
             FROM comments c
             LEFT JOIN `user` u ON u.id = c.user_id
             ORDER BY c.is_approved ASC, c.created_at DESC"
        );
    }

    public function create(array $data): int
    {
        return (int) $this->db->insert('comments', [
            'entity_type' => $data['entity_type'],
            'entity_id'   => (int)$data['entity_id'],
            'user_id'     => $data['user_id']   ?? null,
            'parent_id'   => $data['parent_id'] ?? null,
            'body'        => $data['body'],
            'is_approved' => 0,
        ]);
    }

    public function approve(int $id): int
    {
        return $this->db->update('comments', ['is_approved' => 1], 'id = ?', [$id]);
    }

    public function reject(int $id): int
    {
        return $this->db->update('comments', ['is_approved' => 0], 'id = ?', [$id]);
    }

    public function delete(int $id): int
    {
        return $this->db->delete('comments', 'id = ?', [$id]);
    }

    public function find(int $id): ?array
    {
        $row = $this->db->queryOne("SELECT * FROM comments WHERE id = ?", [$id]);
        return $row ?: null;
    }

    /**
     * Проверка прав администратора через user_role + role.
     */
    public static function isAdmin(DataBase $db, int $userId): bool
    {
        $row = $db->queryOne(
            "SELECT 1
             FROM user_role ur
             JOIN role r ON r.id = ur.role_id
             WHERE ur.user_id = ? AND r.name IN ('admin','moderator')
             LIMIT 1",
            [$userId]
        );
        return !empty($row);
    }
}