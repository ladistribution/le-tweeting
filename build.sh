NAME="le-tweeting"
FOLDER="application"
PACKAGE="$NAME.zip"
BUILD="build"

echo "# Building $NAME package"
mkdir $BUILD
mkdir $BUILD/dist
mkdir $BUILD/$FOLDER

echo "# Get source (local)"
cp dist/manifest.xml $BUILD/dist
cp dist/installer.php $BUILD/dist
cp dist/prepend.php $BUILD/dist
cp index.php $BUILD/$FOLDER
cp twitter.php $BUILD/$FOLDER
cp -R css $BUILD/$FOLDER
cp -R js $BUILD/$FOLDER
cp -R views $BUILD/$FOLDER
cp icon.png $BUILD/$FOLDER
cp ld-icon.png $BUILD/$FOLDER

# Remove some unwanted files (mac)
find . -name '*.DS_Store' -type f -delete

echo "# Packing $PACKAGE"
cd $BUILD
zip -r $PACKAGE $FOLDER dist -x \*.svn/\* \*.preserve
mv $PACKAGE ..
cd ..

# Clean
rm -rf $BUILD
