#!/bin/bash

echo "removing old Customizing folder"
[ -d Customizing/global ] && rm -rf Customizing/global

echo "creating Customizing-plugins folder"
mkdir -p Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/
mkdir -p Customizing/global/plugins/Services/COPage/PageComponent/
mkdir -p Customizing/global/plugins/Services/EventHandling/EventHook/
mkdir -p Customizing/global/plugins/Services/AdvancedMetaData/
mkdir -p Customizing/global/plugins/Services/AdvancedMetaData/AdvancedMDClaiming/
mkdir -p Customizing/global/plugins/Services/Cron/CronHook/
mkdir -p Customizing/global/plugins/Services/User/UDFDefinition/
mkdir -p Customizing/global/plugins/Services/EventHandling/EventHook/
mkdir -p Customizing/global/plugins/Services/Repository/RepositoryObject
mkdir -p Customizing/global/plugins/Services/User/UDFClaiming

echo "installing phing"
if [ -f ./phing ]; then
	echo "phing symlink already exists"	
        exit 0
else
        [ -f composer.lock ] && rm -f composer.lock
        composer install --no-interaction
        ln -s vendor/bin/phing ./phing
fi

if [ -f build.xml ]; then
        echo "found build.xml"
else

        echo "creating inital build.xml silently"
        php index.php > /dev/null
fi
