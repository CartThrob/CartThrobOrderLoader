#!/bin/bash

# the EE5 branch
EE5BRANCH="develop"

# allow branch override
while getopts "e:" OPTION; do
    case $OPTION in
    e)
        EE5BRANCH=$OPTARG
        ;;
    esac
done

# get the base directory of the repo
DIR=$(cd "$(dirname "$0")/.."; pwd)

# get the name of the folder
BASENAME=$(basename $DIR)

# check out EE5 branch
git checkout $EE5BRANCH || { echo "Could'nt check out $EE5BRANCH branch"; exit 1; }

# get version number from config.php file
EE5VERSION=$(php -r "include '$DIR/system/user/addons/cartthrob_order_loader/addon.setup.php'; echo \CARTTHROB_ORDER_LOADER_VERSION;")

# go to the directory above the repo
cd ..

# temporarily rename the repo directory to EE5
mv $BASENAME cartthrob_order_loader_$EE5VERSION

ARTIFACTSDIR=cartthrob_order_loader-build

mkdir -p $ARTIFACTSDIR

# add EE5 version to zip
zip -r $ARTIFACTSDIR/cartthrob_order_loader_$EE5VERSION.zip cartthrob_order_loader_$EE5VERSION/system -x "*.DS_Store" -x "__MACOSX*" -x "*composer.lock" -x "*composer.json" -x "*.orig" -x "*.git*"

# move the build directory into the project directory
rm -rf cartthrob_order_loader_$EE5VERSION/build
mv $ARTIFACTSDIR cartthrob_order_loader_$EE5VERSION/build

# rename the repo back to its original name
mv cartthrob_order_loader_$EE5VERSION $BASENAME

# change directory back to repo
cd $DIR
