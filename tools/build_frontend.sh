#!/bin/bash
set -euo pipefail

cd frontend || { echo "ERROR: frontend/ directory not found" >&2; exit 1; }
npm install
npm run build
cd ..

mkdir -p _app
# Use rsync if available for safer copy, or standard cp with checks
if [ -d "frontend/build/_app" ]; then
    rsync -a --delete frontend/build/_app/ _app/
else
    echo "WARNING: frontend/build/_app not found, skipping copy"
fi

if [ -f "frontend/build/index.html" ]; then
    cp frontend/build/index.html .
else
    echo "WARNING: frontend/build/index.html not found"
fi

if [ -f "frontend/build/robots.txt" ]; then
    cp frontend/build/robots.txt .
fi

echo "Frontend built and installed successfully."
