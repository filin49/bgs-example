### Запуск проекта
На запуск контейнера с php-fpm стоит задержка в 20 секунд, т.к. при первом запуске
нужно дождаться инициализации базы данных.

#### Запуск:
```sh
cp .env.example .env
docker-compose up
```

После запуска, API документация (Swagger) будет доступна по адресу:
http://localhost/api/documentation

Bearer токен для доступа - "123"

#### Запуск тестов
```sh
docker-compose run php7 ./vendor/bin/phpunit
```

### Перезапуск со сбросом изменений в БД:
```sh
docker-compose down -v
docker-compose up
```
