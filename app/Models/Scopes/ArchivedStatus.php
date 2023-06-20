<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ArchivedStatus implements Scope
{
    private array $extensions = ['Archived', 'NotArchived', 'WithArchived'];

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where($model->getQualifiedStatusColumn(), '!=', $model->getArchivedStatusName());
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param Builder $builder
     * @return void
     */
    public function extend(Builder $builder): void
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }
    }

    /**
     * Add the archived extension to the builder.
     *
     * @param Builder $builder
     * @return void
     */
    protected function addArchived(Builder $builder): void
    {
        $builder->macro('archived', function (Builder $builder) {
            $model = $builder->getModel();
            return $builder->withoutGlobalScope($this)->where($model->getQualifiedStatusColumn(), $model->getArchivedStatusName());
        });
    }

    /**
     * @param Builder $builder
     * @return void
     */
    protected function addNotArchived(Builder $builder): void
    {
        $builder->macro('notArchived', function (Builder $builder) {
            $model = $builder->getModel();
            return $builder->withoutGlobalScope($this)->where($model->getQualifiedStatusColumn(), '!=', $model->getArchivedStatusName());
        });
    }

    /**
     * @param Builder $builder
     * @return void
     */
    protected function addWithArchived(Builder $builder): void
    {
        $builder->macro('withArchived', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });
    }
}
