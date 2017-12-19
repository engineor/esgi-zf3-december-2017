ESGI - Cours Zend Framework 3 - Decembre 2017
---------------------------------------------

# Session 1

## Présentation de la documentation

Zend Framework existe en 3 versions majeurs : 1, 2 et 3 (on s'en doutait un peu mais ces derniers temps c'est pas si évident... i.e. Windows, PHP, NodeJs...).

Les versions 1 et 2 ont une documentation sur le site [https://framework.zend.com/](https://framework.zend.com/) (respectivement [ici](https://framework.zend.com/manual/1.12/en/manual.html) et [ici](https://framework.zend.com/manual/2.4/en/index.html).

La version 3 est documentée sur [https://docs.zendframework.com/](https://docs.zendframework.com/). La documentation est en deux parties :

* les tutoriels (qui nous expliquent fonctionnellement comment agencer les composants pour faire une chose, i.e. [`Getting Started with Zend Framework MVC Applications`](https://docs.zendframework.com/tutorials/getting-started/overview/))
* les références des composants (qui nous donnent les détails techniques de chaque composants, notamment la syntaxe à utiliser, i.e. [Zend Service Manager](https://docs.zendframework.com/zend-servicemanager/)).

N'oubiez pas que la meilleure documentation c'est la lecture du code lui même !

## Présentation du Skeleton

Zend Framework propose un skelette d'application, qui contient notamment une structure MVC qui peut servir de base à vos projets.

[Zend Skeleton Application](https://github.com/zendframework/ZendSkeletonApplication)

Seul le dossier public est exposé à l'exterieur du serveur (voir la notion de `document root` dans Apache, Nginx, LightHTTPD, IIS, Caddy ou autre).

Le point d'entrée unique de l'application, comme dans la plupart des frameworks modernes, est `public/index.php`. Celui-ci est configuré pour fonctionner avec le serveur de développement PHP intégré (`php -S 0.0.0.0:8080 -t public/ public/index.php`) grâce à la présence des lignes suivantes qui permettent de servir le contenu static (les fichiers css et js) :

```php
// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (__FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}
```

Autre ligne notable dans le fichier, le `chdir(dirname(__DIR__));` qui permettra ailleurs dans l'application d'utiliser des liens relatifs vers les fichiers en commençant à exprimer le lien depuis la racine du projet.

Configuration de composer mise à part, il reste donc uniquement les dernières lignes permettant de configurer puis lancer l'application.

On distingue deux types de configurations : 

* la configuration de l'application (de `Zend\Application`), qui va entre autre exprimer comment aller chercher la configuration du contenu de l'application
* la configuration du contenu de l'application, qui va permettre la configuration des composants utilisés dans l'application (comme le `ServiceManager`, les accès à la base de données et autre choses du genre)

Le code suivant est issu de l'`index.php` et nous permet de chercher la configuration que l'on va ensuite passer à `Zend\Application` :

```php
// Retrieve configuration
$appConfig = require __DIR__ . '/../config/application.config.php';
if (file_exists(__DIR__ . '/../config/development.config.php')) {
    $appConfig = ArrayUtils::merge($appConfig, require __DIR__ . '/../config/development.config.php');
}
```

On constate d'abord la syntaxe utilisant le `require`, que l'on retrouvera dans beaucoup de parties du framework. Le fichier appelé par le `require` retourne un `array`, qui est donc assigné à la variable `$appConfig`.

On cherche ensuite à savoir si une configuration spécifique au développement existe, en quel cas celle-ci sera mergée avec le tableau précédemment récupéré. Noter le `ArrayUtils` permettant un merge recursif, contrairement à `array_merge`.

**Insérer ici une section sur la configuration de Zend Application, et annoncer les sections sur la configuration de dev et les modules.**

Une fois cette configuration récupérée, on la passe à l'application.

```php
Application::init($appConfig)->run();
```

**Expliquer le principe de boucle de dispatch (routing, dispatch...)**

## Les modules

Les modules sont les premières choses que le framework va charger (dans la [méthode `init` de `Zend\Application`](https://github.com/zendframework/zend-mvc/blob/master/src/Application.php) : `$serviceManager->get('ModuleManager')->loadModules();`). Les classes `Module` de chaque module déclarées dans le fichier `modules.config.php` vont être utilisées dans l'ordre utilisé pour aller chercher les configurations des modules à l'aide de différents listeners qui vont s'occuper de récupérer et merger les configs entre autre (voir le [`ModuleManager`](https://github.com/zendframework/zend-modulemanager/tree/master/src/Listener)).

**Annoncer un chapitre sur les events et listeners**

## Installation du projet
Dans le dossier où l'on veut le projet :

```
composer create-project -s dev zendframework/skeleton-application .
```

Questions :

`Do you want a minimal install (no optional packages)? Y/n` => `Y`

`Do you want to remove the existing VCS (.git, .svn..) history? [Y,n]?` => `Y`

**Inserer une explication du mode developpement**

```
zf-development-mode enable
You are now in development mode.
```

Lancement du projet avec `docker-compose up -d`.

## Ajout d'une page *"ping"*

1. Ajout de la route `/ping`
2. Ajout de la configuration du container de controllers
3. Ajout du controller lui-même
4. Ajout de la vue

### Ajout de la route `/ping`

Dans `module/Application/config/module.config.php`, ajouter une route basée sur la route `home`.

**Inserer une explication sur Literal/Segment et les autres routes possibles**

```
'ping' => [
    'type' => Literal::class,
    'options' => [
        'route'    => '/ping',
        'defaults' => [
            'controller' => Controller\PingController::class,
            'action' => 'ping',
        ],
    ],
],
```

Noter :

1. la valeur associée à la clé `controller` est l'alias du service à aller chercher dans le container de controller (le membre de gauche dans le tableau `controllers` => `factories`)
2. on fait une route literal car on connait la string exacte à passer dans l'url pour accéder à la page

### Ajout de la configuration du container de controllers

Sur le même principe que le controller existant :

```
'controllers' => [
    'factories' => [
        Controller\IndexController::class => InvokableFactory::class,
        Controller\PingController::class => InvokableFactory::class,
    ],
],
```

### Ajout du controller lui-même

```php
<?php

declare(strict_types=1);

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

final class PingController extends AbstractActionController
{
    public function pingAction() : ViewModel
    {
        return new ViewModel([
            'time' => new \DateTimeImmutable(),
        ]);
    }
}
```

### Ajout de la vue

### Discussions

* template_path_stack / template_map
* erreurs rencontrées :
  * controller non trouvé
  * config de développement non enable => cache donc pas de route en plus
  * fichier de vue manquant
* Short echo syntax
* inline docblock dans la vue
* layout
* affichage des détails d'exceptions

## Injection de dépendences

Objectif : passer le `DateTimeImmutable` à notre controller pour ne plus le créer dedans.

1. ajouter un constructeur
2. ajouter la factory qui va avec

### Ajouter un constructeur

Dans le controller :

```php
/**
 * @var \DateTimeImmutable
 */
private $dateTime;

public function __construct(\DateTimeImmutable $dateTime)
{
    $this->dateTime = $dateTime;
}
```

### ajouter la factory qui va avec

**Bien expliquer les factories et le InvokableFactory**

Dans la config du module application (`module/Application/config/module.config.php`) :

```php
'controllers' => [
    'factories' => [
       Controller\IndexController::class => InvokableFactory::class,
       Controller\PingController::class => Controller\PingControllerFactory::class,
    ],
],
```

Puis dans `module/Application/src/Controller\PingControllerFactory.php` :

```php
<?php

declare(strict_types=1);

namespace Application\Controller;

final class PingControllerFactory
{
    public function __invoke() : PingController
    {
        $dateTime = new \DateTimeImmutable();

        return new PingController($dateTime);
    }
}
```

### extra : créer une factory pour le `DateTimeImmutable`

```
'service_manager' => [
    'factories' => [
        \DateTimeImmutable::class => \Application\DateTimeImmutableFactory::class
    ],
],
```

`module/Application/src/DateTimeImmutableFactory.php` :

```
<?php

declare(strict_types=1);

namespace Application;

final class DateTimeImmutableFactory
{
    public function __invoke() : \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
```

**Expliquer le service_manager et les différents SM en se basant sur celui des controllers**

### extra : utiliser le container pour récupérer le `DateTimeImmutable`

Dans la factory du controller :

```
<?php

declare(strict_types=1);

namespace Application\Controller;

use Psr\Container\ContainerInterface;

final class PingControllerFactory
{
    public function __invoke(ContainerInterface $container) : PingController
    {
        $dateTime = $container->get(\DateTimeImmutable::class);

        return new PingController($dateTime);
    }
}
```

### extra : supprimer la factory inutile

Pas de paramètre requis dans le constructeur de `DateTimeImmutable`, donc on pourrait utiliser une `InvokableFactory` ! **(mais ici on garde notre factory custom pour la suite)**

## Injecter de la configuration

1. ajouter une config custom (i.e. une date en string à passer au `DateTimeImmutable`)
2. récupérer la config dans la factory du `DateTimeImmutable`

`module/Application/config/module.config.php` :

```php
'app' => [
   'date' => '2017-12-06',
],
```

Puis dans la factory (`module/Application/src/DateTimeImmutableFactory.php`) :

```php
<?php

declare(strict_types=1);

namespace Application;

use Psr\Container\ContainerInterface;

final class DateTimeImmutableFactory
{
    public function __invoke(ContainerInterface $container) : \DateTimeImmutable
    {
        $config = $container->get('config');
        if (!isset($config['app']['date']) || !is_string($config['app']['date'])) {
            throw new \Exception('Config manquante');
        }

        return new \DateTimeImmutable($config['app']['date']);
    }
}
```

# Doctrine

## Qu'est ce que Doctrine

Doctrine est composé de plusieurs parties :

 * Un [ORM](https://en.wikipedia.org/wiki/Object-relational_mapping), c'est à dire une couche permettant d'utiliser des objets dans le code PHP sans se préoccuper du schema de base de données.
 * Un [DBAL](https://en.wikipedia.org/wiki/Database_abstraction_layer), c'est à dire une interface d'abstraction à la base de données, qui va permettre l'utilisation de la même API pour contacter les différents [RDBMS](https://en.wikipedia.org/wiki/Relational_database_management_system)
 * Un [système de migration](https://en.wikipedia.org/wiki/Schema_migration), c'est à dire une solution permettant de faire des versions du schema de base de données
 * Un [système de fixtures](https://en.wikipedia.org/wiki/Database_seeding), permettant de charger des données dans la base à partir d'objets préconçus, souvent pour préparer une base de développement ou de tests
 
Ces différents projets sont disponibles sur le [github de Doctrine](https://github.com/doctrine).
 
En plus de ces projets, on trouvera de `*Bundle` et `*Module`, qui sont les intégrations au frameworks :
 
 * `DoctrineModule` = intégration du module de DBAL à Zend Framework
 * `DoctrineORMModule` = intégration du module d'ORM à Zend Framework
 * `DoctrineBundle` = intégration du DBAL et de l'ORM à Symfony
 
On note donc la sémantique du nom, `Bundle` pour Symfony, `Module` pour Zend Framework.

Pour la suite du cours, nous ne verrons que l'ORM, qui se base lui-même sur le DBAL.
 
## Configuration

Pour configurer Doctrine ORM dans notre projet, il faut se référer à la documentation fournie sur le [compte Github du projet](https://github.com/doctrine/DoctrineORMModule/).

Premièrement, il faut récupérer le code à l'aide de Composer :

```bash
composer require doctrine/doctrine-orm-module
```

Comme d'habitude Composer va demander où installer la configuration, soit dans notre cas `config/modules.config.php`.

On peut constater que `DoctrineORMModule` est chargé après `DoctrineModule`, car il existe une dépendence entre les deux, comme indiqué dans la méthode `getModuleDependencies` de [Module.php](https://github.com/doctrine/DoctrineORMModule/blob/master/src/DoctrineORMModule/Module.php).

Il faut ensuite configurer doctrine ORM conformément à ce qui est décrit dans le [README.md](https://github.com/doctrine/DoctrineORMModule/blob/master/README.md) du projet.

### Configuration des entités

Considérant que les entités seront définies dans notre module `Application`, on peut mettre la configuration fournie dans `module/Application/config/module.config.php`.

```php
'doctrine' => [
    'driver' => [
        // defines an annotation driver with two paths, and names it `my_annotation_driver`
        'application_driver' => [
            'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
            'cache' => 'array',
            'paths' => [
                __DIR__.'/../src/Entity/',
            ],
        ],

        // default metadata driver, aggregates all other drivers into a single one.
        // Override `orm_default` only if you know what you're doing
        'orm_default' => [
            'drivers' => [
                // register `application_driver` for any entity under namespace `Application\Entity`
                'Application\Entity' => 'application_driver',
            ],
        ],
    ],
],
```

La seconde partie du tableau défini sous la clé `orm_default` l'`EntityManager`. Pour en apprendre plus sur Doctrine et son fonctionnement il faut lire la [documentation](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/).

On constate que le membre de gauche du tableau correspond au **namespace** qu'auront nos entités. Le membre de droite correspond au *driver* à utiliser pour les entités de ce namespace.

Par exemple, si mon entité est `Application\Entity\Film`, je rentre dans ce cas, et mon driver sera donc `application_driver`, qui fait référence au `driver` en haut du tableau, qui nous dit donc que nous allons utiliser les annotations pour configurer l'entité, utiliser un cache en mémoire (array) et nous donne le(s) chemin(s) dans le projet dans lequel/lesquels chercher nos entités.

### Configuration de la connection

La dernière partie de la configuration consiste à indiquer à les informations de connection à la base de donnée.

#### Configuration locale

Comme vu précédemment, par défaut Zend Framework nous propose de charger les fichiers de configuration suivant le pattern suivant (`config/application.config.php`) :

```php
__DIR__.'/autoload/{{,*.}global,{,*.}local}.php'
```

On peut donc mettre cette configuration dans `config/autoload`, soit dans un fichier `*.global.php`, `global.php`, `*.local.php` ou encore `local.php`.

Étant donné que cette configuration contient des identifiants d'accès à la base de données, il ne faut pas le commiter, donc faire un fichier local. Nous pouvons donc créer `config/autoload/db.local.php` et mettre notre configuration dedans.

```php
<?php

use Doctrine\DBAL\Driver\PDOMySql\Driver;

return [
    'doctrine' => [
        'connection' => [
            // default connection name
            'orm_default' => [
                'driverClass' => Driver::class,
                'params' => [
                    'host'     => 'localhost',
                    'port'     => '3306',
                    'user'     => 'username',
                    'password' => 'password',
                    'dbname'   => 'database',
                ],
            ],
        ],
    ],
];

```

Ce fichier créé étant ignoré par Git, il ne se retrouvera pas sur le serveur de versionning ([exemple de problème lorsque l'on commit des identifiants](https://www.theregister.co.uk/2015/01/06/dev_blunder_shows_github_crawling_with_keyslurping_bots/)).

Il est donc de bonne pratique de copier notre fichier de configuration et de nommer le nouveau `doctrine.local.php.dist` (qui sera donc commité), retirer les identifiants de cette version, de sorte à donner un template de configuration aux autres développeurs.

#### Configuration avec des variables d'environnement

Le [twelve-factor appl manifesto](https://12factor.net/fr/), qui défini comme indiqué dans le nom 12 points pour faire une application moderne industrialisé, indique que la configuration de l'application devrait se faire via les variables d'environnement.

Docker vient nous faciliter la tâche pour le développent grace à la clé `environment` dans les services de docker-compose ou simplement le flag `-e` pour le lancement d'un container simple.

Dans ce cas, nous pouvons donc mettre la configuration directement dans le fichier `config/autoload/db.global.php`, et remplacer les valeurs par `$_ENV['MY_ENV_VAR']`.

La bonne pratique Unix veut que les applications préfixent leurs variables d'environnement, nous appellerons donc les notres `SKEL_DB_*` (`SKEL` pour skeleton application).

```php
<?php

use Doctrine\DBAL\Driver\PDOMySql\Driver;

return [
    'doctrine' => [
        'connection' => [
            // default connection name
            'orm_default' => [
                'driverClass' => Driver::class,
                'params' => [
                    'host'     => $_ENV['SKEL_DB_HOST'],
                    'port'     => $_ENV['SKEL_DB_PORT'],
                    'user'     => $_ENV['SKEL_DB_USER'],
                    'password' => $_ENV['SKEL_DB_PASS'],
                    'dbname'   => $_ENV['SKEL_DB_NAME'],
                ],
            ],
        ],
    ],
];
```

Il faut donc ensuite changer le `docker-compose.yml` pour refleter ces changements (on ajoute une base de données, on ajoute les variables d'environnement).

```yaml
version: "3.3"
services:
  zf:
    build:
      context: .
      dockerfile: Dockerfile
    ports:
      - "8080:80"
    volumes:
      - .:/var/www
    depends_on:
      - database
    environment:
      - SKEL_DB_HOST=database
      - SKEL_DB_NAME=demo
      - SKEL_DB_USER=demo
      - SKEL_DB_PASS=demo
      - SKEL_DB_PORT=3306
  database:
    image: mysql:5.7
    expose:
      - "3306"
    environment:
      - MYSQL_ROOT_PASSWORD=demo
      - MYSQL_DATABASE=demo
      - MYSQL_USER=demo
      - MYSQL_PASSWORD=demo
```

Ces informations peuvent être commitées car elles sont communes pour tous les développeurs et ne posent pas de risque de sécurité.

#### Configuration avec `$_ENV` sans Docker

Deux possibilités s'offrent à vous :

1. ajouter des variables d'environnement sur votre système
2. utiliser une librairie qui émule les variables d'environnement

La solution numéro 1 pose problème si vous voulez démarrer plusieurs instances de la même application avec différentes configurations.

Pour la solution 2, vous pouvez utiliser `symfony/dotenv`. Dans le scénario suivant, on considère que dotEnv est utilisé pour le développement, mais que les variables d'environnement seront utilisées sur la production.

Copier la configuration du chapitre au dessus, donc dans `config/autoload/db.global.php`.

Ensuite, on va prendre `symfony/dotenv` en dépendence de développement pour notre projet :

```bash
composer require --dev symfony/dotenv
``` 

Il reste à modifier le code comme indiqué sur la [documentation de Symfony](https://symfony.com/doc/current/components/dotenv.html). Notre point d'entré unique est `public/index.php`, c'est donc là que le chargement de dotEnv doit survenir, à la suite du chargement des vendor car il utilise les classes chargées.

La première étape consiste à vérifier si la classe existe (elle n'existera pas en prod à cause du `--dev`). Ensuite, il faut vérifier si le fichier `.env` existe. Puis charger la config si les deux sont vrai.

```php
// Load environment variables
if (class_exists(Dotenv::class) && is_file(__DIR__ . '/../.env')) {
    $dotenv = new Dotenv();
    $dotenv->load(__DIR__ . '/../.env');
}
``` 

Le fichier `.env` (à la racine du projet donc) doit être ajouté au `.gitignore`, et un fichier `.gitignore.dist` doit être créé pour donner le skeleton des valeurs attendues aux autres développeurs :

```dotenv
SKEL_DB_HOST=
SKEL_DB_NAME=
SKEL_DB_USER=
SKEL_DB_PASS=
SKEL_DB_PORT=
```

### Executer Doctrine CLI

Il faut ensuite faire fonctionner Doctrine pour valider notre configuration. La commande php est la suivante :

```bash
php vendor/bin/doctrine-module
```

**Note** : sous Windows il ne faut pas utiliser `php` devant (que ce soit sous directement sur le système hôte ou sur Docker).

Sans argument, cette commande va lister les arguments possibles.

La même commande suivi de `orm:info` doit permettre d'indiquer si le système est capable de trouver nos entités.

**Note** : pour ceux qui utilise `docker-compose` (ce que je recommande), il faut executer ces commandes dans le cadre de votre container. La commande devient donc :

```bash
docker-compose run --rm zf php vendor/bin/doctrine-module orm:info
``` 

**Note** : il se peut que le `Dockerfile` initial ne charge pas les drivers mysql pour pdo. Dans ce cas, changez le `Dockerfile` et ajoutez les modules.

**Note** : à chaque changement du `Dockerfile` et du `docker-compose.yml`, il faut executer `docker-compose up -d --build` pour recréer les containers.

```
&& docker-php-ext-install pdo pdo_mysql zip \
```

## Ajouter une entité

Une entité est une classe simple (Plain Old PHP Object, POPO), qui sera par la suite étendue par Doctrine ORM dans notre
implementation (attention de bien séparer le concept d'entité et son implementation dans Doctrine ORM, ici les classes
ne peuvent pas être final, mais c'est un problème de Doctrine ORM, les entités peuvent conceptuellement être finales).

Selon votre configuration précedemment définie, nous allons positionner une nouvelle classe dans le dossier contenant
les entités (ici `module/Application/src/Entity`). La commande `php vendor/bin/doctrine-module orm:info` devrait alors
retourner l'entité trouvée.

```php
<?php

declare(strict_types=1);

namespace Application\Entity;

use Ramsey\Uuid\Uuid;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Film
 *
 * Attention : Doctrine génère des classes proxy qui étendent les entités, celles-ci ne peuvent donc pas être finales !
 *
 * @package Application\Entity
 * @ORM\Entity
 * @ORM\Table(name="films")
 */
class Film
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=36)
     **/
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=2000, nullable=false)
     */
    private $description = '';

    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
    }

    /**
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title) : void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription() : string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description) : void
    {
        $this->description = $description;
    }
}
```

Ici notre [entité est dite anémique](https://beberlei.de/2012/08/22/building_an_object_model__no_setters_allowed.html), car elle ne contient que des accesseurs (aka. `getters` et `setters`).
Une entité a le rôle de garant de l'intégrité des données métier, et devrait donc ne pas avoir de setters mais uniquement
une création via le constructeur et des getters. Les méthodes de l'entité devraient lever des exceptions pour les
comportements non attendus.

Il est possible de faire des setters `fluents`, c'est à dire qui retournent l'objet sur lequel ils ont effectué le
changement d'état :

```php
    /**
     * @param string $description
     * @return Film  
     */
    public function setDescription(string $description) : Film
    {
        $this->description = $description;
        
        return $this;
    }

```

Le [fluent interface design pattern](https://ocramius.github.io/blog/fluent-interfaces-are-evil/) est considéré comme un antipattern sauf dans le cas d'un builder (comme Zend Db, Doctrine), car il
incite les développeurs à chainer un maximum de méthodes et donc faire plein de changements d'états successifs plutôt 

## Créer le schema

Une fois l'entité créée il ne reste qu'à executer doctrine pour créer le schema de base de données :

```
php vendor/bin/doctrine-module orm:schema-tool:update
```

L'option `--dump-sql` permet de vérifier le script SQL qui sera effectué, alors que `--force` permet d'executer le SQL
sur la base de données en question.

## Lister les données

Pour lister les données, il faut injecter le repository dans notre controlleur (via sa factory donc).

