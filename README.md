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

Doctrine ORM Module vs Doctrine Module vs DoctrineORMBundle...

Configuration

Docker compose avec DB