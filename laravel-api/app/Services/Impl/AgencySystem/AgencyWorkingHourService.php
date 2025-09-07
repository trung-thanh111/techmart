<?php
namespace App\Services\Impl\AgencySystem;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\AgencySystem\AgencyWorkingHourRepository;
use App\Http\Resources\AgencySystem\AgencyWorkingHourResource;
use App\Services\Interfaces\AgencySystem\AgencyWorkingHourServiceInterface;

class AgencyWorkingHourService extends BaseService implements AgencyWorkingHourServiceInterface {

protected $repository;

protected $complexFilter = ['id'];

protected $with = [];
protected $relationFilter = [];

protected $cacheKeyPrefix = 'agency_working_hours';
protected $cacheTags = ['agency_working_hours:collection'];

protected $useUpload = false;

    public function __construct(
        AgencyWorkingHourRepository $repository
    ) {
        $this->repository = $repository;
    }

    protected function getRepository(){
        return $this->repository;
    }

    /**
     * nhiệm vụ bắt data từ client post lên (dạng thô, chuỗi, file, ....)
     *
     */
    protected function prepareModelData(Request $request): self {

        return $this->initializeBasicModelData($request);
    }

    protected function initializeBasicModelData(Request $request): self {
        $fillable = $this->repository->getFillable();
        $this->modelData = $request->only($fillable);
        return $this;
    }

    protected function getResource(Model $resource){
        return new AgencyWorkingHourResource($resource);
    }

}
