<?php
namespace App\Console\Commands;

class RollbackModule {



    protected $name;
    protected $namespace;
    protected $startTime;

    /**
     * Phương thức này được khởi tạo để thiết lập các tham số cho module sử dụng mà không cần tuyền ở các tham số trong hàm
     * chỉ cần gọi hàm này và truyền tham số cần sử dụng vào là các phương thức bên chaining sẽ tự động nhận được các tham số này
     * @param [type] $name
     * @param [type] $namespace
     * @return self
     */
    protected function setParameters($name, $namespace): self
    {
        $this->name = $name;
        $this->namespace = $namespace;
        return $this;
    }



}
