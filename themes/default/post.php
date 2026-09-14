<?php
// themes/default/post.php

// Получаем ID страницы из URL (например, ?page_id=1)
$pageId = isset($_GET['page_id']) ? (int)$_GET['page_id'] : 0;

// ЗАГОТОВКА: В будущем здесь будет запрос к БД через DataBase::getInstance()
// Пока используем тестовые данные, чтобы проверить работу JS
$postTitle = "Статья №" . $pageId;
$postContent = "Это тестовый контент статьи. Ниже должна появиться форма комментариев.";
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($postTitle) ?></title>
    <!-- Подключаем основные стили сайта -->
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <div class="container" style="max-width: 800px; margin: 40px auto; padding: 0 20px;">
        
        <!-- Заголовок и текст статьи -->
        <h1><?= htmlspecialchars($postTitle) ?></h1>
        <div class="article-content" style="margin-bottom: 40px; line-height: 1.6;">
            <?= nl2br(htmlspecialchars($postContent)) ?>
        </div>

        <!-- ========================================== -->
        <!--          БЛОК КОММЕНТАРИЕВ (JS)            -->
        <!-- ========================================== -->
        <div id="comments-widget" data-post-id="<?= $pageId ?>" style="border-top: 2px solid #eee; padding-top: 30px;">
            <h3>💬 Комментарии</h3>
            
            <!-- Форма добавления комментария -->
            <form id="comment-form" style="display: flex; flex-direction: column; gap: 12px; max-width: 600px; margin-bottom: 30px;">
                <input 
                    type="text" 
                    name="author_name" 
                    placeholder="Ваше имя" 
                    required 
                    maxlength="50"
                    style="padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;"
                >
                <textarea 
                    name="content" 
                    placeholder="Напишите ваш комментарий..." 
                    required 
                    maxlength="1000" 
                    rows="4"
                    style="padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; resize: vertical;"
                ></textarea>
                <button 
                    type="submit" 
                    id="submit-btn" 
                    style="padding: 12px 24px; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; width: fit-content;"
                >
                    Отправить комментарий
                </button>
                <p class="form-message" style="display: none; margin: 0; font-size: 14px;"></p>
            </form>

            <!-- Сюда JS будет загружать список комментариев -->
            <div id="comments-list">
                <p style="color: #888;">Загрузка комментариев...</p>
            </div>
        </div>
        <!-- ========================================== -->

    </div>

    <!-- Подключаем наш JS-модуль комментариев -->
    <script src="/js/CommentSystem.js"></script>
    
    <!-- Инициализируем систему после загрузки DOM -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const widget = document.getElementById('comments-widget');
            if (widget) {
                // Передаем элемент виджета в класс CommentSystem
                new CommentSystem(widget);
            }
        });
    </script>
</body>
</html>