<?php

namespace App\CoreLogic\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @extends Model
 */
abstract class BaseRepository implements Repository
{
    public Model $model;

    public string $modelName;

    public function __construct()
    {
        if (property_exists($this, 'modelName')) {
            $this->model = new $this->modelName();
        }
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->model->{$key};
    }

    /**
     * @return Repository
     */
    public static function getInstance(): Repository
    {
        return new static();
    }

    /**
     * @param Model $model
     * @return $this
     */
    public function setModel(Model $model): static
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        return $this->model->{$method}(...$args);
    }
}
