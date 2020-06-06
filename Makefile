ci:
	vendor/bin/phpcs
	php -d memory_limit=-1 vendor/bin/phpstan analyse
	vendor/bin/psalm
	vendor/bin/phpunit
