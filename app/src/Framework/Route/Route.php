<?php

namespace App\Framework\Route;

use App\Framework\Traits\Hydrator;
use Attribute;

#[Attribute]
class Route
{
    private ?string $name;
    private ?string $path;
    private ?string $controller;
    private ?string $action;
    private array $params = [];
    private array $methods = ["GET", "POST", "PUT", "PATCH", "DELETE"];

    use Hydrator;

    public function __construct(string $path, ?string $name = null, ?array $methods = null)
    {
        $this->setPath($path);
        $this->setName($name);
        if ($methods) {
            $this->methods = $methods;
        }
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return Route
     */
    public function setName(?string $name): Route
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string|null $path
     * @return Route
     */
    public function setPath(?string $path): Route
    {
        preg_match_all('/{(\w+)}/', $path, $match);
        $this->params = $match[1];

        $this->path = preg_replace('/{(\w+)}/', '([^/]+)', str_replace('/', '\/', $path));
        return $this;
    }

    /**
     * @return string|null
     */
    public function getController(): ?string
    {
        return $this->controller;
    }

    /**
     * @param string|null $controller
     * @return Route
     */
    public function setController(?string $controller): Route
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAction(): ?string
    {
        return $this->action;
    }

    /**
     * @param string|null $action
     * @return Route
     */
    public function setAction(?string $action): Route
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     * @return Route
     */
    public function setParams(array $params): Route
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param array $methods
     * @return Route
     */
    public function setMethods(array $methods): Route
    {
        $this->methods = $methods;
        return $this;
    }

    /**
     * Returns an array with the keys passed in the YAML and the values from the URI
     * @param $path
     * @return array
     */
    public function mergeParams($path): array
    {
        preg_match("#$this->path#", $path, $matches);
        array_shift($matches);
        return array_combine($this->params, $matches);
    }

    public function match(string $path): bool
    {
        return (bool)preg_match("#^($this->path)$#", $path);
    }
}
