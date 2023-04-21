.DEFAULT_GOAL := help
.PHONY: help

help: ## This help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

init: ## Initialize project
	$(MAKE) start
	$(MAKE) composer-install
	$(MAKE) db-migrate

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
	docker-compose exec -u 1000 $(filter-out $@,$(MAKECMDGOALS)) bash

db-migrate: ## Run doctrine migrations
	docker-compose exec -u 1000 php bin/console doctrine:database:create --no-interaction --if-not-exists
	docker-compose exec -u 1000 php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
	docker-compose exec -u 1000 php bin/console doctrine:database:create --env test --no-interaction --if-not-exists
	docker-compose exec -u 1000 php bin/console doctrine:migrations:migrate --env test --no-interaction --allow-no-migration

test: ## Run test
	docker-compose exec -u 1000 php bin/phpunit --filter "$(filter-out $@,$(MAKECMDGOALS))"

test-coverage: ## Run test with coverage
	docker-compose exec -u 1000 php bin/phpunit --coverage-html var/coverage

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
	docker-compose exec -u 1000 php composer install
