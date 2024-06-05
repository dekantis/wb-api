<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>


# Тестовое API на фреймворке Laravel

### Созданы миграции на добавление таблиц в базу данных

`php artisan migrate`

### Реализован сервис для обработки данных перед сохранением в базу данных

#### App\Services\WbApiService

### Реализовано сохранение сущностей 

`php artisan db:seed`

### Доступы к базе данных
#### DB_CONNECTION=mysql
#### DB_HOST=sql.freedb.tech
#### DB_PORT=3306
#### DB_DATABASE=freedb_wb_api
#### DB_USERNAME=freedb_wb_api
#### DB_PASSWORD=hgG5tW!@qjGK3e3


### Названия таблиц в базе данных

### -- из коробки

#### failed_jobs
#### migrations
#### orders
#### password_resets
#### personal_access_tokens
#### users

### -- созданные в результате выполнения задачи

#### orders
#### incomes
#### stocks
#### sales



