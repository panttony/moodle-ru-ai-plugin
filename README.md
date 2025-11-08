# moodle-ru-ai-plugin

Этот плагин предоставляет интеграцию YandexGPT с AI-Manager. Его можно использовать как альтернативу подсистеме AI, поставляемой с Moodle 4.5.

## Зависимости

- Docker

- Make (опционально для удобного запуска)

## Доступные цели:

```
make up          - Запустить контейнеры (detached)
make down        - Остановить и удалить контейнеры
make restart     - Перезапустить контейнеры
make clean       - Остановить и удалить контейнеры + анонимные тома
make zip         - Собрать ZIP плагинов в каталоге dist/
make clean-dist  - Удалить артефакты сборки (dist/)
```

## Быстрый старт (локально с помощью Docker)

0. Склонируйте репозиторий с сабмодулями

```bash
git clone --depth 1 \
    --recurse-submodules --shallow-submodules \
    https://github.com/artem-burashnikov/moodle-ru-ai-plugin.git
```

1. Создайте `zip-архивы` из плагинов

```bash
make zip
```

<img src="assets/images/makezip.png" width="400">

2. Запустите окружение

```bash
make up
```

<img src="assets/images/makeup.png" width="400">

3. После запуска Moodle будет доступен по адресу

- URL: http://localhost:8080
- **username**: user
- **password**: bitnami

<img src="assets/images/loginpage.png" width="400">

4. Загрузите ZIP‑файлы с необходимыми плагинами. Дополнительные сведения будут запрошены, только если тип плагина не будет определён автоматически.

<img src="assets/images/plugin.png" width="800">

## Лицензия

[GNU GPL v3](LICENSE)
