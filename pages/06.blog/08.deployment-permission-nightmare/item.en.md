---
title: 'The deployment nightmare continues'
published: true
show_breadcrumbs: true
date: '17-03-2019'
taxonomy:
    category:
        - Blog
    tag:
        - Deployment
        - Webhook
        - Deployer
---

After [yesterday post about my frustrating progress](/blog/deployment-progress) on git auto-deployment and relative success, I've encountered new frustrations...

===

First, I got stuck on the `Error occurred while executing the hook's command. Please check your logs for more details.` issue again when accessing the hook URL. Not very helpful as the logs didn't showed anything. I did however managed to find a way to see what was wrong by adding the `-verbose` argument to Webhook in the service.

The issue was related to the `deploy:lock` Deployer task. Somehow the execution failed and Deployer didn't unlock the process. So I commented the lock task. At this point I don’t care anymore.

I also added `deploy:whoami` task for sanity reasons. Should have done it on first hand.

Once the lock issue sorted out, I got stuck on the white page of death again. This time all files belong to `www-data`, so WTF. Clearing Grav's cache as `www-data` didn’t worked. It would really help to see Grav output here...

### Fresh start the next day...

Next day, I decide to try again. First open the site that wasn't working the day before... and it works. Say what?

Try a new deployment and everything is still working. But at this point, Webhook was still using the verbose output. However turns out we can't use the verbose output with GitHub since it blocks the request as long as the deployment is not done and GitHub enforce a 10 seconds timeout, which is more than our deployment takes.

Changed the output in `hooks.json`, restart service, try again... and white page of death. Arg...

But then something strange happened. The logs says the Deployer script finishes, but the site still displays a white page... until 5 mins later when everything appears out of nowhere, after a brief Grav error page.

This means the Deployer script does return, but the actual code is not done executing in the background just yet. Note that this only applies when the hook `include-command-output-in-response` property is set to false. When set to true (display all Deployer output), I don't have this issue obviously because Webhook wait for the execution to be finished before returning any output.

Somehow, while writing this article, I did try again the deployment and the whole 5 mins delay got away. Was it because of a latency in the Webhook service? Was it because some Grav commands, either the main Grav update or install command was taking too long? Hard to tell... But one thing for sure, I can't keep this unreliable deployment time on a production environment.

### Zero Downtime Deployment

At this point, there's two solutions I can think of: Trying to find what causes the delay or change the Deployer code so Grav is deployed inside `current/` directory. By doing so, it would achieve true zero downtime deployment.

It really is a problem if a deployment goes wrong and there's no way of rolling back the release... Not to mention the five minutes delay when the script is apparently done, but still working in the background and you can't see what's going on. And this will only gets worst when I move on to UserFrosting, as building the assets can take a while (even if this was greatly improved in the new 4.2.0 version).

Anyway, while it kind of work right now, I don't think it's ready for production. So I guess it's back to the drawing board on the Deployer script to come around with a better plan on the [two repository issue](/blog/deployment-progress#deployer) to achieve true zero downtime.

At least in the mean time I got Webhook SSL support working !
