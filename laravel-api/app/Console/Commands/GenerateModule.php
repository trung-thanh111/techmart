<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Carbon;

class GenerateModule extends Command
{
    /*
    * example: php artisan generate:module User --namespace=User
    * module là số ít, namespace là thư mục chứa module
    */
    protected $signature = 'generate:module {name} {--namespace=: The namespace for the Generate Module Structure}';
    protected $description = 'Generate a complate CRUD structure for a module';

    protected $name;
    protected $namespace;
    protected $startTime;

    /**
     * Phương thức này được khởi tạo để thiết lập các tham số cho module sử dụng mà không cần tuyền ở các thamk số trong hàm
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
    // Artisan sẽ tự động gọi hàm này khi chạy lệnh -> bắt buộc cần method handle()
    public function handle()
    {
        $name = $this->argument('name');
        $namespace = $this->option('namespace') ?: 'App';
        $this->newLine();
        // thông báo khởi tạo module với tên và namespace trên terminal
        $this->info("Generating module {$name} in namespace: {$namespace}");
        $this->newLine();
        $startTime = microtime(true); // tgian bắt đầu chạy

        $this->setParameters($name, $namespace)
            ->createController()
            ->createModel()
            ->createRequest()
            ->createRepository()
            ->createServiceInterface()
            ->createService()
            ->createResource()
            ->registerServiceProvider()
            ->createRoute()
            ->createMigration()
            // ->logGenerateModule()
        ;

        $executionTime =  round((microtime(true) - $startTime) * 1000, 2); // tgian chạy
        // thông báo tạo thành công module trên terminal
        $this->info("Module {$name} generated successfully in {$executionTime}ms.");
    }

    // đọc file stub và trả về nội dung của file
    public function getStub($stubName)
    {
        $stubPath = resource_path("stubs/{$stubName}.stub");
        if (!File::exists($stubPath)) {
            $this->error("Stub file {$stubName} not found.");
            return '';
        }
        return File::get($stubPath);
    }

    protected function createController(): self
    {
        $folder = app_path("Http/Controllers/Api/V1/{$this->namespace}");
        // bảo đảm folder dã tồn tại nếu filder chưa có thì sẽ tự động tạo ra
        File::ensureDirectoryExists($folder);

        $stub = $this->getStub('controller');
        $content = str_replace(
            ['{{namespace}}', '{{name}}'],
            [$this->namespace, $this->name],
            $stub
        );
        // kiếm tra xem file đã tồn tại hay chưa
        if (!File::exists("{$folder}/{$this->name}Controller.php")) {
            // nếu chưa có thì sẽ tạo mới với content đã được thay thế
            File::put("{$folder}/{$this->name}Controller.php", $content);
        }
        return $this;
    }

    protected function createModel(): self
    {
        $tableName = camel_case_to_snake_case($this->name);
        $stub = $this->getStub('model');
        $content = str_replace(
            ['{{name}}', '{{tableName}}'],
            [$this->name, $tableName],
            $stub
        );
        if (!File::exists(app_path("Models/{$this->name}.php"))) {
            File::put(app_path("Models/{$this->name}.php"), $content);
        }
        return $this;
    }

    protected function createRequest(): self
    {
        $folder = app_path("Http/Requests/{$this->namespace}/{$this->name}");
        File::ensureDirectoryExists($folder);
        $requests = [
            'StoreRequest' => 'store-request',
            'UpdateRequest' => 'update-request',
            'BulkStoreRequest' => 'bulkstore-request',
            'BulkUpdateRequest' => 'bulkupdate-request',
        ];
        foreach ($requests as $key => $stubName) {
            $stub = $this->getStub($stubName);
            $content = str_replace(
                ['{{namespace}}', '{{name}}'],
                [$this->namespace, $this->name],
                $stub
            );
            if (!File::exists("{$folder}/{$key}.php")) {
                File::put("{$folder}/{$key}.php", $content);
            }
        }

        return $this;
    }

    protected function createRepository(): self
    {
        $folder = app_path("Repositories/{$this->namespace}");
        File::ensureDirectoryExists($folder);
        $stub = $this->getStub('repository');
        $content = str_replace(
            ['{{namespace}}', '{{name}}'],
            [$this->namespace, $this->name],
            $stub
        );
        if (!File::exists("{$folder}/{$this->name}Repository.php")) {
            File::put("{$folder}/{$this->name}Repository.php", $content);
        }
        return $this;
    }

    protected function createServiceInterface(): self
    {
        $folder = app_path("Services/Interfaces/{$this->namespace}");
        File::ensureDirectoryExists($folder);
        $stub = $this->getStub('service-interface');
        $content = str_replace(
            ['{{namespace}}', '{{name}}'],
            [$this->namespace, $this->name],
            $stub
        );
        if (!File::exists("{$folder}/{$this->name}ServiceInterface.php")) {
            File::put("{$folder}/{$this->name}ServiceInterface.php", $content);
        }
        return $this;
    }

    protected function createService(): self
    {
        $folder = app_path("Services/Impl/{$this->namespace}");
        File::ensureDirectoryExists($folder);
        $cacheKeyPrefix = camel_case_to_snake_case($this->name);
        $stub = $this->getStub('service');
        $content = str_replace(
            ['{{namespace}}', '{{name}}', '{{cacheKeyPrefix}}'],
            [$this->namespace, $this->name, $cacheKeyPrefix],
            $stub
        );
        if (!File::exists("{$folder}/{$this->name}Service.php")) {
            File::put("{$folder}/{$this->name}Service.php", $content);
        }
        return $this;
    }

    protected function createResource(): self
    {
        $folder = app_path("Http/Resources/{$this->namespace}");
        File::ensureDirectoryExists($folder);
        $stub = $this->getStub('resource');
        $content = str_replace(
            ['{{namespace}}', '{{name}}'],
            [$this->namespace, $this->name],
            $stub
        );
        if (!File::exists("{$folder}/{$this->name}Resource.php")) {
            File::put("{$folder}/{$this->name}Resource.php", $content);
        }
        return $this;
    }

    protected function registerServiceProvider(): self
    {
        $folder = app_path('Providers/AppServiceProvider.php');
        if (!File::exists($folder)) return $this;
        $content = File::get($folder);

        $interface = "{$this->name}ServiceInterface";
        $implementation = "{$this->name}Service";

        if (strpos($content, $interface) !== false) return $this;

        $usePattern = "/use Illuminate\\\\Support\\\\ServiceProvider;/";
        $useStatements = "use Illuminate\\Support\\ServiceProvider;\nuse App\\Services\\Interfaces\\{$this->namespace}\\{$interface};\nuse App\\Services\\Impl\\{$this->namespace}\\{$implementation};";
        $content = preg_replace($usePattern, $useStatements, $content);

        $bindingPattern = "/protected array \\\$bindServices = \[(.*?)\];/s";
        $newBinding = "$1    {$interface}::class => {$implementation}::class,";
        $content = preg_replace($bindingPattern, "protected array \$bindServices = [$newBinding\n    ];", $content);

        File::put($folder, $content);
        return $this;
    }

    protected function createRoute(): self
    {
        $folder = base_path('routes/api.php');
        if (!File::exists($folder)) return $this;
        $content = File::get($folder);

        $prefix = camel_case_to_snake_case($this->name);

        $controllerImport = "use App\\Http\\Controllers\\Api\\V1\\{$this->namespace}\\{$this->name}Controller;";
        if (strpos($content, $controllerImport) === false) {
            $usePattern = "/use Illuminate\\\\Support\\\\Facades\\\\Route;/";
            $useStatements = "use Illuminate\\Support\\Facades\\Route;\n{$controllerImport}";
            $content = preg_replace($usePattern, $useStatements, $content);
        }

        $patternRoute = '/\/\/ generate route/';
        $createRoute = <<<EOT
// This is route {$this->name}
        Route::group(['prefix' => '{$prefix}s'], function() {
            Route::post('bulkStore', [{$this->name}Controller::class, 'bulkStore']);
            Route::put('bulkUpdate', [{$this->name}Controller::class, 'bulkUpdate']);
            Route::patch('bulkUpdate', [{$this->name}Controller::class, 'bulkUpdate']);
            Route::delete('bulkDelete', [{$this->name}Controller::class, 'bulkDelete']);
            Route::post('{id}/attach', [{$this->name}Controller::class, 'attach']);
            Route::delete('{id}/detach', [{$this->name}Controller::class, 'detach']);
        });
        Route::resource('{$prefix}s', {$this->name}Controller::class)->except(\$routeExcept);

        // generate route
EOT;

        $content = preg_replace($patternRoute, $createRoute, $content);

        File::put($folder, $content);
        return $this;
    }
    protected function createMigration(): self{
        $folder = database_path('migrations');
        File::ensureDirectoryExists($folder);

        $tableName = camel_case_to_snake_case($this->name);
        $time = Carbon::now()->format('Y_m_d_His');
        $stub = $this->getStub('migration');
        $content = str_replace(
            ['{{tableName}}'],
            [$tableName],
            $stub
        );
        $filename = "{$time}_create_{$tableName}_table";

        if (!File::exists("{$folder}/{$filename}.php")) {
            File::put("{$folder}/{$filename}.php", $content);
        }
        return $this;
    }



}
