# My personal site source

## Docker

To run inside a Docker container, without the need to install Grav first, you first need to build the container :

```
docker build -t grav:latest .
```

Then you can run the container :
```
docker run -d --rm --name=BBQ -p 8080:80 -e DUID=1000 -e DGID=1000 -v "$(pwd):/var/www/grav/user" grav:latest
```

It will take a couples of second for the site to be up and running while the base Grav installation is setup. Once this is done, you can access the site at [http://localhost:8080/](http://localhost:8080/).

To stop the image:

```bash
docker stop BBQ
```

```bash
docker exec -it BBQ bash
chmod +x bin/gpm # This is only needed if permissions are acting up
bin/grav install
```

## Manual Installation

Clone Grav, from your install dir :
```
git clone https://github.com/getgrav/grav.git .
```

Git clone this repo into `/user`
```
rm -r user
git clone git@github.com:lcharette/website.git user
```

Install grav
```
bin/grav install
```

When in doubt, clear cache:
```
bin/grav clearcache
```

Permissions :
```
sudo chown -R www-data:malou .
find . -type f | xargs chmod 664
find ./bin -type f | xargs chmod 775
find . -type d | xargs chmod 775
find . -type d | xargs chmod +s
```
