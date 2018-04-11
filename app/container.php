<?php

use Slim\Container;

// Error Handlers
// $container['errorHandler'] = function (Container $c):callable {
//    return function (\Slim\Http\Request $request, \Slim\Http\Response $response, $e) use ($c):\Slim\Http\Response  {
//        $response = $response->withStatus(500);
//        return $c['twig']->render($response, 'error.twig', ['statusCode' => 500, 'message' => 'Что-то пошло не так...']);
//    };
// };
// $container['notFoundHandler'] = function (Container $c):callable {
//    return function (\Slim\Http\Request $requset, \Slim\Http\Response $response) use ($c): \Slim\Http\Response  {
//        $response = $response->withStatus(404);
//        return $c['twig']->render($response, 'error.twig', ['statusCode' => 404, 'message' => 'Страницы с таким адресом не существует']);
 //   };
//};

// Dependencies

// Eloquent ORM
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']); // primary db
$capsule->addConnection($container['settings']['sphinx'], 'sphinxSearch'); // sphinx search
$capsule->setAsGlobal();
$capsule->bootEloquent();
$container['db'] = function () use ($capsule) {
    return $capsule;
};
// Twig
$container['twig'] = function (Container $c): \Slim\Views\Twig {
    $twig = new \Slim\Views\Twig('../views/templates', [
        'strict_variables' => true
    ]);
    $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $twig->addExtension(new \Slim\Views\TwigExtension($c['router'], $basePath));
    return $twig;
};
// Slim CSRF Guard
$container['csrf'] = function (Container $c): \Slim\Csrf\Guard {
    return new \Slim\Csrf\Guard('csrf', $storage = null, null, 200, 16, true);
};
// GetID3
require_once(__DIR__ . '/../vendor/james-heinrich/getid3/getid3/getid3.php');
$container['getID3'] = function (Container $c): getID3 {
    return new getID3 ();
};
// Controllers
$container['HomeController'] = function (Container $c): \Filehosting\Controllers\HomeController {
    return new \Filehosting\Controllers\HomeController($c);
};
$container['DownloadController'] = function (Container $c): \Filehosting\Controllers\DownloadController {
    return new \Filehosting\Controllers\DownloadController($c);
};
$container['SearchController'] = function (Container $c): \Filehosting\Controllers\SearchController {
    return new \Filehosting\Controllers\SearchController($c);
};
// Helpers
$container['fileSystem'] = function (): \Filehosting\Helpers\FileSystem {
    return new \Filehosting\Helpers\FileSystem (__DIR__);
};
$container['sphinxSearch'] = function (): \Filehosting\Helpers\SphinxSearch {
    return new \Filehosting\Helpers\SphinxSearch();
};
// Validators
$container['commentValidator'] = function (Container $c): \Filehosting\Validators\CommentValidator {
    return new \Filehosting\Validators\CommentValidator;
};
// UploaderAuth
$container['uploaderAuth'] = function (Container $c): \Filehosting\Auth\UploaderAuth {
    return new \Filehosting\Auth\UploaderAuth ($c);
};


