---
title: Installation of Xdebug on MacOS Catalina 10.15
published: true
show_breadcrumbs: true
date: '14-10-2019'
taxonomy:
    category:
        - Blog
    tag:
        - PHP IDE
        - XDebug
        - MacOS Catalina
---

After figuring out a way around the massive [breaking change introduced by Xcode 11](/blog/xdebug-catalina-issue), it's now time to install Xdebug back on MacOS Catalina.

===

[notice=note]
**Update from January 6th 2021:**   
Using MacOS Big Sur? This solution should work for you too. But since Apple Deprecated PHP in MacOS Big Sur, you should probably [rely on Homebrew now](/blog/phpunit-big-sur#the-solution).

**Update from June 30th 2020:**   
After some new investigation while trying to upgrade to a newer version of xdebug, I now believe most of the instructions in this post are not necessary. Before doing anything, you should check if `xdebug.so` already exists in `/usr/lib/php/extensions/no-debug-non-zts-20180731/`, which I believe is there by default (let me know if otherwise). If it does exist, you could skip to the **[Enabled support in PHP](#enabled-support-in-php)** portion of this post.

Note that building xdebug from source code and actually trying to use that version of `xdebug.so` (for example by referencing the built file in `xdebug/module/xdebug.so` after using `make install`) with the build-in PHP should end up in a "code signature" error. As described [here](https://stackoverflow.com/questions/53668236/how-to-compile-and-use-php-extensions-on-mac-os-mojave) and [here](https://superuser.com/a/1536442/1100783), even after signing the binary, MacOS won't allow system binaries to interact with non-system binaries for security reasons. The only real solution to use a custom version of xdebug would be to compile and use you own instance of PHP instead of the build in one.
[/notice]

Long story short, Apple decided to nuke `/usr/include` in MacOS Catalina, which has been the default location for C header file for ever in UNIX systems. Trying to install through PEAR / PECL will return an error as the compiler will look for necessary headers file in `/usr/include`. So the solution is to compile Xdebug manually, manually specifying the actual location of the header files, which are still provided by Xcode, just at a different location.

<!--[notice=note]**June 28th 2020**: Instructions have been update for the latest xdebug version at this time, aka 2.7.2[/notice]-->

## Xcode

The first step is to get Xcode from the [App Store](https://apps.apple.com/ca/app/xcode/id497799835).

Once Xcode installed, we have to get the command line tools :
```
xcode-select --install
```

You might need to actually open Xcode at this point to finish installation and accept terms and conditions, especially if the previous command fails.


Finally, make sure the SDK is found. If the path you get differs from the one bellow, you might need to edit the path accordingly later on:
```
$ xcrun --show-sdk-path

/Applications/Xcode.app/Contents/Developer/Platforms/MacOSX.platform/Developer/SDKs/MacOSX.sdk
```

## Manually Compiling Xdebug

### Getting source
Let's compile **2.7.2**, getting the source code from git. Alternatively, you can download the source from [Xdebug site](https://xdebug.org/download.php#releases).

```
git clone https://github.com/xdebug/xdebug.git
cd xdebug
git checkout tags/2.7.2
```

### phpize
Next we need to make a copy `phpize` so we can edit the include path :

```
cp /usr/bin/phpize .
nano ./phpize
```

Find this line :

```
includedir="`eval echo ${prefix}/include`/php"
```

...and replace it with this line :
```
includedir="/Applications/Xcode.app/Contents/Developer/Platforms/MacOSX.platform/Developer/SDKs/MacOSX.sdk/usr/include/php"
```

Run `phpize`:

```
./phpize
```

You should now see something like this :

```
Configuring for:
PHP Api Version:         20180731
Zend Module Api No:      20180731
Zend Extension Api No:   320180731
```

### Configure & build

We can now configure :

```
./configure --enable-xdebug
```

...and run make using our custom SDK location defined as compiler flags. I used a variable to store the path to the SDK so it's easier to edit if it changes :
```
SDK_PATH=$(xcrun --show-sdk-path)

make CPPFLAGS="-I${SDK_PATH}/usr/include/php -I${SDK_PATH}/usr/include/php/main -I${SDK_PATH}/usr/include/php/TSRM -I${SDK_PATH}/usr/include/php/Zend -I${SDK_PATH}/usr/include/php/ext -I${SDK_PATH}/usr/include/php/ext/date/lib"
```

You might see some warning, just ignore it for now. Finally, we'll need to run :

```
make install
```

Again, this command will fail because it can't move the extension to the right place. SIP will prevent it. But no worries, we'll take care of that manually at the next step. `make install` is still required as it will sign the `*.so` file.

[notice=tip]The above trick should work for [any PHP extension you want to compile](https://superuser.com/questions/1487126/php-7-3-8-zip-extension-on-macos-catalina-10-15). If you're trying to compile something other than a PHP extension, I recommend having a look at the `Makefile` to see which directory to include in your custom `CPPFLAGS`.[/notice]

## Enabled support in PHP

Once `make install` has been run, we can move the executable somewhere safe. I use `/usr/local/php/extensions`.

```
sudo mkdir -p /usr/local/php/extensions
sudo cp $(php-config --extension-dir)/xdebug.so /usr/local/php/extensions
```

Then we edit the PHP configuration to enable Xdebug. Simply edit `php.ini`:

```
sudo nano /etc/php.ini
```

And we add the following at the bottom :
```
[xdebug]
zend_extension=/usr/local/php/extensions/xdebug.so
xdebug.remote_enable=on
xdebug.remote_log="/var/log/xdebug.log"
xdebug.remote_host=localhost
xdebug.remote_handler=dbgp
xdebug.remote_port=9000
```

Restart built in server to be sure :
```
sudo apachectl restart
```

And finally test everything went fine :

```
php -i | grep "xdebug support"
```

If the above command returns nothing, then Xdebug is not available on your install. Go back the steps to find out what's missing.


## References and thanks :
- [Can't compile a C program on a Mac after upgrading to Catalina 10.15](https://stackoverflow.com/questions/58278260/cant-compile-a-c-program-on-a-mac-after-upgrading-to-catalina-10-15)
- [Missing system headers (/usr/include) on macOS Catalina](https://stackoverflow.com/questions/58232595/missing-system-headers-usr-include-on-macos-catalina)
- [Installation of Xdebug on MacOS Catalina 10.15](https://stackoverflow.com/questions/58317736/installation-of-xdebug-on-macos-catalina-10-15)
- [PHP 7.3.8. ZIP extension on MacOS Catalina 10.15](https://superuser.com/questions/1487126/php-7-3-8-zip-extension-on-macos-catalina-10-15)
- [How to compile and use php extensions on Mac OS Mojave](https://stackoverflow.com/questions/53668236/how-to-compile-and-use-php-extensions-on-mac-os-mojave)
- [How to get zip extension working in PHP in MacOS 10.15.1?](https://superuser.com/questions/1499342/how-to-get-zip-extension-working-in-php-in-macos-10-15-1)
- [How to install Xdebug on MacOS 10.15 Catalina (Xcode 11)](https://profilingviewer.com/installing-xdebug-on-catalina.html)
