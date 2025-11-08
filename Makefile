COMPOSE ?= docker compose

PROJECT_NAME ?= moodle-ai
PLUGIN_NAME ?= moodle-ru-ai-plugin
PLUGINS_DIRS := ai_manager moodle-block_ai_chat moodle-block_ai_control moodle-qbank_questiongen moodle-qtype_aitext moodle-tiny_ai
DIST_DIR ?= dist

.PHONY: help up down restart clean zip dist clean-dist

help:
	@echo "Доступные цели:"
	@echo "  make up          - Запустить контейнеры (detached)"
	@echo "  make down        - Остановить и удалить контейнеры"
	@echo "  make restart     - Перезапустить контейнеры"
	@echo "  make clean       - Остановить и удалить контейнеры + анонимные тома"
	@echo "  make zip         - Собрать ZIP архивы для каждого плагина в каталоге dist/"
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
	@echo "==> Packaging plugins into separate archives"
	@rm -f $(DIST_DIR)/*.zip
	@for dir in $(PLUGINS_DIRS); do \
		echo "  -> $$dir.zip"; \
		zip -rq "$(DIST_DIR)/$$dir.zip" "$$dir"; \
	done
	@echo "Done: all plugins packaged in $(DIST_DIR)/"

clean-dist:
	rm -rf $(DIST_DIR)
