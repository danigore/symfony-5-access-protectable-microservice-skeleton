Stateless and database-less JWT authorization ready Symfony 5 microservice skeleton with secure cookie token extractor. Like, the access protectable part of [danigore/symfony-5-identity-provider-microservice](https://github.com/danigore/symfony-5-identity-provider-microservice)

1; Update dependencies:
---
`$ composer update`

2; And test it ...
---
`$ php bin/phpunit`

*The `./bin/phpunit` command is created by Symfony Flex when installing the phpunit-bridge package. If the command is missing, you can remove the package (`composer remove symfony/phpunit-bridge`) and install it again. **Another solution is to remove the project’s symfony.lock file and run** `composer install` to force the execution of all Symfony Flex recipes.*

... You can generate your SSH keys with these:
---
`$ mkdir -p config/jwt`</br>
`$ openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096`</br>
`$ openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout`</br>

*Any more info about the **lexik/jwt-authentication-bundle** here:*
[LexikJWTAuthenticationBundle#getting-started](https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md#getting-started)