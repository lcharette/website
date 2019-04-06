---
title: "La recherche d'un service de déploiement"
published: true
date: '08-03-2019'
taxonomy:
    category:
        - Blog
    tag:
        - GitHub
        - Grav
        - Deployment
---

Dans ma quête pour trouver une solution de déploiement automatique pour un site Web construit avec Grav, j'ai déjà [établi que le plugin Grav git-sync ne répondait pas exactement à mes besoins](/blog/grav-git-sync). J'ai fait beaucoup de recherches depuis et je suis surpris de voir très peu de solutions sont disponibles, pas seulement pour Grav, mais pour les scripts post-commit et Webhooks en général.

===

Ce dont j'ai besoin, en gros, c'est de mettre à jour les fichiers sur les serveurs de production chaque fois qu'un nouveau commit est envoyé à GitHub:

[center]
[![](01.IntegrationGraph.png)](https://github.com/mauris/Deployer)
[size=12]Source: <https://github.com/mauris/Deployer>[/size]
[/center]

Cela semble facile, car GitHub fournit des [_Webhooks_](https://developer.github.com/webhooks/) pour envoyer un _ping_ au serveur de production. Nous avons maintenant besoin d’un script de déploiement pour que notre application soit automatiquement déployée sur le serveur de production. Ce script de déploiement, dans le cas de Grav, devra simplement effectuer `git pull` et `bin/grav clear-cache`.

[center]![](Deployment1.png)[/center]

En réalité, vous avez besoin d'une app sur votre serveur pour recevoir le hook. De plus, ce noeud doit être accessible via une URL publique. C’est là que ça se complique, car il n’ya pas beaucoup de documentation ou d'app "tout en un" pour gérer cela.


## Candidats potentiels

Le résultat le plus populaire lors d'une recherche Google pour une solution de déploiement est [un dépôt git vide ("git bare repository") et un script `post-receive`](https://gist.github.com/noelboss/3fe13927025b89757f8fb12e9066f2fa) sur le serveur de production. Cependant, bien que cette méthode soit la plus simple à implémenter, elle n'est pas automatique et ne peut pas être appelée directement par l'API GitHub Webhook. La seule façon de procéder de manière automatisée consiste à utiliser Travis CI pour envoyer le message au serveur de production après un test réussi.

D'autres résultats populaires sont soit [vieux de 6 ans](https://github.com/mboynes/github-deploy), [n'offre qu'un support de base pour les scripts](https://github.com/adnanh/webhook), [offre seulement 1 site gratuit](https://signup.deploybot.com/signup/new#pricing), [offre un essai gratuit limité à 10 jours seulement](https://www.deployhq.com/pricing), [contient une énorme vulnérabilité de sécurité](https://gist.github.com/oodavid/1809044#gistcomment-2237254), [semble excessivement compliqué](https://www.heroku.com/home), [n'est qu'un tas de code "spaghetti"](https://github.com/markomarkovic/simple-php-git-deploy/blob/master/deploy.php) ou [ne gère pas l'URL publique](https://deployer.org). [Un projet était proche](https://github.com/scriptburn/git-auto-deploy), mais il contient une interface utilisateur qui n'a pas l'air trop sûr au premier coup d'oeil.

La [documentation de Github](https://developer.github.com/v3/guides/delivering-deployments/#writing-your-server) pointe vers [Sinatra](http://sinatrarb.com/), une application Ruby utilisé pour créer rapidement des applications Web. Malheureusement, Sinatra est davantage orienté vers une utilisation locale, et non comme un _deamon_ à installer sur un serveur de production.

Cela signifie qu'il y a un besoin pour **une nouvelle solution de déploiement auto-hébergée**. Ou pas ?


## Écrire ma propre application

À ce stade, j'ai envisagé de créer ma propre application de déploiement. La question qui tue : qu'est-ce qui fait une bonne application de déploiement? Comment ça marche? Et à quoi cela ressemblerait-il si je l'écrivais? Faisons une liste:

1. Écrit en PHP (plus rapide et plus facile à coder pour moi);
1. Configuration facile via Composer, globalement ou par projet;
1. Pas d'interface utilisateur. Le seul point accessible publiquement est l'URL pour lancer le script. Une application CLI est parfaite, question de sécurité et est plus facile à utiliser;
1. URL publique pour la réception des _Webhook_, en dehors du dépôt git que nous souhaitons déployer [pour une sécurité accrue](https://www.exploit-db.com/ghdb/4593);
1. Utilisation des clés SSH, pas de mot de passe ni de jeton enregistré;
1. Peut être intégré à une application existante via Composer, en utilisant `vendor/bin/post-receive`;
1. Peut gérer les autorisations du serveur, tout en étant sécurisé (Utilisation de `sudo` sur une commande si nécessaire);
1. Prise en charge de Grav, mais aussi d’autres framework;
1. Prise en charge des Webhooks GitHub, tout en étant modulaire pour pouvoir s'intégrer à d’autres fournisseurs (BitBucket, etc.);
1. Configuration sauvegardée dans des fichiers locaux. Utilisé dans une application existante, la config peut être stocké dans un fichier du genre `.deployment`;

Et voilà. Une application en ligne de commande, écrite avec le merveilleux [_Console Component_ de Symfony](https://symfony.com/doc/current/components/console.html) et capable de déployer notre code, avec une URL publique pour gérer la demande de hook de réception !

Mais attendez, quelque chose cloche. Si cette application peut être chargée par Composer dans un projet existant, comment peut-elle gérer l'URL public? Même si notre nouvelle app est chargé globalement, cela nécessite toujours une configuration supplémentaire dans Apache / Nginx. Peut-être que l’utilisation de la commande `create-project` de Composer pourrait être utile?

Ou peut-être que cette liste, le concept même d'un script de déploiement et ma perception de ce que c'est... est erroné...?


## C'est un travail d'équipe

Plus je réfléchis à cette question et plus je fais de recherches, plus je réalise que ma perception de _**déploiement**_ est erroné. Le concept d'un script de déploiement ne peut pas être à la fois capable de gérer l'URL publique pour le hook.

En regardant de plus près la _liste_, une chose devient évidente: un script de déploiement ne pourra jamais satisfaire les points de la liste, car certains de ces points ne sont pas compatibles les uns avec les autres!

Nous ne pouvons pas avoir l'URL publique gérée en dehors du code que nous souhaitons déployer, et avoir toujours le script de déploiement à l'intérieur en même temps! Pour cette partie, il n’y a pas d’autres solution: nous avons besoin d’un vhost Apache / Nginx dédié. La seule autre manière pour un service externe (GitHub) d’envoyer une requête (ping) à notre serveur serait avec une connexion SSH. Cela pourrait techniquement être fait en utilisant un service tel que Travis. Cependant, même si nous finissons par utiliser un service tel que Travis pour envoyer une requête ping à notre serveur de production, nous avons toujours besoin d'un script robuste pour gérer la procédure de déploiement une fois le ping reçu...

Bref, ça ne signifie seulement qu'une chose. **Ma perception _était_ erronée. C'est un travail d'équipe qui nécessite _deux_ parties**: Le **script de déploiement** et l'**url publique**:

[center]![](Deployment2.png)[/center]

Maintenant que nous avons établi ceci, certains [candidats potentiels](#candidats-potentiels) commencent à faire du sens. Peut-être que certains d'entre eux pourraient être utiles après tout...


### Le script de déploiement : Deployer

Ici, ce n'est pas très compliqué. L'application [Deployer](https://deployer.org) est littéralement _A deployment tool for PHP_ (un outil de déploiement pour PHP).

Ma première pensée quand je suis tombé sur cette application était qu'elle ne pouvait pas gérer l'URL publique. En effet, Deployer doit être exécuté à partir d'une machine de déploiement (votre ordinateur), et non du serveur de production lui-même. Mais je ne pense pas que ce serait trop compliqué de faire en sorte que le serveur de production se déploie lui-même. Ce faisant, nous allons essentiellement tout exécuter sur le même ordinateur (sauf si vous avez un serveur de déploiement dédié, ce qui n’est pas mon cas).

Bien sûr, nous risquons maintenant entrer dans une boucle infinie: comment déployer notre script de déploiement? Je pense qu'en ajoutant votre script de déploiement spécifique au projet dans le répertoire _git_ du projet lui-même, cela devrait être correct. La seule chose dont nous avons besoin maintenant est _quelque chose_ pour déclencher la construction.


### Gestion de l'URL publique : Webhook & Travis

Celui-ci est un peu plus compliqué. Il y a [Webhook](https://github.com/adnanh/webhook), un outil écrit en Go, qui permet de créer facilement des points de terminaison HTTP (hook) sur un serveur. D'un côté, _Webhook_ demande d'installer quelque chose sur le serveur (ce qui n'est pas un gros problème avec un VPS), mais, selon leur [fichier Readme](https://github.com/adnanh/webhook#configuration), il doti être exécuté manuellement et écouter sur un port non standard. Cela pourrait être un problème, mais peut probablement être résolu avec le _Mod Proxy_ d'Apache (comme nous aurons quand même besoin d'un vhost dédié).

Un autre moyen de déclencher notre script de déploiement serait d’utiliser **Travis CI**. Travis offre quelques avantages. Par exemple, on peut déclencher un déploiement qu'après un _build_ ou des tests réussis. Il ne requière pas nécessairement une URL publique et de _Webhook_, donc pas non plus de serveur Apache / Nginx Vhost dédié. Par contre, Travis nécessiterait un accès SSH à notre serveur de production.

Je pense qu'en fin de compte, cela est vraiment du cas par cas. Pour Grav, il n’y a pas de _build_ ou de tests, donc nous n’avons généralement pas Travis prêt è l'emploie. Pour les applications plus complexes, par exemple une application créée avec [UserFrosting](https://www.userfrosting.com), Travis est déjà opérationnel dans mon cas. Par conséquent, l'ajout du déploiement aux instructions de Travis pourrait être préférable, on est sûr de ne pas déployer de mauvais code si un test échoue.

Mais une chose est sûre: _Webhook_ ou _Travis_, ils devront travailler avec _Deployer_ pour arriver à notre fin. Voici donc mon plan à l'heure actuelle :

[center]![](Deployment3.png)[/center]


## Conclusion

Une chose est sûre. Ce sujet n'est pas très bien documenté. La plupart des [références](#références) que j'ai trouvées expliquent _Deployer_ ou _Webhook_. Le déploiement automatique ne semble pas être un sujet populaire, probablement parce que vous devez avoir confiance en votre code pour ne pas planter à chaque _commit_ (C'est pour cela qu'on utilise des tests automatisés, n'est-ce pas?). C'est peut-être aussi parce qu'il n'y a pas de solution universelle...

Depuis que nous nous sommes rendus compte que _nous n'avions pas déployé la documentation sur le serveur de production depuis six mois_ avec l'équipe de UserFrosting, je pense que le déploiement automatisé est une chose sur laquelle nous devrions tous nous pencher. Heureusement, nous avons maintenant des outils qui peuvent nous rendre la vie plus facile. Fini l'époque du FTP! Et si _Deployer_ se révèle digne de sa réputation, je pense que l'époque du déploiement manuel sur le serveur de production, en le connectant via SSH, sera bientôt révolue, enfin pour moi.

Fait amusant, ce n'est pas la première fois que j'expérimente avec le déploiement automatisé. Lorsque je travaillais sur [SimpsonsCity.com](https://simpsonscity.com) il y a quelques années (vers 2012-2013?), j'avais un script PHP un peu pêle-mêle, très public (et potentiellement très non sécurisé) qui gérait la procédure de déploiement. Naviguez vers une URL, entrez votre mot de passe `Htpasswd`, et vous pouviez voir l'intégralité du `git clone` et ainsi de suite exécuté sous vos yeux. Aie aie aie... C'est drôle de voir que le même problème est toujours présent aujourd'hui, mais nous avons maintenant plus d'outils pour le gérer (et j'ai aussi plus d'expérience) sans avoir de solution définitive.

Bref, dans les prochains jours, je testerai à la fois _Deployer_ et _Webhook_ et je fais m'assurer de rédiger un guide définitif une fois que tout sera installé!


### Références
- [Deploying from GitHub to a Server](https://www.sitepoint.com/deploying-from-github-to-a-server/)
- [How To Automatically Deploy Your PHP Apps](https://www.codepicky.com/php-automatic-deploy/)
- [Making deployment a piece of cake with Deployer](https://www.silverstripe.org/blog/making-deployment-a-piece-of-cake-with-deployer/)
- [Zero downtime local build Laravel 5 deploys with Deployer](https://medium.com/@nickdenardis/zero-downtime-local-build-laravel-5-deploys-with-deployer-a152f0a1411f)
- [Automatic deployment with Deployer](https://webthoughts.koderhut.eu/automatic-deployment-with-deployer-b3eb39c88665)
- [Deploy using GitHub webhooks](https://davidauthier.com/blog/deploy-using-github-webhooks.html)
- [TravisDeployer](https://github.com/Enrise/TravisDeployer)
