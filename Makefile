.DEFAULT_GOAL := help
.PHONY: help

help: ## This help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

init: ## Initialize project
	$(MAKE) start
	$(MAKE) composer-install
	$(MAKE) db-migrate
	$(MAKE) db-fixture

start: ## Stop running project
	docker-compose up -d

stop: ## Stop running project
	docker-compose stop

delete: ## Delete project
	docker-compose down

reset: ## Reinitialize project
	$(MAKE) delete
	$(MAKE) init

restart: ## Restart project
	$(MAKE) stop
	$(MAKE) start

open: ## Open container
	docker-compose exec $(filter-out $@,$(MAKECMDGOALS)) bash

db-diff: ## Run create migration diff
	docker-compose exec php bin/console doctrine:migrations:diff --no-interaction

db-reset: ## Reset database to init version
	docker-compose exec php bin/console doctrine:database:drop --force --if-exists
	docker-compose exec php bin/console doctrine:database:drop --env test --force --if-exists
	$(MAKE) db-migrate
	$(MAKE) db-fixture

db-migrate: ## Run doctrine migrations
	docker-compose exec php bin/console doctrine:database:create --no-interaction --if-not-exists
	docker-compose exec php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
	docker-compose exec php bin/console doctrine:database:create --env test --no-interaction --if-not-exists
	docker-compose exec php bin/console doctrine:migrations:migrate --env test --no-interaction --allow-no-migration

db-fixture: ## Run doctrine fixtures with append
	docker-compose exec php bin/console doctrine:fixture:load --append

test: ## Run test
	docker-compose exec php bin/phpunit --filter "$(filter-out $@,$(MAKECMDGOALS))"

test-coverage: ## Run test with coverage
	docker-compose exec php bin/phpunit --coverage-html var/coverage

static-analyse: ## Analyse project files
	$(MAKE) phpmd
	$(MAKE) phpstan

phpmd: ## PHP Mess Detector
	docker-compose exec php composer phpmd

phpstan: ## PHP Static analyse
	docker-compose exec php composer phpstan

phpcs: ## PHP Code Sniffer fix
	docker-compose exec php composer phpcs

composer-install: ## Composer install
	docker-compose exec php composer install
