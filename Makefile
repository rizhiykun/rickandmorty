APP=symfony

psalm:
	docker exec $(APP) php ./vendor/bin/psalm --no-cache

run_unit_test:
	docker exec $(APP) php vendor/bin/phpunit --testdox

build:
	@docker exec $(APP) git config --global --add safe.directory /var/www
	@docker exec $(APP) composer install
	@docker exec $(APP) bin/console doctrine:migrations:migrate --no-interaction
