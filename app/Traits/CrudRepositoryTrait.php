<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

trait CrudRepositoryTrait
{

    private Model $model;
    private array $relations = [];

    private function upsert(Model $model, array $params) : void
    {
        $relations = [];
        foreach ($params as $key => $value) {
            if (in_array($key, $this->relations)) {
                $relations[] = match(get_class($model->$key())) {
                    HasOne::class => [$key, 'updateOrCreate', $value],
                    HasMany::class => [$key, 'updateOrCreate', $value],
                    BelongsTo::class => [$key, 'associate', $value],
                    BelongsToMany::class => [$key, 'sync', collect($value)->pluck('id')],
                    default => null,
                };
            } else {
                $model->$key = $value;
            }
        }
        $model->save();
        foreach (array_filter($relations) as $row) {
            list($relation, $method, $value) = $row;
            $attr = (is_array($value) && array_values($value) === $value) ? $value : [$value];
            call_user_func_array([$model->$relation(), $method], $attr);
        }
    }

    public function create(array $params): int
    {
        $model = new $this->model();
        $this->upsert($model, $params);
        return $model->id;
    }

    public function read(int $id): array
    {
        $record = $this->model::with($this->relations)->find($id);
        return empty($record) ? [] : $record->toArray();
    }

    public function update(int $id, array $params): void
    {
        $record = $this->model::with($this->relations)->find($id);
        if (empty($record) === false) {
            $this->upsert($record, $params);
        }
    }

    public function delete(int $id): void
    {
        $record = $this->model::with($this->relations)->find($id);
        if (empty($record) === false) {
            $record->delete();
        }
    }

    public function restore(int $id): void
    {
        $record = $this->model::with($this->relations)->onlyTrashed()->find($id);
        if (empty($record) === false) {
            $record->restore();
        }
    }

    public function forceDelete(int $id): void
    {
        $record = $this->model::with($this->relations)->withTrashed()->find($id);
        if (empty($record) === false) {
            $record->forceDelete();
        }
    }

    private function createFinder(array $params, ?array $options = []) : Builder
    {
        $builder = $this->model::with($this->relations)->where($params);
        foreach ($options as $key => $value) {
            call_user_func_array([$builder, $key], (array)$value);
        }
        return $builder;
    }

    public function find(array $params, ?array $options = []): array
    {        
        $builder = $this->createFinder($params, $options);
        $result =$builder->get();
        return empty($result) ? [] : $result->toArray();
    }

    public function findOne(array $params, ?array $options = []): array
    {
        $builder = $this->createFinder($params, $options);
        $result = $builder->get()->first();
        return empty($result) ? [] : $result->toArray();
    }

    public function findByPage(int $page, array $params, ?array $options = []): LengthAwarePaginator
    {
        $builder = $this->createFinder($params, $options);
        return $builder->paginate($page);
    }

}
