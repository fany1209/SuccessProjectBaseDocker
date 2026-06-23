.PHONY: help up down build rebuild logs shell artisan migrate seed fresh \
        tinker test npm queue schedule status restart mysql

# ── Default ──────────────────────────────────────────────────────────────────
help: ## Show this help
	@echo.
	@echo   Success Project - Docker Commands
	@echo   ==================================
	@echo.
	@echo   make setup      - First-time setup (copy env + build + start)
	@echo   make up         - Start all containers
	@echo   make down       - Stop all containers
	@echo   make build      - Build/rebuild images
	@echo   make restart    - Restart all containers
	@echo   make logs       - View all logs (follow)
	@echo   make shell      - Enter PHP container shell
	@echo   make mysql      - Enter MySQL CLI
	@echo   make artisan    - Run artisan command (make artisan cmd="migrate")
	@echo   make migrate    - Run migrations
	@echo   make seed       - Run seeders
	@echo   make fresh      - Fresh migration + seed
	@echo   make tinker     - Open Laravel Tinker
	@echo   make test       - Run tests
	@echo   make npm        - Run npm command (make npm cmd="run dev")
	@echo   make queue      - Start queue worker
	@echo   make schedule   - Run scheduler once
	@echo   make status     - Show container status
	@echo.

# ── Setup ────────────────────────────────────────────────────────────────────
setup: ## First-time setup
	@if not exist .env ( \
		copy .env.docker .env \
		&& echo [OK] .env created from .env.docker \
		&& echo [!!] Edit .env and add your credentials before starting \
	) else ( \
		echo [OK] .env already exists \
	)
	docker compose build
	docker compose up -d
	@echo.
	@echo   Setup complete! Visit http://localhost:8001
	@echo.

# ── Container lifecycle ─────────────────────────────────────────────────────
up: ## Start all containers in background
	docker compose up -d

down: ## Stop and remove all containers
	docker compose down

build: ## Build images without cache
	docker compose build --no-cache

rebuild: ## Rebuild and restart
	docker compose down
	docker compose build --no-cache
	docker compose up -d

restart: ## Restart all containers
	docker compose restart

status: ## Show container status
	docker compose ps

logs: ## View all logs (follow mode)
	docker compose logs -f

logs-app: ## View only app container logs
	docker compose logs -f app

logs-nginx: ## View only nginx logs
	docker compose logs -f nginx

logs-mysql: ## View only mysql logs
	docker compose logs -f mysql

# ── Shell access ─────────────────────────────────────────────────────────────
shell: ## Enter PHP container bash
	docker compose exec app bash

mysql: ## Enter MySQL CLI
	docker compose exec mysql mariadb -u success_user -psuccess_pass success

# ── Laravel commands ─────────────────────────────────────────────────────────
artisan: ## Run artisan command (usage: make artisan cmd="migrate:status")
	docker compose exec app php artisan $(cmd)

migrate: ## Run migrations
	docker compose exec app php artisan migrate

seed: ## Run seeders
	docker compose exec app php artisan db:seed

fresh: ## Fresh migration + seed (DESTROYS DATA)
	docker compose exec app php artisan migrate:fresh --seed

tinker: ## Open Laravel Tinker REPL
	docker compose exec app php artisan tinker

test: ## Run tests
	docker compose exec app php artisan test

# ── Frontend ─────────────────────────────────────────────────────────────────
npm: ## Run npm command (usage: make npm cmd="run build")
	docker compose exec node npm $(cmd)

npm-install: ## Install npm dependencies
	docker compose exec node npm install

npm-build: ## Build assets for production
	docker compose exec node npm run build

# ── Queue & Scheduler ───────────────────────────────────────────────────────
queue: ## Start queue worker manually
	docker compose exec app php artisan queue:work --tries=3

schedule: ## Run scheduler once (for testing)
	docker compose exec app php artisan schedule:run

# ── Maintenance ──────────────────────────────────────────────────────────────
cache-clear: ## Clear all Laravel caches
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan route:clear
	docker compose exec app php artisan view:clear
	docker compose exec app php artisan cache:clear

optimize: ## Optimize Laravel for development
	docker compose exec app php artisan optimize:clear

permissions: ## Fix storage permissions
	docker compose exec app chown -R www-data:www-data storage bootstrap/cache
	docker compose exec app chmod -R 775 storage bootstrap/cache

# ── Database utilities ───────────────────────────────────────────────────────
db-export: ## Export database dump
	docker compose exec mysql mariadb-dump -u success_user -psuccess_pass success > backup_$(shell date +%Y%m%d_%H%M%S).sql
	@echo [OK] Database exported

destroy: ## Remove all containers, volumes, and data (NUCLEAR OPTION)
	docker compose down -v --remove-orphans
	@echo [!!] All data destroyed. Run 'make setup' to start fresh.
