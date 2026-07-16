.PHONY: setup up down test lint format migrate seed shell logs

setup:
	cp .env.example .env
	docker compose build
	docker compose run --rm app composer install
	docker compose run --rm app php artisan key:generate
	docker compose run --rm app php artisan migrate --seed

up:
	docker compose up -d

down:
	docker compose down

test:
	docker compose run --rm app php artisan test

lint:
	docker compose run --rm app composer lint

format:
	docker compose run --rm app composer format

migrate:
	docker compose run --rm app php artisan migrate

seed:
	docker compose run --rm app php artisan db:seed

shell:
	docker compose exec app bash

logs:
	docker compose logs -f app nginx worker

