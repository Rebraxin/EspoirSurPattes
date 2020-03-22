<?php

namespace Deployer;

require 'recipe/symfony4.php';

// Project name
set('application', 'projet-espoirs-sur-patte');

// Project repository
set('repository', 'git@github.com:O-clock-Y/projet-espoirs-sur-patte.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Shared files/dirs between deploys 
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server 
add('writable_dirs', []);
set('allow_anonymous_stats', false);

// Hosts

host('51.77.215.247')
->user('deployer')
->identityFile('~/.ssh/id_rsa')
->set('deploy_path', '/var/webapps/projet-espoirs-sur-patte'); 
// Tasks

task('build', function () {
run('cd {{release_path}} && build');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'database:migrate');
