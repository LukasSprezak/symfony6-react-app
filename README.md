<h4><b>Requirements:</b></h4>
- PHP 8.2
- Postgresql

<h4><b>Usage:</b></h4>
- `docker compose up -d`
- `docker exec -it php82-container bash`
- `./setup.sh OR sh setup.sh` 
- `php bin/console lexik:jwt:generate-keypair`


<h4><b>Tests:</b></h4>
`php bin/phpunit` - running all tests <br/>
`php bin/phpunit --fi ExampleClassTest::testExample` - execution of a single test <br/>