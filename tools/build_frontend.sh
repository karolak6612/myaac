#!/bin/bash
set -euo pipefail

cd frontend || { echo "ERROR: frontend/ directory not found" >&2; exit 1; }
npm install
npm run build
cd ..
mkdir -p _app
cp -r frontend/build/_app/* _app/
cp frontend/build/index.html .
cp frontend/build/robots.txt .
echo "Frontend built and installed successfully."
