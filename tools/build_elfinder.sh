#!/bin/bash
cd submodule/sources/elFinder || exit;
echo "Building elFinder from submodule.";
npm run plugin-build;
cd ../../.. || exit;
