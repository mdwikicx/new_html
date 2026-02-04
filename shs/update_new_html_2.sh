#!/bin/bash

echo "Cd to home directory..."
cd "$HOME" || { echo "Failed to change directory to home directory"; exit 1; }

REPO_URL="https://github.com/mdwikicx/new_html.git"
TARGET_DIR="public_html/new_html"
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

# Copy the required files to the target directory
cp -rf "$CLONE_DIR"/src/* "$TARGET_DIR" -v

rm -rf "$CLONE_DIR"
