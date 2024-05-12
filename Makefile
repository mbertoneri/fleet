behat:
	vendor/behat/behat/bin/behat --config=./behat.yml

tools:
	mkdir $@

tools/php-cs-fixer/vendor: composer.lock
	mkdir -p tools/php-cs-fixer
	composer --working-dir=tools/php-cs-fixer install

php-cs-fixer: tools/php-cs-fixer/vendor
	php tools/php-cs-fixer/vendor/bin/php-cs-fixer $(arguments)

apply-php-cs:
	$(MAKE) php-cs-fixer arguments="fix --using-cache=no --verbose --diff"

pre-commit: apply-php-cs