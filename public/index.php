<?php

use \Sofi\router;
use \Sofi\http\Http;

//TODO All
ini_set('display_errors', true);

\Sofi\SofiBase::init();

$login = function () {
    if (empty($_SESSION['adminLogin'])) {
        if (!empty($_POST['user']) && !empty($_POST['pass'])) {
            if ($_POST['user'] == 'dimax' && $_POST['pass'] == '23051984') {
                $_SESSION['adminLogin'] = true;
                return true;
            } else {
                echo (new SysCode\Sofi\mvc\view\View(BASE_PATH . 'app/resources/views/'))->name('login')->render();
                return false;
            }
        } else {
            echo (new SysCode\Sofi\mvc\view\View(BASE_PATH . 'app/resources/views/'))->name('login')->render();
            return false;
        }
    } else {
        return true;
    }
};

$Routes = new router\RouteCollection();
$Routes
        ->route(
                '/', ['app\controllers\FrontController@actionIndex']
        )
        ->route(
                '/manage', ['app\modules\manage\controllers\ManageController@index'], router\Router::ANY_METHOD, '', [
            $login
                ]
        )
;

$Router = new router\Router();
try {
    $Router
            ->collection($Routes)
            ->dispatch(
                    Http::getURI(), Http::getMethod()
    );
} catch (\SysCode\Sofi\router\exceptions\RouteNotFound $e) {
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
    echo '<h1>Ошибка: запрашиваемая Вами страница не найдена</h1>';
    echo 'перейти на страницу <a href="/">Home</a>';
} catch (SysCode\Sofi\router\exceptions\InvalidRouteCallback $e) {
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
    echo '<h1>Ошибка: запрашиваемая Вами страница не найдена</h1>';
    echo 'перейти на страницу <a href="/">Home</a>';
} finally {
    
}

