<?php
namespace App\Services\Impl\User;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\User\UserCatalogueResource;
use App\Repositories\User\UserCatalogueRepository;
use App\Services\Interfaces\User\UserCatalogueServiceInterface;

class UserCatalogueService extends BaseService implements UserCatalogueServiceInterface
{

    protected $repository;

    protected $complexFilter = ['id'];

    protected $with = [];
    protected $relationFilter = ['users', 'permissions'];

    protected $cacheKeyPrefix = 'user_catalogues';
    protected $cacheTags = ['user_catalogues:collection'];


    public function __construct(
        UserCatalogueRepository $repository
    ) {
        $this->repository = $repository;
    }

    protected function getRepository()
    {
        return $this->repository;
    }

    /**
     * nhiệm vụ bắt data từ client post lên (dạng thô, chuỗi, file, ....)
     *
     */
    protected function prepareModelData(Request $request): self
    {

        return $this->initializeBasicModelData($request);
    }

    protected function initializeBasicModelData(Request $request): self
    {
        $fillable = $this->repository->getFillable();
        $this->modelData = $request->only($fillable);
        return $this;
    }

    // protected function uploadImage(Request $request): self{
    //     return $this;
    // }

    protected function getResource(Model $resource)
    {
        return new UserCatalogueResource($resource);
    }

    /**
     * Lấy danh sách permissions của user catalogue và tất cả permissions có sẵn
     */
    public function getPermissions(int $id): array
    {
        $userCatalogue = $this->repository->findById($id, ['permissions']);
        if (!$userCatalogue) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('User catalogue not found');
        }

        // Lấy tất cả permissions, nhóm theo module
        $allPermissions = \App\Models\Permission::where('publish', 2)
            ->orderBy('module')
            ->orderBy('value')
            ->get();

        // Lấy permissions đã được gán cho user catalogue
        $assignedPermissionIds = $userCatalogue->permissions->pluck('id')->toArray();

        // Nhóm permissions theo module
        $permissionsByModule = [];
        foreach ($allPermissions as $permission) {
            $module = $permission->module ?? 'other';
            if (!isset($permissionsByModule[$module])) {
                $permissionsByModule[$module] = [];
            }
            $permissionsByModule[$module][] = [
                'id' => $permission->id,
                'name' => $permission->name,
                'title' => $permission->title,
                'value' => $permission->value,
                'module' => $permission->module,
                'isAssigned' => in_array($permission->id, $assignedPermissionIds),
            ];
        }

        return [
            'userCatalogue' => new UserCatalogueResource($userCatalogue),
            'permissionsByModule' => $permissionsByModule,
            'assignedPermissionIds' => $assignedPermissionIds,
        ];
    }
}
