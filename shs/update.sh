#!/bin/bash
set -euo pipefail  # Exit on error, undefined vars, pipe failures

# Configuration
readonly REPO_URL="https://github.com/mdwikicx/new_html.git"
readonly TARGET_DIR="$HOME/public_html/new_html_1"
readonly CLONE_DIR="$HOME/new_html_temp"
readonly CLEAN_INSTALL="${CLEAN_INSTALL:-1}"
readonly BRANCH="${1:-main}"

# Color output
readonly RED='\033[0;31m'
readonly GREEN='\033[0;32m'
readonly YELLOW='\033[1;33m'
readonly NC='\033[0m' # No Color

log_info() { echo -e "${GREEN}[INFO]${NC} $*"; }
log_warn() { echo -e "${YELLOW}[WARN]${NC} $*"; }
log_error() { echo -e "${RED}[ERROR]${NC} $*" >&2; }

cleanup() {
    if [ -d "$CLONE_DIR" ]; then
        log_info "Cleaning up temporary files..."
        rm -rf "$CLONE_DIR"
    fi
}

trap cleanup EXIT ERR

# Main deployment
main() {
    log_info "Starting deployment (branch: $BRANCH, clean install: $CLEAN_INSTALL)"

    # Clone repository
    log_info "Cloning repository..."
    git clone --depth 1 --branch "$BRANCH" "$REPO_URL" "$CLONE_DIR" || {
        log_error "Failed to clone repository"
        exit 1
    }

    # Clean unnecessary files
    log_info "Removing vendor and composer.lock..."
    rm -rf "$CLONE_DIR/src/vendor" "$CLONE_DIR/src/composer.lock"

    # Handle clean install
    if [ "$CLEAN_INSTALL" -eq 1 ]; then
        if [ -d "$TARGET_DIR" ]; then
            local backup_dir="${TARGET_DIR}_backup_$(date +%Y%m%d_%H%M%S)"
            log_warn "Backing up to: $backup_dir"
            mv "$TARGET_DIR" "$backup_dir"
        fi
        mv "$CLONE_DIR/src" "$TARGET_DIR"
    else
        log_info "Updating existing installation..."
        mkdir -p "$TARGET_DIR"
        cp -rf "$CLONE_DIR"/src/* "$TARGET_DIR/"
    fi

    # Install dependencies
    log_info "Installing composer dependencies..."
    cd "$TARGET_DIR"

    if ! command -v composer >/dev/null 2>&1; then
        log_error "Composer not found. Install it first."
        exit 1
    fi

    composer install --no-dev --optimize-autoloader --no-interaction

    log_info "Deployment completed successfully!"
}

main "$@"
