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

> #### Additionally
>
> **Originally enabled the secure http-only cookie token extractor** (to provide security against XSS attacks):
> [LexikJWTAuthenticationBundle/1-configuration-reference.md#automatically-generating-cookies](https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/1-configuration-reference.md#automatically-generating-cookies)
>
> *... but the shift back to the authorization header type extractor is easy to, just update the lexik_jwt_authentication config file by this commit:*
> [commit/Insecure authorization header type token extractor mode](https://github.com/danigore/symfony-5-access-protectable-microservice-skeleton/commit/96d0a45869c68c3b0d10b188ab6a4d7f19b8af0c)
>
> ***More info about why is the combination of JWT and XSS so relevant***:
> [Christian Kolb:Improve security when working with JWT and Symfony](https://blog.liplex.de/improve-security-when-working-with-jwt-and-symfony/)

*Offtopic: I implemented an entity constraint validator subscriber to this skeleton, what subscribed to the prePersist ORM lifecycle event (more info:[doctrine-orm/lifecycle-events](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/events.html#lifecycle-events), primarily because I create this service for myself, but anyway in my opinion **the entity validation should be automated** in a REST api **and the application layer should be throws an exception, if that entity is invalid**. So, the Pokemon entity is just because for the tests, so, you know, just delete the unnecessary files (App\Entity\Pokemon, App\Repository\PokemonRepository, App\Tests\Entity\PokemonTest).*