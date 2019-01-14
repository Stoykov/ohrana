<?php
namespace stoykov\Ohrana\Traits;

trait OhranaHelper
{
    /**
     * Parses the action to namespace, controller and action
     * @param  string $action
     * @return array
     */
    protected function parseAction($action)
    {
        //$action will have as value 'App\Http\Controllers\HomeController@showWelcome'
        $explodedAction = explode("\\", $action);
        list($controller, $method) = explode("@", $explodedAction[count($explodedAction) - 1]);
        unset($explodedAction[count($explodedAction) - 1]);

        return [
            "namespace" => implode("\\", $explodedAction),
            "controller" => $controller,
            "method" => $method
        ];
    }

    /**
     * Parses the permission to namespace, controller and action
     * @param  string $action
     * @return array
     */
    protected function parsePermission($permission)
    {
        // Return imediately if we have a global All permission
        if ($permission == 'All') {
            return [
                "namespace" => 'All',
                "controller" => 'All',
                "method" => 'All',
                "methods" => ['All']
            ];
        }

        $explodedAction = explode("\\", $permission);
        $controllerMethod = $explodedAction[count($explodedAction) - 1];
        if (!stristr($controllerMethod, '@')) {
            return [
                "namespace" => implode("\\", $explodedAction),
                "controller" => 'All',
                "method" => 'All',
                "methods" => ['All']
            ];
        } else {
            unset($explodedAction[count($explodedAction) - 1]);
        }

        // If we have a full namespace access (namespace\All)
        if ($explodedAction[count($explodedAction) - 1] == 'All') {
            return [
                "namespace" => implode("\\", $explodedAction),
                "controller" => 'All',
                "method" => 'All',
                "methods" => ['All']
            ];
        }

        list($controller, $method) = explode("@", $controllerMethod);
        $methods = explode(";", $method);

        return [
            "namespace" => implode("\\", $explodedAction),
            "controller" => $controller,
            "method" => $method,
            "methods" => $methods
        ];
    }
}