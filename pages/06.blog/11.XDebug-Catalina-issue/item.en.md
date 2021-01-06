---
title: Missing /usr/include on MacOS 10.15 Catalina
published: true
show_breadcrumbs: true
date: '12-10-2019'
taxonomy:
    category:
        - Blog
    tag:
        - XDebug
        - MacOS Catalina
---

[In the last post](/blog/serenata-phpstan), I was finally back up and running with my IDE after an update to Serenata. Or maybe not...

===

Turns out, another update broke something. MacOS Catalina was released recently and introduced a new version of PHP (7.3.8 in my case). This change means XDebug is now referencing outdated Zend API, which throws an error when running pretty much anything. Oops, beause no XDebug egals no PHPUnit code coverage report.

On MacOS Mojave, getting XDebug was a matter of installing Pear, setting up some Xcode SDK Headers file, installing XDebug from source using Pear and enabling XDebug in PHP configuration files. Sounds easy, should be the same for Catalina...

Spoiler alert, Xcode 11 comes with a massive breaking change.

[notice=note]An alternative installation method is to use **homebrew** to install PHP, Pecl and XDebug. I didn't test this method, as I prefer the long method which was simple to setup on Mojave. Plus, solving this issue might prove useful for other situations.[/notice]


## Installing XDebug

Assuming Pear, Autoconf, and Xcode is installed on your computer, installing **XDebug** should be easy as :

```
sudo pecl install xdebug
```

But now, I'm getting the following error. This is because the compiler requires some header files, which are provided by the MacOS SDK bundled with Xcode. Not a surprise, as it was the same error with previous version of MacOS, aka Mojave :

```
/private/tmp/pear/install/xdebug/xdebug.c:25:10: fatal error: 'php.h' file not found
#include "php.h"
         ^~~~~~~
1 error generated.
make: *** [xdebug.lo] Error 1
ERROR: `make' failed
```

## The Mojave Solution

On Mojave, the following step was necessary to install the missing header files. **Don't actually run this command on Catalina, as it will fail**.

```
sudo installer -pkg /Library/Developer/CommandLineTools/Packages/macOS_SDK_headers_for_macOS_10.14.pkg -target /
```

The problem is [the SDK headers package was removed](https://apple.stackexchange.com/q/372032) starting with Xcode 11.0. And we can't just change `10.14` to `10.15` to make it work...

## The Catalina Issue

After a lot of digging, I've found the actual files are actually stored somewhere :

```
$ sudo find /Library -name php.h
/Library/Developer/CommandLineTools/SDKs/MacOSX10.14.sdk/usr/include/php/main/php.h
/Library/Developer/CommandLineTools/SDKs/MacOSX10.15.sdk/usr/include/php/main/php.h
```

And if you look closely,

```
$ sudo pecl install xdebug
downloading xdebug-2.7.2.tgz ...
Starting to download xdebug-2.7.2.tgz (230,987 bytes)
.................................................done: 230,987 bytes
69 source files, building
running: phpize
grep: /usr/include/php/main/php.h: No such file or directory
grep: /usr/include/php/Zend/zend_modules.h: No such file or directory
grep: /usr/include/php/Zend/zend_extensions.h: No such file or directory
```

See the error returned by `phpize` claiming `/usr/include/php/main/php.h` doesn't exist? Turns out, `/usr/include` **doesn't actually exist on my system**:

```
$ ls /usr/include
ls: /usr/include: No such file or directory
```

If you try to symlink one into the other, even using sudo, that won't work, thanks to SIP :

```
$ sudo ln -s /Library/Developer/CommandLineTools/SDKs/MacOSX10.15.sdk/usr/include /usr/include
ln: /usr/include: Operation not permitted
```

The reason is Apple has deprecated having a `/usr/include` distinct from the SDK. This [has been completely removed in Catalina](https://apple.stackexchange.com/q/372032) so different SDK and Xcode version could be run together.

So now the issue is, in order to compile XDebug, we need to either tell the compiler to use the headers from a different location, or actually put the required files in `/usr/include`...

Until this issue is resolved, either on Apple side or XDebug side, not much can be done other than use a VM to run tests locally...

[notice=tip][size=18]**Update**: [A solution has been found !](/blog/xdebug-catalina)[/size][/notice]

### References for later
- [Can't compile a C program on a Mac after upgrading to Catalina 10.15](https://stackoverflow.com/questions/58278260/cant-compile-a-c-program-on-a-mac-after-upgrading-to-catalina-10-15)
- [Where are the C headers in MacOS Mojave?](https://stackoverflow.com/a/53171665/445757)
- [Can't compile C program on a Mac after upgrade to Mojave](https://stackoverflow.com/questions/52509602/cant-compile-c-program-on-a-mac-after-upgrade-to-mojave)
- [/usr/include missing on macOS Catalina (with Xcode 11)](https://apple.stackexchange.com/questions/372032/usr-include-missing-on-macos-catalina-with-xcode-11#_=_)
- [Installation of Xdebug on MacOS Catalina 10.15](https://stackoverflow.com/questions/58317736/installation-of-xdebug-on-macos-catalina-10-15)
