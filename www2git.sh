#!/bin/bash
# local web server document_root path
www=/var/www/html/elovalasztok2
cp -R -v $www/administrator/components/com_elovalasztok/* $www/github/component/admin/
cp -R -v $www/components/com_elovalasztok/* $www/github/component/site/
cp -R -v $www/administrator/components/com_elovalasztok/*.xml $www/github/component/



