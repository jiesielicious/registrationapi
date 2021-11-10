<?php
namespace App\Http\Services;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Models\User;

abstract class BaseService 
{
    protected $currentUser;
    protected $searchTerm = '';
    protected $sortBy = 'id';
    protected $sortDir = 'asc';
    protected $request;
    protected $perPage = 700;
    protected $currentPage = 1;
    protected $excludeBeforeId = -1;

    /**
     * Main repository model
     *
     * @var $model
     */
    protected $model;

    /**
     * Create a new repository instance.
     *
     * @param object $model Main repository model
     */
    public function __construct($model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
        $this->searchTerm = isset($request->searchQuery) && !empty($request->searchQuery) ? strtoupper($request->searchQuery) : strtoupper($this->searchTerm);
        $this->sortBy = isset($request->order_by) ? $request->order_by : $this->sortBy;
        $this->sortDir = isset($request->order_dir) ? $request->order_dir : $this->sortDir;
        $this->perPage = isset($request->per_page) ? $request->per_page : $this->perPage;
        $this->currentPage = isset($request->current_page) && $request->current_page != 0 ? $request->current_page : $this->currentPage;
        $this->excludeBeforeId = isset($request->exclude) && is_numeric($request->exclude) ? $request->exclude : $this->excludeBeforeId;
        Paginator::currentPageResolver(function () {return $this->currentPage;});
    }
    public function all()
    {
        return  $this->model->all();
    }
    public function orderBy($column)
    {
        return  $this->model->orderBy($column)->get();
    }
    public function create(array $request)
    {
        return  $this->model->create($request);
    }
    public function update($request, $id)
    {
        $model = $this->model->findOrFail($id);
        return  $model->fill($request)->save();
    }
    public function findOrFail($id)
    {
        return  $this->model->findOrFail($id);
    }
    public function insert(array $data)
    {
        return  $this->model->insert($data);
    }
    public function select($value, $id)
    {
        return  $this->model->select($value)->where('id', $id)->first();
    }
    public function insertGetId(array $data)
    {
        $this->model->insert($data);
        return  $this->model->select('id')->latest()->first();
    }
    public function find($id)
    {
        return  $this->model->find($id);
    }
    public function whereIn(array $id)
    {
        return  $this->model->whereIn('id', $id)->get();
    }
    public function getTheLastId()
    {
        $model = $this->model->latest()->first();
        return  $model->id;
    }
    public function clearCache()
    {
        return  $this->model->forget($this->allCacheName);
    }
    public function getNotification()
    {
        return $this->model->whereHas('notification', function($query) {
            $query->where('status', 'unseen');
        })->get();
    }
    public function getModel()
    {
        return $this->model;
    }
    public function getClassName()
    {
        return get_class($this->model);
    }

    public function withRelated(array $related)
    {
        return $this->model->with($related)->get();
    }

    public function delete($id)
    {
        return $this->model->delete($id);
    }

    public function paginate($count = 15)
    {
        return $this->model->paginate($count);
    }
}