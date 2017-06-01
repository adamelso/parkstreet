install:
	@echo "--> Checking for Composer"
	command -v composer >/dev/null && continue || { echo "Composer not found."; exit 1; }

	@echo "--> Installing dependencies"
	composer install -o

	# mysqladmin -u$USER -p$PASSWORD create $DB_NAME
	# bin/doctrine orm:schema-tool:create

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
