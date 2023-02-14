Build process
=============

The Scavix Software Web Development Framework can be packed as PHAR.
Usage is simple, just replace the plain include with one referencing the PHAR:
```php
// old/normal style
require_once("/path/to/scavix-wdf/system.php");

// new/PHAR style
require('phar:///path/to/scavix-wdf.phar/system.php');
```

This is done from within Visual Studio Code by pressing STRG+Shift+B (aka run the build task).

Output will be placeed into the `tools` folder.