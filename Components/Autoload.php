<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 05.08.17
 * Time: 9:26
 */
spl_autoload_register('autoload');

const COMPONENTS_DIR = '/Components/';
const MODELS_DIR = '/Model/';
const CONTROLLERS_DIR = '/Controller/';
const ENTITIES_DIR = '/Entity/';
const CONFIGS_DIR ='/Config/';

/**
 * Autoload function
 *
 * @param $className
 */
function autoload($className) // TODO something with hard code
{
    $path = $className . '.php';

    if (file_exists($path)) {
        include $path;
    } else {
        $path = __ROOT__ . COMPONENTS_DIR . $className . '.php';

        if (file_exists($path)) {
            include $path;
        } else {
            $path = __ROOT__ . MODELS_DIR . $className . '.php';

            if (file_exists($path)) {
                include $path;
            } else {
                $path = __ROOT__ . CONTROLLERS_DIR . $className . '.php';

                if (file_exists($path)) {
                    include $path;
                } else {
                    $path = __ROOT__ . ENTITIES_DIR . $className . '.php';

                    if (file_exists($path)) {
                        include $path;
                    } else {
                        $path = __ROOT__ . CONFIGS_DIR . $className . '.php';

                        if (file_exists($path)) {
                            include $path;
                        }
                    }
                }
            }
        }
    }
}