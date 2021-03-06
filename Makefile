.PHONY: start stop init build tests init-tests

start:
	docker-compose up -d

stop:
	docker-compose stop

init:
	docker-compose build
	docker-compose up -d
	docker-compose exec php composer --no-interaction install
	docker-compose exec php php bin/console doctrine:database:create
	docker-compose exec php php bin/console doctrine:migrations:migrate --no-interaction
	docker-compose exec php php bin/console doctrine:fixtures:load --no-interaction

build:
	build/build.sh

init-tests:
	docker-compose exec php php bin/console --env=test doctrine:database:create
	docker-compose exec php php bin/console --env=test doctrine:migrations:migrate --no-interaction
	docker-compose exec php php bin/console --env=test doctrine:fixtures:load --no-interaction

tests:
	docker-compose exec php php vendor/bin/simple-phpunit
