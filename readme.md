# My Thoughts
Project is functional.
Not full done because I'm missing PHPUnit a lot... do not blame me for that (behat is clearly not enough).\
For Behat tests, I think I could have written some helpers.\

finally added some unit tests.

# Requirements
To run this project you will need a computer with PHP 8.3 and composer installed.

# Install
To install the project, you just have to run `composer install` to get all the dependencies

# Running the tests
```
make tests
make behat
make tu
```

# Running quality tools:
```
make pre-commit
```

# Fleet Client commands
```
php bin/console fleet:client create --userId fleet-one
php bin/console fleet:client register-vehicle --userId fleet-one --plate-number AX-3K-OK --type motorcycle
php bin/console fleet:client localize-vehicle --plate-number AX-3K-OK --lat 3.45 --lng 18.456
```

# Quality tools
phpmd: find duplicate, maybe some bugs\
php-cs-fixer: format code following a code style\
phpstan: static analysis tool for PHP that helps find errors in code

Rector could be installed too.

# CI/CD

## CI
In order to validate a pull request, a CI workflow could be done:
it will run all tests and quality tools

## CD
A "script" that allow an automation in order to push a release in production or preprod with actions like:
- install vendor
- run migrations