#!/bin/sh

#
#  Author: Benj Fassbind & Jan Bucher
#  Description: Create a zip file for the next release of no-bug
#  

# Colors for colorfull output
green="\033[32m"
red="\033[31m"
blue="\033[34m"
normal="\033[0m"
bold="\033[1m"
lineBeginner="[--- $LOGNAME@`hostname` ---]  "

# Error Checker
hasErrors=0

# Used Tools
minifier='java -jar ../yuicompressor-2.4.8.jar'
lesscompiler='java -jar ../jcruncherEx.jar --less '

# Banner
echo $blue"  +-----------------------------------------------------------+"
echo      "  |                   no-bug release script                   |"
echo      "  +-----------------------------------------------------------+\n"$normal

# Usage Help if parameters are not correct
if [ "$#" != "2" ]
 then
   echo "usage:$bold\t createRelease.sh [new_version_name] [internal_version_number]"$normal
   echo "       \t e.g.:  ./createRelease.sh \"1.0\" \"1\"\n"
   echo "required Application: $bold zip & java (in \$PATH)\n"$normal
   return 1
fi

# Jump into the directory of the script
cd $(dirname $0)

# Read Parameters
version=$1
internalVersion=$2
currentDate=`date +"%Y-%m-%d"`
echo $normal" > New version name:      $version"
echo        " > Internal Version #:    $internalVersion"
echo        " > Date of newest Build:  $currentDate\n"$red

# Create temp folder for build
tmpFolder="tmp_$version"
mkdir $tmpFolder
cd $tmpFolder|| hasErrors=1
if [ $hasErrors = 1 ]
 then
   echo $red$lineBeginner'******** Build was NOT successfull ******** '\
    'Cannot access the folder' $tmpFolder'!'
   echo $green $lineBeginner "Remove temp directory..."$normal
   rm -R ../$tmpFolder 
   exit 1
fi

# Copy all files in src to temp folder
echo $green$lineBeginner"Copy src into temp directory..."$red
cp -r ../../src/* . || hasErrors=1

# Reset nobug-config.php
echo $green$lineBeginner"Reset nobug-config.php..."$red
echo "<?php " > nobug-config.php || hasErrors=1

# Set the newest version into core/version.php
echo $green$lineBeginner"Set Variables of version.php..."$red
echo "<?php\n\$versionname=\"$version\";\n\$internalVersion=$internalVersion;\n\$compileDate=\"$currentDate\";\n\$lessLoader='';" > core/version.php || hasErrors=1

# Compile LESS to CSS and Compress them
echo $green$lineBeginner"Compile & Compress LESS Files..."$red
filesToCompile="style/global.less style/administration.less"
for onefile in $filesToCompile
do
   if [ -f $onefile ]
    then
      cssfilename=`echo $onefile | sed 's/less/css/g'`
      $lesscompiler $onefile $cssfilename > /dev/null || hasErrors=1
      $minifier $cssfilename > tmpfile || hasErrors=1
      cat tmpfile > $cssfilename
      cat core/meta.php | sed 's/'`echo $onefile | cut -d/ -f2`'/'`echo $cssfilename | cut -d/ -f2`'/g' > core/metaTEMP.php
      mv core/metaTEMP.php core/meta.php
      rm $onefile
   else
      echo "  Fatal Error - File Not Found: $onefile" 
      hasErrors=1
   fi
done
cat core/meta.php | sed 's=stylesheet/less=stylesheet=g' > core/metaTEMP.php
mv core/metaTEMP.php core/meta.php
rm js/less.js

# Compress JS with YUIcompressor
echo $green$lineBeginner"Compress JS/CSS Files..."$red
filesToCompress="js/global.js js/jscolor/jscolor.js"
for onefile in $filesToCompress
do
   if [ -f $onefile ] 
    then
      $minifier $onefile > tmpfile || hasErrors=1
      cat tmpfile > $onefile
      rm tmpfile
   else
      echo "  Fatal Error - File Not Found: $onefile" 
      hasErrors=1
   fi
done

# Zip the Build Files together
echo $green$lineBeginner"Zip files..."$red
if [ $hasErrors = 1 ]
 then
   echo $red'******** Build was NOT successfull ******** '
   echo $green"Remove temp directory..."$normal
   rm -R ../$tmpFolder 
   exit 2
fi
if [ ! -d "../../gen" ]
 then
   mkdir "../../gen"
fi

zip -r "../../gen/nobug_$version.zip" * > /dev/null || hasErrors=1

# remove temp folder
echo $green$lineBeginner"Remove temp directory..."$red
rm -R ../$tmpFolder || hasErrors=1

# End message
echo ""
if [ $hasErrors = 0 ]
 then
   cd ../../gen
   outputfile=`pwd`"/nobug_$version.zip"
   echo $green$lineBeginner"******** Successfull build the newest no-bug Release ********"
   echo "  Output Zip: $outputfile\n"$normal
   exit 0
 else
   echo $red $lineBeginner"******** Build was not successfull ********\n"$normal
   exit 3
fi
