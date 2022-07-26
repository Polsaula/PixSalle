<?php

declare(strict_types=1);

use Salle\PixSalle\Controller\AlbumController;
use Salle\PixSalle\Controller\BlogController;
use Salle\PixSalle\Controller\ExploreController;
use Salle\PixSalle\Controller\HomeController;
use Salle\PixSalle\Controller\MembershipController;
use Salle\PixSalle\Controller\PortfolioController;
use Salle\PixSalle\Controller\ProfileController;
use Salle\PixSalle\Controller\SignUpController;
use Salle\PixSalle\Controller\UserSessionController;
use Salle\PixSalle\Controller\PwdController;
use Salle\PixSalle\Controller\WalletController;
use Salle\PixSalle\Middleware\LoginMiddleware;
use Salle\PixSalle\Middleware\MainMiddleware;
use Slim\App;


/* GET */
$app->get(
    '/',
    HomeController::class . ':showHome'
)->setName('home');

$app->get(
    '/sign-in',
    UserSessionController::class . ':showSignInForm'
)->setName('signIn')->add(MainMiddleware::class);

$app->get(
    '/sign-up',
    SignUpController::class . ':showSignUpForm'
)->setName('signUp')->add(MainMiddleware::class);

$app->get(
    '/explore',
    ExploreController::class . ':showExplore'
)->setName('explore')->add(LoginMiddleware::class);

$app->get(
    '/portfolio',
    PortfolioController::class . ':showPortfolio'
)->setName('portfolio')->add(LoginMiddleware::class);

$app->get(
    '/blog',
    BlogController::class . ':showBlog'
)->setName('blog');

$app->get(
    '/blog/{id}',
    BlogController::class . ':showBlogDetail'
)->setName('blog');

$app->get(
    '/profile',
    ProfileController::class . ':showProfile'
)->setName('home')->add(LoginMiddleware::class);

$app->get(
    '/user/wallet',
    WalletController::class . ':showWallet'
)->setName('wallet')->add(LoginMiddleware::class);

$app->get(
    '/user/membership',
    MembershipController::class . ':showMembership'
)->setName('membership')->add(LoginMiddleware::class);

$app->get(
    '/profile/changePassword',
    PwdController::class . ':showPwdSettings'
)->setName('home')->add(LoginMiddleware::class);

$app->get(
    '/portfolio/album/{id}',
    AlbumController::class . ':showAlbum'
)->setName('home')->add(LoginMiddleware::class);



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

$app->post(
    '/profile/changePassword',
    PwdController::class . ':updatePassword');

$app->post(
    '/user/wallet',
    WalletController::class . ':updateWallet');

$app->post(
    '/portfolio',
    PortfolioController::class . ':createPortfolio');

$app->post(
    '/portfolio/album',
    PortfolioController::class . ':createAlbum');

$app->post(
    '/portfolio/album/{id}',
    AlbumController::class . ':addImage'
);

$app->post(
    '/barcode/{albumId}',
    AlbumController::class . ':generateQRCode'
);

/* DELETE */
$app->delete(
    '/portfolio/album/{id}',
    AlbumController::class . ":deleteImage"
);


/* API */

$app->get(
    '/api/blog',
    BlogController::class . ':getBlog'
);
$app->get(
    '/api/blog/{id}',
    BlogController::class . ':getBlogId'
);
$app->post(
    '/api/blog',
    BlogController::class . ':postBlog'
);
$app->put(
    '/api/blog/{id}',
    BlogController::class . ':putBlog'
);
$app->delete(
    '/api/blog/{id}',
    BlogController::class . ':deleteBlogId'
);

