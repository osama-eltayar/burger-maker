# simple burger-maker system

>assessment backend task for fodics Company

**small app for burger maker using laravel api and docker for development

## Install instructions

1. in terminal `$ git clone https://github.com/osama-eltayar/burger-maker`
2. in terminal `$ cd burger-maker`
3. in terminal  `$ cp .env.example .env`
4. in terminal  `docker run --rm \
   -u "$(id -u):$(id -g)" \
   -v "$(pwd):/var/www/html" \
   -w /var/www/html \
   laravelsail/php83-composer:latest \
   composer install --ignore-platform-reqs`
5. in terminal  `$ ./vendor/bin/sail artisan key:generate`
6. in terminal  `$ ./vendor/bin/sail up`
7. in terminal  `$ ./vendor/bin/sail artisan migrate --seed`

** by default mysql image expose on 3306 port if you want change it update this var in .env FORWARD_DB_PORT

** by default application running on port 80  you can change it from env APP_PORT

** by default notification running on database queue if you want try it configure your mail service and run queue 

to run test cases you can
in terminal  `$ ./vendor/bin/sail artisan test`

you can use the postman collection in the repo 
