This project is created to demonstrate a sample task.

I assume that the docker is installed on your computer. It is not a requirement but it helps to reproduce the same
runtime environment anywhere (PHP 7.1 with mysqli).

Usage with docker:

1. clone the repository or download the files
2. cd into the project directory
3. run composer install to download project dependencies into the vendor directory (slim, php-view as template)
4. change the path to your installation directory in the docker-compose.yml file
4. run docker-compose up -d
5. append the "127.0.0.1 project_test.local" line to your host file
6. run db/schema.sql in mysql and setup your access, see environment variable in docker/nginx/conf.d/project_test.conf
7. open your browser and type
    http://project_test.local/populate_db to populate database with sample data
    http://project_test.local/ to run the main application

Usage without docker:

If your PHP version is equal or greater than 5.4 there is a built in server and you should set the database environment variables only.

1. clone the repository or download the files
2. cd into the project directory
3. get your database settings (host,user,password) and type the following in the command line
DB_HOST="127.0.0.1" DB_USER=<your user> DB_PASS=<your password> php -d variables_order=EGPCS -S localhost:8000
4. open the browser and type: http://localhost:8000/

Frontend modification:

npm install
npm run build
./node_modules/.bin/webpack --config webpack.config.js