install:
	@echo "--> Checking for Composer"
	command -v composer >/dev/null && continue || { echo "Composer not found."; exit 1; }

	@echo "--> Installing dependencies"
	composer install -o

	@echo "--> Creating database"
	./bin/parkstreet db:recreate

	@echo "--> Creating schema"
	./bin/doctrine orm:schema-tool:create

	@echo "--> Running import from local source"
	./bin/parkstreet import

	@echo "--> Success"

clean-install:
	@echo "--> Checking for Composer"
	command -v composer >/dev/null && continue || { echo "Composer not found."; exit 1; }

	@echo "--> Clearing vendor directory"
	rm -Rf vendor

	@echo "--> Clearing bin directory"
	find ./bin ! -name 'bin/parkstreet' -type f -exec rm -f {} +

	@echo "--> Installing dependencies"
	composer install -o

	@echo "--> Success"

reload:
	./bin/parkstreet db:recreate
	./bin/doctrine orm:schema-tool:create
	./bin/parkstreet import

test:
	./bin/phpspec run -fpretty
