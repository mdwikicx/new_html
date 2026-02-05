#!/bin/bash

CLEAN_INSTALL=1
echo "Cd to home directory..."
cd "$HOME" || { echo "Failed to change directory to home directory"; exit 1; }

REPO_URL="https://github.com/mdwikicx/new_html.git"
TARGET_DIR="public_html/new_html_1"
CLONE_DIR="new_html_temp"

# Remove any existing clone directory
rm -rf "$CLONE_DIR"

BRANCH="${1:-main}"
echo ">>> clone --branch ${BRANCH} ."

echo "Cloning repository from GitHub..."

if git clone --branch "$BRANCH" "$REPO_URL" "$CLONE_DIR"; then
    echo "Repository cloned successfully."
else
    echo "Failed to clone the repository." >&2
    exit 1
fi

# remove vendor directory if exists
if [ -d "$CLONE_DIR/src/vendor" ]; then
    echo "Removing vendor directory..."
    rm -rf "$CLONE_DIR/src/vendor"
fi

# remove composer.lock file if exists
if [ -f "$CLONE_DIR/src/composer.lock" ]; then
    echo "Removing composer.lock file..."
    rm -f "$CLONE_DIR/src/composer.lock"
fi

# if CLEAN_INSTALL mv the existing target directory to a backup location
if [ "$CLEAN_INSTALL" -eq 1 ]; then
    if [ -d "$TARGET_DIR" ]; then
        BACKUP_DIR="${TARGET_DIR}_backup_$(date +%Y%m%d%H%M%S)"
        echo "Backing up existing target directory to $BACKUP_DIR"
        mv "$TARGET_DIR" "$BACKUP_DIR"
    fi
    # Move the cloned directory to the target location
    mv "$CLONE_DIR/src" "$TARGET_DIR" -v

    # copy composer_public_html.json to $HOME/public_html
    cp "$CLONE_DIR/composer_public_html.json" "$HOME/public_html/composer.json" -v

    # install composer dependencies
    echo "Installing composer dependencies..."
    cd "$HOME/public_html" || { echo "Failed to change directory to $TARGET_DIR"; exit 1; }
    if command -v composer >/dev/null 2>&1; then
        composer install --no-progress --prefer-dist --optimize-autoloader
    else
        echo "Composer is not installed. Please install composer to manage dependencies." >&2
        exit 1
    fi
else
    # Copy the required files to the target directory
    cp -rf "$CLONE_DIR"/src/* "$TARGET_DIR" -v
fi

rm -rf "$CLONE_DIR"
