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
        - Xdebug
        - MacOS Catalina
---

After figuring out a way around the massive [breaking change introduced by Xcode 11](/blog/xdebug-catalina-issue), it's now time to install Xdebug back on MacOS Catalina.

===

Long story short, Apple decided to nuke `/usr/include` in MacOS Catalina, which has been the default location for C header file for ever in UNIX systems. Trying to install through PEAR / PECL will return an error as the compiler will look for necessary headers file in `/usr/include`. So the solution is to compile Xdebug manually, manually specifying the actual location of the header files, which are still provided by Xcode, just at a different location.

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

...and run make using our custom SDK location defined as compiler flags :
```
make CPPFLAGS='-I/Applications/Xcode.app/Contents/Developer/Platforms/MacOSX.platform/Developer/SDKs/MacOSX.sdk/usr/include/php -I/Applications/Xcode.app/Contents/Developer/Platforms/MacOSX.platform/Developer/SDKs/MacOSX.sdk/usr/include/php/main -I/Applications/Xcode.app/Contents/Developer/Platforms/MacOSX.platform/Developer/SDKs/MacOSX.sdk/usr/include/php/TSRM -I/Applications/Xcode.app/Contents/Developer/Platforms/MacOSX.platform/Developer/SDKs/MacOSX.sdk/usr/include/php/Zend -I/Applications/Xcode.app/Contents/Developer/Platforms/MacOSX.platform/Developer/SDKs/MacOSX.sdk/usr/include/php/ext -I/Applications/Xcode.app/Contents/Developer/Platforms/MacOSX.platform/Developer/SDKs/MacOSX.sdk/usr/include/php/ext/date/lib'
```

Might see some warning, just ignore it for now.


## Enabled support in PHP

Next, we move the executable somewhere safe. I use `/usr/local/php/extensions`.

```
sudo mkdir -p /usr/local/php/extensions
sudo cp /usr/lib/php/extensions/no-debug-non-zts-20180731/xdebug.so /usr/local/php/extensions
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
