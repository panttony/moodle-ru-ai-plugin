# moodle-ru-ai-plugin

Этот плагин предоставляет интеграцию YandexGPT с AI-Manager. Его можно использовать как альтернативу подсистеме AI, поставляемой с Moodle 4.5.

## Зависимости

- [Сервисный аккаунт Yandex Cloud](https://yandex.cloud/ru/docs/iam/concepts/users/service-accounts)

- [Идентификатор каталога Yandex Cloud](https://yandex.cloud/ru/docs/resource-manager/operations/folder/get-id)

- [Yandex Cloud API Key](https://yandex.cloud/ru/docs/ai-studio/operations/get-api-key)

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
make inject      - Развернуть все плагины в контейнер Moodle
```

## Быстрый старт (локально с помощью Docker)

0. **Склонируйте репозиторий с сабмодулями**

```bash
git clone --depth 1 \
    --recurse-submodules --shallow-submodules \
    https://github.com/artem-burashnikov/moodle-ru-ai-plugin.git
```

1. **Запустите окружение**

```bash
make up
```

<img src="assets/images/makeup.png" width="400">

2. **После запуска Moodle будет доступен по адресу**

- URL: http://localhost:8080
- **username**: user
- **password**: bitnami

<img src="assets/images/loginpage.png" width="400">

3. **Установите плагины в контейнер с помощью команды**

```bash
make inject
```

<img src="assets/images/inject.png" width="500">

## Установка на сервер вручную

1. **Создайте `zip-архивы` из плагинов**

```bash
make zip
```

<img src="assets/images/makezip.png" width="400">

2. **Добавьте архивы через панель администратора и следуйте указаниям помощника установки**

<img src="assets/images/plugin.png" width="800">

## Первичная найстройка для использования YandexGPT в качестве поставщика ИИ

1. **Перейдите в раздел `AI tools administration` и включите AI Tools**

<img src="assets/images/newmenu.png" width="800">

2. **Добавьте YandexGPT**

<img src="assets/images/tools.png" width="800">

3. **Внесите ваши данные из Yandex Cloud: API-ключ и ID каталога**

4. **Настройте необходимые поля**

5. **Пользуйтесь**

<img src="assets/images/chat.jpg" width="400">


## Лицензия

[GNU GPL v3](LICENSE)
