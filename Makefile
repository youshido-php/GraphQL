all:
	@cat Makefile | grep : | grep -v PHONY | grep -v @ | sed 's/:/ /' | awk '{print $$1}' | sort

#-------------------------------------------------------------------------------


.PHONY: install-composer
install-composer:
	- SUDO="" && [[ $$UID -ne 0 ]] && SUDO="sudo"; \
	curl -sSL https://raw.githubusercontent.com/composer/getcomposer.org/master/web/installer \
	    | $$SUDO $$(which php) -- --install-dir=/usr/local/bin --filename=composer

.PHONY: dump
dump:
	composer dump-autoload -oa

.PHONY: test
test:
	php bin/phpunit

#-------------------------------------------------------------------------------

.PHONY: lint
lint:
	@ mkdir -p reports

	@ echo " "
	@ echo "=====> Running PHP CS Fixer..."
	- bin/php-cs-fixer fix -vvv

	@ echo " "
	@ echo "=====> Running PHP Code Sniffer..."
	- bin/phpcs --report-xml=reports/phpcs-src.xml -p --colors --encoding=utf-8 $$(find src/ -type f -name "*.php" | sort | uniq)
	- bin/phpcs --report-xml=reports/phpcs-tests.xml -p --colors --encoding=utf-8 $$(find tests/ -type f -name "*.php" | sort | uniq)
	- bin/phpcbf --encoding=utf-8 --tab-width=4 src/ 1>/dev/null
	- bin/phpcbf --encoding=utf-8 --tab-width=4 tests/ 1>/dev/null
	@ echo " "
	@ echo "---------------------------------------------------------------------------------------"
	@ echo " "
	@ php tools/reporter.php

.PHONY: analyze
analyze: lint test
	@ echo " "
	@ echo "=====> Running PHP Copy-Paste Detector..."
	- bin/phpcpd --names=*.php --log-pmd=$$(pwd)/reports/copy-paste.xml --fuzzy src/

	@ echo " "
	@ echo "=====> Running PHP Lines-of-Code..."
	- bin/phploc --progress --names=*.php --log-xml=$$(pwd)/reports/phploc-public.xml public/ > $$(pwd)/reports/phploc-public.txt
	- bin/phploc --progress --names=*.php --log-xml=$$(pwd)/reports/phploc-src.xml src/ > $$(pwd)/reports/phploc-src.txt
	- bin/phploc --progress --names=*.php --log-xml=$$(pwd)/reports/phploc-tests.xml tests/ > $$(pwd)/reports/phploc-tests.txt

	@ echo " "
	@ echo "=====> Running PHP Code Analyzer..."
	- php bin/phpca public/ --no-progress | tee reports/phpca-public.txt
	- php bin/phpca src/ --no-progress | tee reports/phpca-src.txt
	- php bin/phpca tests/ --no-progress | tee reports/phpca-tests.txt

	@ echo " "
	@ echo "=====> Running PHP Metrics Generator..."
	@ # phpmetrics/phpmetrics
	- bin/phpmetrics --config $$(pwd)/phpmetrics.yml --template-title="Avalon Authoring API" --level=10 src/

	@ echo " "
	@ echo "=====> Running PDepend..."
	- bin/pdepend \
	    --configuration=$$(pwd)/pdepend.xml.dist \
	    --dependency-xml=$$(pwd)/reports/pdepend.full.xml \
	    --summary-xml=$$(pwd)/reports/pdepend.summary.xml \
	    --jdepend-chart=$$(pwd)/reports/jdepend.chart \
	    --jdepend-xml=$$(pwd)/reports/jdepend.xml \
	    --overview-pyramid=$$(pwd)/reports/overview \
	    src/ | tee reports/pdepend-src.txt \
	;

	@ echo " "
	@ echo "=====> Running Open-Source License Check..."
	- composer licenses -d www | grep -v BSD-.-Clause | grep -v MIT | grep -v Apache-2.0 | tee reports/licenses.txt

	@ echo " "
	@ echo "=====> Comparing Composer dependencies against the PHP Security Advisories Database..."
	- curl -sSL -H "Accept: text/plain" https://security.sensiolabs.org/check_lock -F lock=@composer.lock | tee reports/sensiolabs.txt
