COMPOSE ?= docker compose

PROJECT_NAME ?= moodle-ai
PLUGIN_NAME ?= moodle-ru-ai-plugin
PLUGIN_DIR := ai_manager
DIST_DIR ?= dist
ZIP_FILE ?= $(DIST_DIR)/$(PLUGIN_NAME).zip

.PHONY: help up down restart clean zip dist clean-dist

help:
	@echo "Доступные цели:"
	@echo "  make up          - Запустить контейнеры (detached)"
	@echo "  make down        - Остановить и удалить контейнеры"
	@echo "  make restart     - Перезапустить контейнеры"
	@echo "  make clean       - Остановить и удалить контейнеры + анонимные тома"
	@echo "  make zip         - Собрать ZIP плагина в каталоге dist/"
	@echo "  make clean-dist  - Удалить артефакты сборки (dist/)"

up:
	$(COMPOSE) -p $(PROJECT_NAME) up -d

down:
	$(COMPOSE) -p $(PROJECT_NAME) down

restart: down up

clean:
	$(COMPOSE) -p $(PROJECT_NAME) down -v

dist:
	mkdir -p $(DIST_DIR)

zip: dist
	@echo "==> Packaging plugin into archive $(ZIP_FILE)"
	@rm -f $(ZIP_FILE)
	@zip -rq "$(ZIP_FILE)" "$(PLUGIN_DIR)"
	@echo "Done: $(ZIP_FILE)"

clean-dist:
	rm -rf $(DIST_DIR)
