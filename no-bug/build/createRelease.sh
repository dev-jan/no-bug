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

# Error Checker
hasErrors=0

# Banner
echo $blue"  +-----------------------------------------------------------+"
echo      "  |                   no-bug release script                   |"
echo      "  +-----------------------------------------------------------+\n"$normal

# Usage Help if parameters are not correct
if [ "$#" != "1" ]
 then
   echo "usage:$bold\t createRelease.sh [new_version_name]"$normal
   echo "       \t e.g.:  ./createRelease.sh \"1.0\"\n"
   echo "required Application: $bold zip\n"$normal
   return 1
fi

# Read Parameters
version=$1
currentDate=`date +"%Y-%m-%d"`
echo $green"New version name:      $version"
echo $green"Date of newest Build:  $currentDate"$red

# Create temp folder for build
tmpFolder="tmp_$version"
mkdir $tmpFolder
cd $tmpFolder|| hasErrors=1
if [ $hasErrors = 1 ]
 then
   echo $red'Cannot access the folder' $tmpFolder'! Script aborted'$normal
   return 1
fi

# Copy all files in src to temp folder
echo $green"Copy src into temp directory..."$red
cp -r ../../src/* . || hasErrors=1

# Reset nobug-config.php
echo $green"Reset nobug-config.php"$red
echo "<?php " > nobug-config.php || hasErrors=1

# Set the newest version into core/version.php
echo $green"Set Variables of version.php..."$red
echo "<?php\n\$versionname=\"$version\";\n\$compileDate=\"$currentDate\";" > core/version.php || hasErrors=1

# Zip the Build Files together
echo $green"Zip files..."$red
if [ $hasErrors = 1 ]
 then
   echo $red'Error while creating build! Script aborted (make sure the build folder is writable)'$normal
   return 1
fi
zip -r "../nobug_$version.zip" * > /dev/null || hasErrors=1

# remove temp folder
echo $green"Remove temp directory..."$red
rm -R ../$tmpFolder || hasErrors=1

# End message
if [ $hasErrors = 0 ]
 then
   cd ..
   outputfile=`pwd`"/nobug_$version.zip"
   echo $green"\n******** Successfull build the newest no-bug Release ********"
   echo "  Output Zip: $outputfile"$normal
   return 0
 else
   echo $red"\n******** Build was not successfull ********"$normal
   return 1
fi
