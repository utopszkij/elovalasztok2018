cd test;
sudo phpunit --whitelist . \
--coverage-clover ../component/site/clover.xml \
--log-junit ../component/site/junit-logfile.xml \
.

cd /var/www/html/elovalasztok2/github/component/site
sudo /usr/local/sbin/sonar/bin/sonar-scanner \
  -Dsonar.projectKey=utopszkij_elovalasztok2018 \
  -Dsonar.organization=utopszkij-github \
  -Dsonar.sources=/var/www/html/elovalasztok2/github/component/site \
  -Dsonar.host.url=https://sonarcloud.io \
  -Dsonar.php.tests.reportPath=/var/www/html/elovalasztok2/github/component/site/junit-logfile.xml \
  -Dsonar.php.coverage.reportPaths=/var/www/html/elovalasztok2/github/component/site/clover.xml \
  -Dsonar.login=47b18c5eb028e4cd9651f98af0eed7f6f070d419
  