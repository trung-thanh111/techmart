<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait HasCache
{

    /**
     * cache lưu dữ liệu bằng cặp key-value
     * vd: user_catalogues:single:1 // lưu dạng 1 bản ghi
     * vd2: user_catalogues:collection:sfsdfsdhf2234cx // lưu dạng nhiều bản ghi
     */

    protected $cacheKeyPrefix = ''; // tiền tố của key (có thể là tên bảng và định nghĩa ở lớp con)
    protected $cacheTTL = 3600; // thời gian sống của cache

    /**
     * random tgian sống của của cache để các data không bị hết hạn cũng lút => bão cache
     *
     * @return integer
     */
    protected function getRandomTTL(): int
    {
        return rand(3600, 7200) * 60; // thời gian sống của cache
    }

    /**
     * set tgian sống cho cache bằng cách random khi có thay đổi or lấy tgian sống mặc định
     *
     * @param integer|null $ttl
     * @return self
     */
    protected function setCacheTTL(?int $ttl = null): void
    {
        $this->cacheTTL = $ttl ?? $this->getRandomTTL();
    }

    /**
     * tự động tạo ra key cho cache để lưu dữ liệu
     *
     * @param integer|null $id
     * @return string
     */
    protected function generateSingleCacheKey(?int $id = null): string
    {
        if (!$id) {
            $id = $this->model ? $this->model->getKey() : request()->route(substr($this->cacheKeyPrefix, 0, -1));
        }
        return "{$this->cacheKeyPrefix}:single:{$id}";
    }

    protected function cacheSingleRecord(?int $id = null): self
    {
        $cacheKey = $this->generateSingleCacheKey($id);
        $cacheData = $this->getResult();
        Cache::put($cacheKey, $cacheData, $this->cacheTTL); // lưu cache

        return $this;
    }

    protected function clearCacheById(?int $id = null): self
    {
        $cacheKey = $this->generateSingleCacheKey($id);
        Cache::forget($cacheKey); // xóa cache
        return $this;
    }

    protected function generateCollectionCacheKey(array $params = [])
    {
        krsort($params); // sắp xếp giảm dần để tránh việc trùng lập data khi vị trí các params thay dổi
        $hash = md5(serialize($params)); // mã hóa các tham số thành dạng hash
        return "{$this->cacheKeyPrefix}:collection:{$hash}";
    }

    protected function clearCacheByTags(): self
    {
        Cache::tags($this->cacheTags)->flush(); // xóa cache theo tag
        return $this;
    }
}
