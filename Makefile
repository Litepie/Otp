.PHONY: help install test coverage format analyse clean all

# Default target
help: ## Show this help message
	@echo 'Laravel OTP Package - Development Commands'
	@echo ''
	@echo 'Usage:'
	@echo '  make [target]'
	@echo ''
	@echo 'Targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

install: ## Install composer dependencies
	composer install

test: ## Run tests
	vendor/bin/phpunit

coverage: ## Run tests with coverage report
	vendor/bin/phpunit --coverage-html coverage

format: ## Fix code style
	vendor/bin/php-cs-fixer fix --allow-risky=yes

format-check: ## Check code style without fixing
	vendor/bin/php-cs-fixer fix --dry-run --diff

analyse: ## Run static analysis
	vendor/bin/phpstan analyse --memory-limit=2G

clean: ## Clean up generated files
	rm -rf coverage/
	rm -rf build/
	rm -rf .phpunit.cache/

ci: ## Run all CI checks
	make format-check
	make analyse
	make test

all: ## Run format, analyse and test
	make format
	make analyse
	make test

release: ## Prepare for release
	@echo "Preparing release..."
	make clean
	make install
	make all
	@echo "Release ready!"
