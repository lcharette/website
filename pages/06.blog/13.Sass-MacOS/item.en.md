---
title: Installation of node-sass globally on MacOS and Atom
published: true
show_breadcrumbs: true
date: '14-10-2019'
taxonomy:
    category:
        - Blog
    tag:
        - npm
        - Atom
        - SCSS
        - MacOS
---

I haven't used _scss_ in a while, and of course, when you don't touch something in a while, it won't work anymore. That was the case with `node-sass`, used to compile `.scss` file. Here's my notes on how to reinstall `node-sass` globally on MacOS, including the necessary Atom package, in case it could be useful to someone else.

===

An important thing to know is `npm` installs packages locally within your projects by default. In this case, we want to install the package globally. However the downside of this is that you need to be root (or use sudo) to be able to install globally. The following method makes it possible to install packages globally for a given user without the need to be root.

##### 1. Install node.js

Simply download the installer from [node.js website](https://nodejs.org/en/) and isntall it.

##### 2. Create a directory to store global packages

```
mkdir "${HOME}/.npm-packages"
```

##### 3. Tell `npm` where to store globally installed packages

```
npm config set prefix "${HOME}/.npm-packages"
```

##### 4. Ensure `npm` will find installed binaries

Add the following to your `.bash_profile`:

```
nano ~/.bash_profile
```

```
NPM_PACKAGES="${HOME}/.npm-packages"

export PATH="$PATH:$NPM_PACKAGES/bin"
```

Reload the bash profile :

```
source ~/.bash_profile
```

##### 5. Install `node-sass`

```
npm install -g node-sass
```

You can me sure the package is correctly installed :

```
$ node-sass -v

node-sass	4.12.0	(Wrapper)	[JavaScript]
libsass  	3.5.4	(Sass Compiler)	[C/C++]
```

##### 6. Install Atom package

I use [sass-autocompile](https://atom.io/packages/sass-autocompile) in Atom. This package will use `node-sass` to compile your `.scss` files on save.

[notice=warning]If you have issue with this, make sure you use **`node-sass`** and **not** `npm-sass`.[/notice]

[notice=tip]Psst... Using VSCode now? Check out [Easy Sass](https://marketplace.visualstudio.com/items?itemName=spook.easysass) ![/notice]

## References :
- [Install npm packages globally without sudo on macOS and Linux](https://github.com/sindresorhus/guides/blob/master/npm-global-without-sudo.md)
