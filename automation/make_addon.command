#!/bin/bash

# the EE branch
EEBRANCH="develop"

# allow branch override
while getopts "b:" OPTION; do
    case $OPTION in
    b)
        EEBRANCH=$OPTARG
        ;;
    esac
done

# get the base directory of the repo
DIR=$(cd "$(dirname "$0")/.."; pwd)

# get the name of the folder
BASENAME=$(basename $DIR)

# check out EE5 branch
git checkout $EE5RANCH || { echo "Couldn't check out $EEBRANCH branch"; exit 1; }

# get version number from config.php file
EEVERSION=$(php -r "include '$DIR/system/user/addons/cartthrob_order_loader/addon.setup.php'; echo \CARTTHROB_ORDER_LOADER_VERSION;")

# go to the directory above the repo
cd ..

# temporarily rename the repo directory to EE5
mv $BASENAME cartthrob_order_loader_$EEVERSION

ARTIFACTSDIR=cartthrob_order_loader-build/

mkdir -p $ARTIFACTSDIR

# add EE5 version to zip
zip -r $ARTIFACTSDIR/cartthrob_order_loader_$EEVERSION.zip cartthrob_order_loader_$EEVERSION/system -x "*.DS_Store" -x "__MACOSX*" -x "*composer.lock" -x "*composer.json" -x "*.orig" -x "*.git*"

# move the build directory into the project directory
rm -rf cartthrob_order_loader_$EEVERSION/build
mv $ARTIFACTSDIR cartthrob_order_loader_$EEVERSION/build

# rename the repo back to its original name
mv cartthrob_order_loader_$EEVERSION $BASENAME

# change directory back to repo
cd $DIR
