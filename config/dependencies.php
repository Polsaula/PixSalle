<?php

declare(strict_types=1);

use DI\Container;
use Psr\Container\ContainerInterface;
use Salle\PixSalle\Controller\BlogController;
use Salle\PixSalle\Controller\ExploreController;
use Salle\PixSalle\Controller\HomeController;
use Salle\PixSalle\Controller\MembershipController;
use Salle\PixSalle\Controller\PortfolioController;
use Salle\PixSalle\Controller\ProfileController;
use Salle\PixSalle\Controller\SignUpController;
use Salle\PixSalle\Controller\UserSessionController;
use Salle\PixSalle\Controller\WalletController;
use Salle\PixSalle\Repository\MySQLUserRepository;
use Salle\PixSalle\Controller\PwdController;
use Salle\PixSalle\Repository\PDOConnectionBuilder;
use Slim\Flash\Messages;
use Slim\Views\Twig;

$container = new Container();


$container->set(
    'view',
    function (ContainerInterface $c) {
        if(!isset($_SESSION['start'])){
            session_start();
            $_SESSION['start'] = "started";
            
        }
        $view = Twig::create(__DIR__ . '/../templates', ['cache' => false]);
        $view->getEnvironment()->addGlobal('session', $_SESSION);
        $view->getEnvironment()->addGlobal('flash', $c->get(Messages::class));
        return $view;
    }
);

$container->set('flash', function () {
    return new Messages();
});

$container->set('db', function () {
    $connectionBuilder = new PDOConnectionBuilder();
    return $connectionBuilder->build(
        $_ENV['MYSQL_ROOT_USER'],
        $_ENV['MYSQL_ROOT_PASSWORD'],
        $_ENV['MYSQL_HOST'],
        $_ENV['MYSQL_PORT'],
        $_ENV['MYSQL_DATABASE']
    );
});

$container->set(UserRepository::class, function (ContainerInterface $container) {
    return new MySQLUserRepository($container->get('db'));
});

$container->set(
    HomeController::class,
    function (ContainerInterface $c) {
        return new HomeController($c->get('view'), $c->get(UserRepository::class));
    }
);

$container->set(
    UserSessionController::class,
    function (ContainerInterface $c) {
        return new UserSessionController($c->get('view'), $c->get(UserRepository::class));
    }
);

$container->set(
    SignUpController::class,
    function (ContainerInterface $c) {
        return new SignUpController($c->get('view'), $c->get(UserRepository::class));
    }
);

$container->set(
    ProfileController::class,
    function (ContainerInterface $c) {
        return new ProfileController($c->get('view'), $c->get(UserRepository::class), $c->get('flash'));
    }
);

$container->set(
    BlogController::class,
    function (ContainerInterface $c) {
        return new BlogController($c->get('view'), $c->get(UserRepository::class));
    }
);

$container->set(
    ExploreController::class,
    function (ContainerInterface $c) {
        return new ExploreController($c->get('view'), $c->get(UserRepository::class));
    }
);

$container->set(
    PortfolioController::class,
    function (ContainerInterface $c) {
        return new PortfolioController($c->get('view'), $c->get(UserRepository::class));
    }
);

$container->set(
    WalletController::class,
    function (ContainerInterface $c) {
        return new WalletController($c->get('view'), $c->get(UserRepository::class));
    }
);

$container->set(
    MembershipController::class,
    function (ContainerInterface $c) {
        return new MembershipController($c->get('view'), $c->get(UserRepository::class));
    }
);

$container->set(
    PwdController::class,
    function (ContainerInterface $c) {
        return new PwdController($c->get('view'), $c->get(UserRepository::class));
    }
);



