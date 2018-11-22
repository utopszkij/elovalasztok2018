#!/bin/sh
# rin php unittest and run sonar-scanner
cd test
phpunit --whitelist . \
--coverage-clover ../unittest.xml \
--log-junit ../unittestlog.xml \
.
retVal=$?
if [ $retVal -eq 0 ]; then
  cd ..
  sonar-scanner \
  -Dsonar.projectKey=utopszkij_elovalasztok2018 \
  -Dsonar.organization=utopszkij-github \
  -Dsonar.sources=component/site \
  -Dsonar.host.url=https://sonarcloud.io \
  -Dsonar.login=47b18c5eb028e4cd9651f98af0eed7f6f070d419;  
  retval=$?
fi
exit $retval
