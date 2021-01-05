<?php
namespace Deployer;

require 'recipe/common.php';

// Project name
set('application', 'BBQ');

// Project repository
set('repository', 'https://github.com/lcharette/website.git');
set('branch', 'master');

// Will be effective with Deployer 7.0
// set('current_path', '{{deploy_path}}/user');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Writable dirs by web server 
set('writable_dirs', []);
set('allow_anonymous_stats', false);

// Hosts
host('bbqsoftwares.com')
    ->user('deploy')
    ->identityFile('~/.ssh/deploy_rsa')
    ->stage('prod')
    ->set('deploy_path', '/var/www/bbqsoftwares.com');

host('bbq.test')
    ->user('vagrant')
    ->stage('dev')
    ->set('deploy_path', '/home/vagrant/bbqsoftwares');

// Install grav task
task('grav:install', function () {
    within('{{deploy_path}}', function () {
        run('bin/grav install');
    });
});

// Clear grav cache task
task('grav:clearcache', function () {
    within('{{deploy_path}}', function () {
        run('bin/grav clearcache');
    });
});

// Tasks

desc('Deploy your project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:clear_paths',
    'deploy:symlink',
    'grav:install',
    'grav:clearcache',
    'deploy:unlock',
    'cleanup',
    'success'
]);

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
