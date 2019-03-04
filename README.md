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
bin/grav clear-cache
```

Permissions :
```
sudo chown -R www-data:malou .
find . -type f | xargs chmod 664
find ./bin -type f | xargs chmod 775
find . -type d | xargs chmod 775
find . -type d | xargs chmod +s
```
