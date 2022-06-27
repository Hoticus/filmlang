# Executables (local)
DOCKER_COMP = docker-compose

# Docker containers
PHP_CONT  = $(DOCKER_COMP) exec php
NODE_CONT = $(DOCKER_COMP) exec node

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
YARN     = $(NODE_CONT) yarn

# Misc
.DEFAULT_GOAL = help
.PHONY        = help build up start down logs _php _composer _yarn

## —— 🎵 🐳 The Symfony-docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

_php: ## Get correct php
	@echo $(PHP)

_composer: ## Get correct composer
	@echo $(COMPOSER)

_yarn: ## Get correct yarn
	@echo $(YARN)
