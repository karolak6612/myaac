#!/bin/bash
cd frontend
npm install
npm run build
cd ..
mkdir -p _app
cp -r frontend/build/_app/* _app/
cp frontend/build/index.html .
cp frontend/build/robots.txt .
echo "Frontend built and installed successfully."
