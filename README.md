# My personal site source

## Installation

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

## Admin Plugin
In development mode, the [Grav admin plugin](https://github.com/getgrav/grav-plugin-admin) can be installed to edit pages :

```
$ bin/gpm install admin
```
