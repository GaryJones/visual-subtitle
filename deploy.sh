#! /bin/bash
# A modification of Dean Clatworthy's deploy script as found here: https://github.com/deanc/wordpress-plugin-git-svn
# The difference is that this script lives in the plugin's git repo & doesn't require an existing SVN repo.
# Modifications by GaryJ to be work better with git-flow usage.

# main config
PLUGINSLUG="visual-subtitle"
CURRENTDIR=`pwd`
MAINFILE="visual-subtitle.php" # this should be the name of your main php file in the wordpress plugin

# git config
GITPATH="$CURRENTDIR/" # this file should be in the base of your git repository

# svn config
SVNPATH="/tmp/$PLUGINSLUG" # path to a temp SVN repo. No trailing slash required and don't add trunk.
SVNURL="http://plugins.svn.wordpress.org/$PLUGINSLUG" # Remote SVN repo on wordpress.org, with no trailing slash
SVNUSER="GaryJ" # your svn username


# Let's begin...
echo ".........................................."
echo 
echo "Preparing to deploy WordPress plugin"
echo 
echo ".........................................."
echo 

# Check version in readme.txt is the same as plugin file after translating both to unix line breaks to work around grep's failure to identify mac line breaks
NEWVERSION1=`grep "^Stable tag:" $GITPATH/readme.txt | awk -F' ' '{print $NF}'`
echo "readme.txt version: $NEWVERSION1"
NEWVERSION2=`grep "Version:" $GITPATH/$MAINFILE | awk -F' ' '{print $NF}'`
echo "$MAINFILE version: $NEWVERSION2"

if [ "$NEWVERSION1" != "$NEWVERSION2" ]; then echo "Version in readme.txt & $MAINFILE don't match. Exiting...."; exit 1; fi

echo "Versions match in readme.txt and $MAINFILE. Let's proceed..."

# GaryJ: Ignore check for git tag, as git flow release finish creates this.
#if git show-ref --tags --quiet --verify -- "refs/tags/$NEWVERSION1"
#	then 
#		echo "Version $NEWVERSION1 already exists as git tag. Exiting...."; 
#		exit 1; 
#	else
#		echo "Git version does not exist. Let's proceed..."
#fi

cd $GITPATH
# GaryJ: Commit message variable not needed . Hard coded for SVN trunk commit for consistency.
#echo -e "Enter a commit message for this new version: \c"
#read COMMITMSG
# GaryJ: git flow release finish already covers this commit.
#git commit -am "$COMMITMSG"

# GaryJ: git flow release finish already covers this tag creation.
#echo "Tagging new version in git"
#git tag -a "$NEWVERSION1" -m "Tagging version $NEWVERSION1"

echo "Pushing latest commit to origin, with tags"
git push origin master
git push origin master --tags

echo 
echo "Creating local copy of SVN repo ..."
svn co $SVNURL $SVNPATH

echo "Ignoring github specific files and deployment script"
svn propset svn:ignore "deploy.sh
README.md
Thumbs.db
.git
.gitignore" "$SVNPATH/trunk/"

echo "Exporting the HEAD of master from git to the trunk of SVN"
git checkout-index -a -f --prefix=$SVNPATH/trunk/

#if submodule exist, recursively check out their indexes
if [ -f ".gitmodules" ]
then
echo "Exporting the HEAD of each submodule from git to the trunk of SVN"
git submodule init
git submodule update
git submodule foreach --recursive 'git checkout-index -a -f --prefix=$SVNPATH/trunk/$path/'
fi

# GaryJ: Support for the /assets folder on the repo.
echo "Moving assets-wp-repo"
mkdir $SVNPATH/assets/
mv $SVNPATH/trunk/assets-wp-repo/* $SVNPATH/assets/
svn add $SVNPATH/assets/
svn delete $SVNPATH/trunk/assets-wp-repo

echo "Changing directory to SVN and committing to trunk"
cd $SVNPATH/trunk/
# Add all new files that are not set to be ignored
svn status | grep -v "^.[ \t]*\..*" | grep "^?" | awk '{print $2}' | xargs svn add
svn commit --username=$SVNUSER -m "Preparing for $NEWVERSION1 release"

echo "Updating WP plugin repo assets & committing"
cd $SVNPATH/assets/
svn commit --username=$SVNUSER -m "Updating wp-repo-assets"

echo "Creating new SVN tag & committing it"
cd $SVNPATH
svn copy trunk/ tags/$NEWVERSION1/
cd $SVNPATH/tags/$NEWVERSION1
svn commit --username=$SVNUSER -m "Tagging version $NEWVERSION1"

echo "Removing temporary directory $SVNPATH"
rm -fr $SVNPATH/

echo "*** FIN ***"