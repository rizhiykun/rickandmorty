DOCKER_COMPOSE_FILE ?= .docker/test/docker-compose.yml
DOCKER_COMPOSE_FILE_DEV ?= .docker/dev/docker-compose.yml
LOCAL_STACK_NAME ?= manager
TEST_STACK_NAME ?= manager_test
APP=manager_php

.PHONY: psalm test_coverage test up down composer-install composer-update doctrine-schema doctrine-migrate

psalm:
	docker exec $(APP) php ./vendor/bin/psalm --no-cache

run_unit_test:
	docker exec $(APP) php vendor/bin/codecept run unit

test_coverage:
	docker-compose -f ${DOCKER_COMPOSE_FILE} -p ${TEST_STACK_NAME} down
	docker-compose -f ${DOCKER_COMPOSE_FILE} -p ${TEST_STACK_NAME} run php bin/console doctrine:schema:update -f --env=test
	docker-compose -f ${DOCKER_COMPOSE_FILE} -p ${TEST_STACK_NAME} run php bin/console doctrine:fixtures:load --append --env=test
	docker-compose -f ${DOCKER_COMPOSE_FILE} -p ${TEST_STACK_NAME} run codecept run --coverage --coverage-html
	docker-compose -f ${DOCKER_COMPOSE_FILE} -p ${TEST_STACK_NAME} down

test:
	docker-compose -f ${DOCKER_COMPOSE_FILE} -p ${TEST_STACK_NAME} down
	docker-compose -f ${DOCKER_COMPOSE_FILE} -p ${TEST_STACK_NAME} run php bin/console doctrine:schema:update -f --env=test
	docker-compose -f ${DOCKER_COMPOSE_FILE} -p ${TEST_STACK_NAME} run php bin/console doctrine:fixtures:load --append --env=test
	docker-compose -f ${DOCKER_COMPOSE_FILE} -p ${TEST_STACK_NAME} run codecept run
	docker-compose -f ${DOCKER_COMPOSE_FILE} -p ${TEST_STACK_NAME} down

up:
	docker-compose -f ${DOCKER_COMPOSE_FILE_DEV} -p ${LOCAL_STACK_NAME} up --build -d

down:
	docker-compose -f ${DOCKER_COMPOSE_FILE_DEV} -p ${LOCAL_STACK_NAME} down --remove-orphans

exec:
	docker exec -ti $(APP) bash

composer-install:
	docker exec --user www-data $(APP) composer install

composer-update:
	docker exec --user www-data $(APP) composer update

doctrine-schema:
	docker exec $(APP) bin/console doctrine:schema:update --force

doctrine-migrate:
	docker exec $(APP) bin/console --no-interaction doctrine:migrations:migrate

doctrine-fixture:
	docker exec $(APP) bin/console --no-interaction doctrine:fixtures:load
