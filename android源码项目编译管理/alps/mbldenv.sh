#!/bin/bash
# ##########################################################
# ALPS(Android4.1 based) build environment profile setting
# ##########################################################
# Overwrite JAVA_HOME environment variable setting if already exists
echo "******************"
echo "*********jdk 1.7*********"
echo "******************"

JAVA_HOME=/usr/lib/jvm/java-1.7.0-openjdk-amd64
export JAVA_HOME

# Overwrite ANDROID_JAVA_HOME environment variable setting if already exists
ANDROID_JAVA_HOME=/usr/lib/jvm/java-1.7.0-openjdk-amd64
export ANDROID_JAVA_HOME

# Overwrite PATH environment setting for JDK & arm-eabi if already exists
PATH=/usr/lib/jvm/java-1.7.0-openjdk-amd64/bin:$PWD/prebuilts/gcc/linux-x86/arm/arm-linux-androideabi-4.8/bin:$PWD/prebuilts/gcc/linux-x86/arm/arm-eabi-4.8/bin:$PATH
export PATH

# Add MediaTek developed Python libraries path into PYTHONPATH
if [ -z "$PYTHONPATH" ]; then
  PYTHONPATH=$PWD/device/mediatek/build/build/tools
else
  PYTHONPATH=$PWD/device/mediatek/build/build/tools:$PYTHONPATH
fi
export PYTHONPATH

#add by luwl for .git can't upload 20150625
if [ -d "external/chromium_org/third_party/angle/git" ]; then
  echo "change this git dir to .git dir"
  mv external/chromium_org/third_party/angle/git external/chromium_org/third_party/angle/.git
fi

