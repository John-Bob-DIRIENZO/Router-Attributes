<?php

namespace App\Framework\Route;

use App\Framework\DIC\DIC;
use App\Framework\Entity\Dependency;
use App\Framework\Traits\DirectoryParser;
use InvalidArgumentException;
use ReflectionClass;

class Router
{
    /**
     * @var Route[]
     */
    private array $routes = [];

    use DirectoryParser;

    /**
     * @throws \ReflectionException
     */
    public function getRoutesFromAttributes(string $controllerDirectory): self
    {
        if (!is_dir($controllerDirectory)) {
            throw new InvalidArgumentException('Chemin non valide');
        }

        $controllers = $this->getClassesFromDirectory($controllerDirectory);
        foreach ($controllers as $controller) {
            foreach (($reflection = new ReflectionClass($controller))->getMethods() as $method) {
                foreach ($method->getAttributes() as $attribute) {
                    $route = $attribute->newInstance();
                    /** @var $route Route */
                    $route->setController($reflection->getName())
                        ->setAction($method->getName());

                    if (!$route->getName()) {
                        $route->setName("app_" . strtolower($reflection->getShortName() . "_" . $method->getName()));
                    }
                    $this->routes[] = $route;
                }
            }
        }

        return $this;
    }

    /**
     * @throws \ReflectionException
     */
    public function run()
    {
        $uri = '/' . trim(explode('?', $_SERVER["REQUEST_URI"])[0], '/');

        foreach ($this->routes as $route) {
            if ($route->match($uri)) {
                if (!in_array($_SERVER['REQUEST_METHOD'], $route->getMethods())) {
                    continue;
                }

                $controllerClass = $route->getController();
                $controllerActionName = $route->getAction();

                foreach ((new ReflectionClass($controllerClass))->getMethods() as $method) {
                    if ($method->getName() === $controllerActionName) {
                        foreach ($method->getParameters() as $parameter) {
                            $dependencies[] = (new Dependency())
                                ->setName($parameter->getName())
                                ->setType($parameter->getType()->getName())
                                ->setFromURL($parameter->getType()->isBuiltin());
                        }
                    }
                }

                $params = $route->mergeParams($uri);

                foreach ($dependencies ?? [] as $dependency) {
                    if ($dependency->isFromURL()) {
                        $finalParams[$dependency->getName()] = $params[$dependency->getName()];
                    } else {
                        $finalParams[$dependency->getName()] = DIC::autowire($dependency->getType());
                    }
                }
                return new $controllerClass($controllerActionName, $finalParams ?? []);
            }
        }
        return new ErrorController('noRoute');
    }
}
