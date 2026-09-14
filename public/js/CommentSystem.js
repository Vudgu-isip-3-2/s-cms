// public/js/CommentSystem.js

class CommentSystem {
    constructor(containerElement) {
        this.container = containerElement;
        this.postId = this.container.getAttribute('data-post-id');
        this.apiEndpoint = '/api/comments.php';
        
        // Собираем элементы формы и списка
        this.elements = {
            form: this.container.querySelector('#comment-form'),
            list: this.container.querySelector('#comments-list'),
            submitBtn: this.container.querySelector('#submit-btn'),
            message: this.container.querySelector('.form-message')
        };

        this.init();
    }

    init() {
        // Загружаем существующие комментарии при старте
        this.fetchAndRenderComments();
        
        // Вешаем обработчик на отправку формы
        if (this.elements.form) {
            this.elements.form.addEventListener('submit', (e) => this.handleSubmit(e));
        }
    }

    async fetchAndRenderComments() {
        try {
            const response = await fetch(`${this.apiEndpoint}?post_id=${this.postId}`);
            const result = await response.json();

            if (result.success && Array.isArray(result.data)) {
                this.renderComments(result.data);
            } else {
                this.elements.list.innerHTML = '<p class="error">Не удалось загрузить комментарии.</p>';
            }
        } catch (error) {
            console.error('Ошибка загрузки:', error);
            this.elements.list.innerHTML = '<p class="error">Ошибка сети при загрузке.</p>';
        }
    }

    renderComments(comments) {
        if (!comments || comments.length === 0) {
            this.elements.list.innerHTML = '<p class="empty-state">Комментариев пока нет. Будьте первым!</p>';
            return;
        }

        // Генерируем HTML для каждого комментария
        const html = comments.map(comment => {
            const author = this.escapeHtml(comment.username || comment.author_name || 'Аноним');
            const date = new Date(comment.created_at).toLocaleString('ru-RU', {
                day: 'numeric', month: 'long', year: 'numeric', 
                hour: '2-digit', minute: '2-digit'
            });
            const content = this.escapeHtml(comment.content);

            return `
                <article class="comment-item" style="margin-bottom: 15px; padding: 10px; border-bottom: 1px solid #eee;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                        <strong>${author}</strong>
                        <span style="color:#888; font-size:0.9em;">${date}</span>
                    </div>
                    <div style="line-height:1.5;">${content}</div>
                </article>
            `;
        }).join('');

        this.elements.list.innerHTML = html;
    }

    async handleSubmit(event) {
        event.preventDefault(); // Останавливаем стандартную перезагрузку страницы
        
        // Блокируем кнопку, чтобы не нажимали дважды
        this.elements.submitBtn.disabled = true;
        this.elements.submitBtn.textContent = 'Отправка...';
        this.showMessage('', 'none');

        const formData = new FormData(this.elements.form);
        const payload = {
            post_id: parseInt(this.postId, 10),
            author_name: formData.get('author_name').trim(),
            content: formData.get('content').trim()
        };

        try {
            const response = await fetch(this.apiEndpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await response.json();

            if (result.success) {
                this.showMessage('Комментарий отправлен на модерацию!', 'success');
                this.elements.form.reset(); // Очищаем форму
                // Можно раскомментировать строку ниже, чтобы сразу показать комментарий (если бы он был одобрен)
                // this.fetchAndRenderComments(); 
            } else {
                this.showMessage(result.error || 'Ошибка отправки.', 'error');
            }
        } catch (error) {
            console.error('Ошибка отправки:', error);
            this.showMessage('Ошибка сети. Попробуйте позже.', 'error');
        } finally {
            // Разблокируем кнопку
            this.elements.submitBtn.disabled = false;
            this.elements.submitBtn.textContent = 'Отправить';
        }
    }

    showMessage(text, type) {
        this.elements.message.textContent = text;
        this.elements.message.style.display = type === 'none' ? 'none' : 'block';
        this.elements.message.style.color = type === 'success' ? '#27ae60' : '#e74c3c';
        this.elements.message.style.marginTop = '10px';
    }

    // Защита от XSS (превращает <script> в безопасный текст)
    escapeHtml(unsafe) {
        if (typeof unsafe !== 'string') return '';
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
}