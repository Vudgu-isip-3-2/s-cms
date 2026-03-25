# s-cms
Создать обработчик формы загрузки медиафайлов на сервер. Настроить валидацию данных из формы
Добавить форму логина
Добавьте в таблицу posts поле created_at с типом

## Запуск в docker 
Достаточно использовать команду 
```
docker compose up
```

После того как контейнер будет собран и запущен можно будет подключитья по адресу http://localhost:8086/
## Структура проекта
s-cms/  
├── docker-compose.yml 
├── README.md 
│  
├── docs/ 
│       │ 
│       │ 
│       └── documentation.md 
│  
├── env/
│       │
│       │ 
│       ├── 000-default.conf 
│       │ 
│       └── dockerfile
│ 
│  
├── lib/ 
│      │  
│      │ 
│      ├── autoloader.php 
│      │ 
│      │ 
│      └── Router.php 
│ 
│ 
│  
└── public/ 
    │ 
    │ 
    └── index.php 
