#!/bin/bash
base_path=$(cd "$(dirname "$(dirname "$0")")" && pwd)
echo "Building elFinder from submodule.";
echo "Base path: ${base_path}";
cd submodule/sources/elFinder || exit;
echo "Building elFinder from submodule.";
pnpm i;
rm -rf build;
pnpm build;
cp -r build/* "${base_path}/libs/elFinder/";
cd $base_path || exit;
