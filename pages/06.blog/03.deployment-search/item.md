---
title: 'The search for a deployment service'
published: false
date: '04-03-2019'
taxonomy:
    tag:
        - GitHub
        - Grav
        - Deployment
---

In my quest to setup an automatic deployment solution for a website built with Grav, I've already established the Grav [git-sync plugin](/blog/grav-git-sync) wasn't exactly suiting my needs. I've made a lot of research since and I'm surprise to see very few solution out there, not just for Grav, but for post-commit hooks and Webhooks in general.

===

Basically, what I need is something that will update the files on my productions servers every time a new commit is pushed to GitHub. This sounds easy, as GitHub provides [Webhooks](https://developer.github.com/webhooks/). All we need now is a deployment script to have our deployed app on the production server. This script, in Grav's case, would simply have to do a `git pull` and `bin/grav clear-cache` operation.

[center]![](Deployment1.png)[/center]

The reality is you need an endpoint on your server to receive the hook. Moreover, this endpoint needs to be reachable through a public facing URL. This is where it gets difficult, because there is not a lot of documentation or "all in one" package to handle that.


## Potential candidates

The most popular result when searching for a git hook deployment solution involve [a bare git repository and `post-receive` hook](https://gist.github.com/noelboss/3fe13927025b89757f8fb12e9066f2fa) on the production server. However, while this method is the easiest to implement, it's not automatic and can't be directly called by GitHub webhook API. The only way this could be used in an automated way would be to use Travis CI to push to the production server after a successful test.

Other popular results either [are 6 years old](https://github.com/mboynes/github-deploy), [offers only 1 repo for free](https://signup.deploybot.com/signup/new#pricing), [offers only a free 10 days trial](https://www.deployhq.com/pricing), [looks overly complicated](https://www.heroku.com/home) or [are just a pile of spaghetti code](https://github.com/markomarkovic/simple-php-git-deploy/blob/master/deploy.php). [One result was close](https://github.com/scriptburn/git-auto-deploy), but it contained a web accessible UI which I didn't looked too secure at first glance.

Moreover, while I get offering this kind of service requires a lot of expensive resources, no webhook deployment service offers a free pricing tier for open source projects. This means there is a need for **a self hosted one click deployment solution**. Or is it?


## Writing my own service

At this point I considered creating my own deployment app. Question is, what does a good deployment app do? How does it work? And what would it look like if I wrote it? Let's make a list:

- Written in PHP (faster, easier for me)
- Easy setup via Composer, either globally or per project
- No UI. Only frontend is the hook script at a given URL. A CLI app is perfect for added security and easier setup and usage than a PHP script
- Requires SSH Keys, no stored password or token
- Can be integrated to existing app via Composer, using `vendor/bin/post-receive`
- Can manage server permissions, while being secure (can `sudo` a command if required)
- Supports Grav, but other sites too
- Support GitHub webhooks, but modular enough to be able to integrate other providers
- Configuration done through flat file storage. When used inside an app, config can be stored in a `.deployment` file

So there we are. A CLI app, written using Symfony marvelous [Console Component](https://symfony.com/doc/current/components/console.html), and capable of deploying your code, with a front facing URL to handle the receiving hook request.

But wait, something doesn't add up. If this app can be loaded by Composer into an existing project, how can it handle the public facing URL? Even if loaded globally, this still requires additional setup. Maybe using Composer `create-project` command could be of use? Or maybe the wishlist, the very concept of a deployment script and my perception of what is required is wrong...


## It’s a two men job

The more I was thinking about this issue and the more research I did, I realized my concept of "deployment" was wrong. The concept of a _deployment script_ can't be the same thing as a _webhook endpoint_.





[Github documentation](https://developer.github.com/v3/guides/delivering-deployments/#writing-your-server) point to [Sinatra](http://sinatrarb.com/), a ruby application used ot___... Unfortunaltly , Sinatra is more oriented to localhost use, not on a production server.

Even if we end up using a service like Travis, we still need a robust script to handle the deployment procedure.


<!-- Schéma ce que je cherche (avec deployer script + endpoint) -->
[center]![](Deployment2.png)[/center]



### Deployer



### Webhook

https://github.com/adnanh/webhook



## Conclusion

So ~our composer package~ Deployer + Travis / create-project (custom) / Webhook

So here we have my current plan :

[center]![](Deployment3.png)[/center]

In the coming days I'll test out both _Deployer_ and _Webhook_ and I'll be sure to write a definitive guide once everything is setup !


## References
- https://developer.github.com/v3/guides/delivering-deployments/
- https://www.sitepoint.com/deploying-from-github-to-a-server/
- https://gist.github.com/noelboss/3fe13927025b89757f8fb12e9066f2fa
- https://gist.github.com/oodavid/1809044
- https://github.com/mauris/Deployer
- https://github.com/Enrise/TravisDeployer
- https://www.codepicky.com/php-automatic-deploy/
- https://www.silverstripe.org/blog/making-deployment-a-piece-of-cake-with-deployer/
- https://medium.com/@nickdenardis/zero-downtime-local-build-laravel-5-deploys-with-deployer-a152f0a1411f
- https://webthoughts.koderhut.eu/automatic-deployment-with-deployer-b3eb39c88665
