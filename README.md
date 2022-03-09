# phinghelper

The phing code-generator and testsuite.

### Code Generator
index.php reads the file "plugins.csv" and creates a build.xml file from it.
you may either use it as a website/"IDE" or from the shell.

### testsuite
the code-generator tests against a copy of the gitlab-projects. a copy of the database is already shipped here.
a git pull will bring you the latest version...
if the version-string is set, then the min/max version will be checked also. skins have no plugin.php!

### my own index of the gitlab-server
a copy of the database is already shipped here. a git pull will bring you the latest version...
the index is created via "spider.php".
```
$> php spider.php
```
if you want to use the spider, you must edit /lib/_all.php and paste your token there.

### web"IDE"
In the first section of the site, you may type/copy the plugin.csv and check the version-string.
the lower section is just for copy-paste purposes...

### shellbased
if you are not interested in version-checking, then use this
```
$> php index.php
```
if you also want to check the plugin min/max version, then just add the version-string as the parameter
```
$> php index.php 7.1.2
```

### First Install of phinghelper(install phing and mock-Customizing/ dirs)
```
git config --global credential.helper 'cache --timeout=300'
git clone https://github.com/qualitus-hellweg/phinghelper.git
cd phinghelper
sh install.sh
./phing
```
### Integrity check the csv-file(use ONLY for old gitlab-indexes)
- check if "clean clone prod composer dev" ( aka "install dev" ) returns "Build Successful" ... then the columns hold valid git-information(the commands make no sense, but test each column of the csv file )

### Move the build.xml to ilias
- edit build.xml, change name="MyPhingTest" to customer name
- move editted build.xml to ilias-directory

### first install in ilias
```
https://github.com/qualitus-hellweg/_phing.git
cd _phing
sh install.sh
# test, show targets
cd ..
./phing
```
