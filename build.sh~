cd test;
phpunit --whitelist . \
--coverage-clover ../component/site/unittest.xml \
--log-junit ../component/site/unittestlog.xml \
.;

# cd ../component/site;
cd /
sudo /usr/local/sbin/sonar/bin/sonar-scanner \
  -Dsonar.projectKey=utopszkij_elovalasztok2018 \
  -Dsonar.organization=utopszkij-github \
  -Dsonar.sources=/var/www/html/elovalasztok2/github/component/site \
  -Dsonar.host.url=https://sonarcloud.io \
  -Dsonar.login=47b18c5eb028e4cd9651f98af0eed7f6f070d419;  