#!/bin/bash
cd submodule/sources/elFinder || exit;
echo "Building elFinder from submodule.";
npm i;
npm run plugin-build;
cd ../../.. || exit;
