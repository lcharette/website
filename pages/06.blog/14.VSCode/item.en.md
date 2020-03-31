---
title: Moving to VSCode
published: true
show_breadcrumbs: true
date: '10-03-2020'
taxonomy:
    category:
        - Blog
    tag:
        - Atom
        - VSCode
        - PHP IDE
---

Up until recently, I've been using Atom as my de facto editor. I started using Atom about five years ago when I became aware of Composer, PSR and such and needed a real PHP editor to replace Coda 2. But a critical package update changed things up, and I actually moved to VSCode. Every developer has his favourite editor, and switching environment is usually a big step. So what made me change?

===

[center]![](01.Header.png)[/center]

The first reason is plugin, or packages, availability in Atom. Since I mostly work with PHP, [Serenata](https://serenata.gitlab.io) have been my PHP IDE of choice inside Atom for a while now. But with the recent introduction of Serenata 5 last fall, things started to fall apart. The new version came with a much needed performance boost, mostly when came time to install the Serenata server, but it also came with crashes and a critical feature removal. While I understand [why they removed code linting](https://gitlab.com/Serenata/Serenata/-/wikis/Linting#deprecation) (**tldr**; because other does the same job way better, looking at you PHPStan, more on that later), the instability of Serenata post-update is what made me flip the table.

## (╯°□°）╯︵ ┻━┻


You don't really appreciate what your IDE does for you until it doesn't anymore. After the update, I got no more code autocompletion. No more methods and definitions link. All I got was constant server crashes. Even after version 5.1 was released, the server was still unstable in Atom.

I'm in no way a professional coder and I don't code everyday, but if this was enough for me to flip the switch, I can't imagine someone disappointment when using a trusted tool everyday for their regular job.

Serenata issues where the beginning of the end for Atom because it pointed out a fundamental issue : **Atom doesn't have a good PHP IDE package.** I tried many of them, and while I didn't kept a list, none of them were has useful for me as Serenata (V4) was. So I started looking at alternatives.

## VSCode

Naturally, I gave VSCode a try. I’ve been hearing about it for a while, but never look at what it actually was or what it was actually capable of. All I knew is it was a Microsoft product, and MacOS people will understand why I don’t always trust Microsoft product.

My first impression where… mitigated… I installed some extension, gave opened a PHP proejct and… dragged VSCode to the trash. **Seriously.**

Something wasn’t right. Something wasn’t as it used to be. At that point I actually went back to Atom and (unstable) Serenata, but it was only a matters of days before I gave VSCode a second chance (after flipping a table again).

## A true PHP IDE

The second time was the right one. This time, I took the time to actually setup everything properly, research which extensions where _cool_, and made sure everything was working as it should.

The truth is, even whit the default install, VSCode provides more for PHP developer than Atom could ever do with the available packages. This was in fact what was causing issue the first time : You have to disable the built-in PHP IDE when working with some extensions.

Getting used to VSCode was a matters of days, if not houes after this. I also gained in productivity, not only because VSCode is _wayyyy_ faster than Atom, especially at startup, but also because I found some nice and useful extensions I never thought I needed. The number of extensions available for VSCode is really impressive when compared to Atom choice.

For the curious (and in case I have to re-setup everything again in the near future), my currently installed extensions are :

- **[PHP Intelephense](https://marketplace.visualstudio.com/items?itemName=bmewburn.vscode-intelephense-client) - The most important of the list. It what’s make VSCode a must have for PHP developer in my opinion compared to Atom. Seriously, give it a try (and follow the quick start guide).**
- [PHP Static Analysis](https://marketplace.visualstudio.com/items?itemName=breezelin.phpstan) - PHPStan integration - Second most important package on my list.
- [php cs fixer](https://marketplace.visualstudio.com/items?itemName=junstyle.php-cs-fixer) - The necessary PHP CS Fixer integration !
- [PHP Debug](https://marketplace.visualstudio.com/items?itemName=felixfbecker.php-debug) - My new best friend : XDebug integration. More on that in a future post !
- [PHP DocBlocker](https://marketplace.visualstudio.com/items?itemName=neilbrayfield.php-docblocker)
- [PHP Getters & Setters](https://marketplace.visualstudio.com/items?itemName=phproberto.vscode-php-getters-setters)
- [Project Manager](https://marketplace.visualstudio.com/items?itemName=alefragnani.project-manager) - I found this way more useful than saving the workspace. One click solution to open one of my projects.
- [Atom One Dark Theme](https://marketplace.visualstudio.com/items?itemName=akamud.vscode-theme-onedark) - So I can feel like home, you know…
- [Git Web Links for VS Code](https://marketplace.visualstudio.com/items?itemName=reduckted.vscode-gitweblinks) - Very useful when sharing code reference while doing support or working with someone. You no longer have to find the line on GitHub, just copy the GitHub link directly from VSCode !
- [GitHub Notifications](https://marketplace.visualstudio.com/items?itemName=fabiospampinato.vscode-github-notifications-bell)
- [GitHub Pull Requests](https://marketplace.visualstudio.com/items?itemName=GitHub.vscode-pull-request-github) - I’ve not used this one much, but it look promising, seeing how VSCode does lack the PR support Atom does have built-in.
- [Markdown All in One](https://marketplace.visualstudio.com/items?itemName=yzhang.markdown-all-in-one)
- [Markdown Preview Github Styling](https://marketplace.visualstudio.com/items?itemName=bierner.markdown-preview-github-styles) - For those READMEs
- [Markdown Table Formatter](https://marketplace.visualstudio.com/items?itemName=fcrespo82.markdown-table-formatter) - Provide a different style than All in One.
- [Twig](https://marketplace.visualstudio.com/items?itemName=whatwedo.twig) - Twig Syntax support.

In the end, I’m now very satisfied with this new setup, and I wound’t go back to Atom in the near future. We don’t know what will happens of Atom now that Microsoft actually bought GitHub, but I wouldn’t be surprised to see VSCode being marketed as the “official” GitHub editor in the future. VSCode visibly have the best support for it’s community, and I think it’s just getting started if it can make people like me actually abandon the comfort of their editor.

## The good ending

One really good thing came out of the downfall of Serenata. Since they removed code linting in 5.0, it actually made me [switch over to **PHPStan**](/blog/serenata-phpstan) for code linting. Serenata solution was good at pointing all basic stuff, but after using PHPStan for a couple of months now, I understand why the Serenata devs removed that feature. I’m currently **obsessed** (maybe too much) by fixing all the issues reported by PHPStan, and with the highest setting nonetheless.

It made me rethink and optimized some code I thought was perfect and pinpoint unique issue I would have never catch otherwise. So thank you Serenata (and Atom) for making me switch over to PHPStan (and VSCode) !
