#!/bin/bash
# toolforge-jobs run updateauth --image python3.11 --command "~/shs/update_auth.sh" --wait

export USER_NAME="mdwikicx"
export SUB_DIR_COPY="src"
export CLEAN_INSTALL=1

# Optional clean of jsons files before copy to avoid issues with old jsons files
export REMOVE_SRC_JSONS_BEFORE_COPY=0

# additional file to copy to TARGET_DIR
export COPY_TO_TARGET=""

REPO_NAME="new_html"
TARGET_DIR="$HOME/public_html/new_html_1"
BRANCH="${1:-main}"

# Run deploy
$HOME/shs/deploy_php_repo.sh "$REPO_NAME" "$TARGET_DIR" "$BRANCH"
