## Instruction
- Clone repo
- Goto "laradock" folder
- Execute "docker-compose up -d nginx postgres"
- Execute "docker-compose exec --user=laradock workspace composer install -d=/var/www"
- Execute "docker-compose exec --user=laradock workspace php /var/www/artisan migrate"
