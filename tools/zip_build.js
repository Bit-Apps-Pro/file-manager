var path = require('path');
var make_zip = require('zip-local');

make_zip.sync.zip('build/file-manager').compress().save( 'build/file-manager.zip');