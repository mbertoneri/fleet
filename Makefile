
#TESTS
behat:
	vendor/behat/behat/bin/behat --config=./behat.yml

## Quality assurance

#PHP-CS-FIXER
tools/php-cs-fixer/vendor: composer.lock
	mkdir -p tools/php-cs-fixer
	composer --working-dir=tools/php-cs-fixer install

php-cs-fixer: tools/php-cs-fixer/vendor
	php tools/php-cs-fixer/vendor/bin/php-cs-fixer $(arguments)

apply-php-cs:
	$(MAKE) php-cs-fixer arguments="fix --using-cache=no --verbose --diff"

#PHPMD
tools/phpmd/vendor: composer.lock
	mkdir -p tools/phpmd
	composer --working-dir=tools/phpmd install

phpmd: tools/phpmd/vendor
	tools/phpmd/vendor/bin/phpmd $(arguments)

apply-phpmd:
	$(MAKE) phpmd arguments="--cache --cache-file tools/phpmd/.phpmd.result-cache.php src,tests text .phpmd.xml"

#PHPSTAN
tools/phpstan/vendor: composer.lock
	composer --working-dir=tools/phpstan install

phpstan: tools/phpstan/vendor
	php tools/phpstan/vendor/bin/phpstan $(arguments)

apply-phpstan:
	$(MAKE) phpstan arguments="analyse --memory-limit=-1 -c .phpstan.neon"

pre-commit: apply-phpmd apply-php-cs apply-phpstan