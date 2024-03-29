@servers(['web' => 'schettler.net'])

@setup
    $repo = 'git@github.com:knowfox/base.git';
    $repo_branch = 'master';
    //$repo_branch = '2021-07';
    $root_dir = '/var/www/knowfox';
    $releases_dir = "{$root_dir}/releases";
    //$releases_dir = "{$root_dir}/l8";
    $now = strftime('%Y%m%d-%H%M%S');
    $release_dir = "{$releases_dir}/{$now}";

    if (!empty($target)) {
        $env = '.env-' . $target;
    }
    else {
        $target = 'current';
        $env = '.env';
    }
@endsetup

@task('deploy', ['on' => 'web'])
    set -x
    if [[ "{{ $target }}" = "current" ]]
    then
      cd {{ $releases_dir }};
      old=$(ls -t1  | tail -n +3)
      if [[ -n $old ]]; then echo $old | xargs rm -r; fi
    fi
    cd {{ $root_dir }};
    git clone -b {{ $repo_branch }} {{ $repo }} {{ $release_dir }};
    cd {{ $release_dir }};
    git submodule update --init --remote --recursive

    rm -rf storage
    rm -rf bootstrap/cache
    ln -s {{ $root_dir }}/shared/storage storage
    ln -s {{ $root_dir }}/shared/storage/app/public public/storage
    ln -s {{ $root_dir }}/shared/cache bootstrap/cache
    ln -s {{ $root_dir }}/shared/{{ $env }} .env
    rm -rf public/system
    ln -s {{ $root_dir }}/shared/uploads public/uploads
    ln -s {{ $root_dir }}/shared/presentation public/presentation

    composer install --ignore-platform-reqs

    php artisan cache:clear
    php artisan migrate

    ##rm package-lock.json
    ##npm install --no-optional --legacy-peer-deps
    ##npm run production
    yarn install
    yarn run production

    #chmod 777 storage/logs/laravel.log
    #sudo setfacl -R -m u:www-data:rwX -m u:olav:rwX {{ $root_dir }}/shared/storage
    #sudo setfacl -dR -m u:www-data:rwX -m u:olav:rwX {{ $root_dir }}/shared/storage

    cd {{ $root_dir }};
    ln -s {{ $release_dir }} {{ $target }}-{{ $now }}
    mv -T {{ $target }}-{{ $now }} {{ $target }}

    cd {{ $root_dir }}/{{ $target }}; php artisan queue:restart
    ##; sudo /usr/local/sbin/restart-php
@endtask
