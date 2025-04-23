#!/bin/bash

# Log the start of the deploy
echo "Starting deploy at $(date)" >> /www/wwwroot/test.mirpurianscafe.com/deploy-log.txt

# Go to the repo directory
cd /www/wwwroot/test.mirpurianscafe.com || { echo "Failed to change directory to /www/wwwroot/test.mirpurianscafe.com" >> /www/wwwroot/test.mirpurianscafe.com/deploy-log.txt; exit 1; }

# Check if the directory is a git repository
if [ ! -d ".git" ]; then
    echo "Not a git repository at $(date)" >> /www/wwwroot/test.mirpurianscafe.com/deploy-log.txt
    exit 1
fi

# Stash local changes if any
git stash --include-untracked >> /www/wwwroot/test.mirpurianscafe.com/deploy-log.txt 2>&1

# Pull the latest changes from the remote repository
git pull origin main >> /www/wwwroot/test.mirpurianscafe.com/deploy-log.txt 2>&1

# Check if the git pull was successful
if [ $? -eq 0 ]; then
    echo "Deploy completed successfully at $(date)" >> /www/wwwroot/test.mirpurianscafe.com/deploy-log.txt
else
    echo "Deploy failed at $(date)" >> /www/wwwroot/test.mirpurianscafe.com/deploy-log.txt
    exit 1
fi
