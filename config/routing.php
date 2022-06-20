<?php

declare(strict_types=1);

use Salle\PixSalle\Controller\BlogController;
use Salle\PixSalle\Controller\ExploreController;
use Salle\PixSalle\Controller\HomeController;
use Salle\PixSalle\Controller\MembershipController;
use Salle\PixSalle\Controller\PortfolioController;
use Salle\PixSalle\Controller\ProfileController;
use Salle\PixSalle\Controller\SignUpController;
use Salle\PixSalle\Controller\UserSessionController;
use Salle\PixSalle\Controller\WalletController;
use Slim\App;

function addRoutes(App $app): void
{
    /* GET */
    $app->get(
        '/',
        HomeController::class . ':showHome'
    )->setName('home');

    $app->get(
        '/sign-in',
        UserSessionController::class . ':showSignInForm'
    )->setName('signIn');

    $app->get(
        '/sign-up',
        SignUpController::class . ':showSignUpForm'
    )->setName('signUp');

    $app->get(
        '/explore',
        ExploreController::class . ':showExplore'
    )->setName('explore');

    $app->get(
        '/portfolio',
        PortfolioController::class . ':showPortfolio'
    )->setName('portfolio');

    $app->get(
        '/blog',
        BlogController::class . ':showBlog'
    )->setName('blog');

    $app->get(
        '/profile',
        ProfileController::class . ':showProfile'
    )->setName('home');

    $app->get(
        '/user/wallet',
        WalletController::class . ':showWallet'
    )->setName('wallet');

    $app->get(
        '/user/membership',
        MembershipController::class . ':showMembership'
    )->setName('membership');



    /* POST */
    $app->post(
        '/sign-in',
        UserSessionController::class . ':signIn');

    $app->post(
        '/sign-out',
        UserSessionController::class . ':signOut');

    $app->post(
        '/sign-up',
        SignUpController::class . ':signUp');

    $app->post(
        '/profile',
        ProfileController::class . ':updateProfileData');

    $app->post(
        '/user/membership',
        MembershipController::class . ':updateMembership');
}
