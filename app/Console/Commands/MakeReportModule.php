<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeReportModule extends Command
{
    protected $signature = 'make:report {name}';

    protected $description = 'Create Report Module Structure';

    public function handle()
    {
        $name = ucfirst($this->argument('name'));

        $basePath = app_path("Domains/Reports/{$name}");

        if (File::exists($basePath)) {
            $this->error("Module {$name} already exists!");
            return;
        }

        $folders = [
            'Controllers',
            'UseCases',
            'Services',
            'Repositories',
            'Pdf',
        ];

        foreach ($folders as $folder) {
            File::makeDirectory(
                "{$basePath}/{$folder}",
                0755,
                true
            );
        }

        $this->createController($name, $basePath);
        $this->createUseCase($name, $basePath);
        $this->createService($name, $basePath);
        $this->createRepository($name, $basePath);
        $this->createPdf($name, $basePath);
        $this->createTests($name);


        $this->info("Report module {$name} created successfully!");
    }

    private function createController($name, $path)
    {
        $class = "{$name}ReportController";

        $content = <<<PHP
<?php

namespace App\Domains\Reports\\{$name}\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domains\Reports\\{$name}\UseCases\Generate{$name}ReportUseCase;

class {$class} extends Controller
{
    public function generate(
        Request \$request,
        Generate{$name}ReportUseCase \$useCase
    ) {
        return \$useCase->execute(\$request->all());
    }
}
PHP;

        File::put(
            "{$path}/Controllers/{$class}.php",
            $content
        );
    }

    private function createUseCase($name, $path)
    {
        $class = "Generate{$name}ReportUseCase";

        $content = <<<PHP
<?php

namespace App\Domains\Reports\\{$name}\UseCases;

use App\Domains\Reports\\{$name}\Services\\{$name}ReportService;
use App\Domains\Reports\\{$name}\Pdf\\{$name}ReportPdf;

class {$class}
{
    public function __construct(
        private {$name}ReportService \$service,
        private {$name}ReportPdf \$pdf
    ) {}

    public function execute(array \$data)
    {
        \$processed = \$this->service->process(\$data);

        return \$this->pdf->generate(\$processed);
    }
}
PHP;

        File::put(
            "{$path}/UseCases/{$class}.php",
            $content
        );
    }

    private function createService($name, $path)
    {
        $class = "{$name}ReportService";

        $content = <<<PHP
<?php

namespace App\Domains\Reports\\{$name}\Services;

use App\Domains\Reports\\{$name}\Repositories\\{$name}ReportRepository;

class {$class}
{
    public function __construct(
        private {$name}ReportRepository \$repository
    ) {}

    public function process(array \$data)
    {
        // TODO: Implement business logic

        return \$data;
    }
}
PHP;

        File::put(
            "{$path}/Services/{$class}.php",
            $content
        );
    }

    private function createRepository($name, $path)
    {
        $class = "{$name}ReportRepository";

        $content = <<<PHP
<?php

namespace App\Domains\Reports\\{$name}\Repositories;

class {$class}
{
    // TODO: Implement database logic
}
PHP;

        File::put(
            "{$path}/Repositories/{$class}.php",
            $content
        );
    }

    private function createPdf($name, $path)
    {
        $class = "{$name}ReportPdf";

        $content = <<<PHP
<?php

namespace App\Domains\Reports\\{$name}\Pdf;

use App\Support\Pdf\PdfViewFactory;

class {$class}
{
    public function __construct(
        private readonly PdfViewFactory \$pdfViewFactory
    ) {}

    public function generate(array \$data, ?string \$orientation = null)
    {
        return \$this->pdfViewFactory
            ->loadView(
                'reports.' . strtolower('{$name}') . '.pdf',
                \$data,
                \$orientation
            )
            ->download(strtolower('{$name}') . '-report.pdf');
    }
}
PHP;

        File::put(
            "{$path}/Pdf/{$class}.php",
            $content
        );
    }

    private function createTests($name)
    {
        $testPath = base_path(
            "tests/Unit/Domains/Reports/{$name}"
        );

        if (!File::exists($testPath)) {
            File::makeDirectory($testPath, 0755, true);
        }

        $this->createUseCaseTest($name, $testPath);
        $this->createServiceTest($name, $testPath);
    }
    private function createUseCaseTest($name, $path)
    {
        $class = "Generate{$name}ReportUseCaseTest";

        $content = <<<PHP
    <?php

    namespace Tests\Unit\Domains\Reports\\{$name};

    use Tests\TestCase;
    use Mockery;
    use App\Domains\Reports\\{$name}\UseCases\Generate{$name}ReportUseCase;
    use App\Domains\Reports\\{$name}\Services\\{$name}ReportService;
    use App\Domains\Reports\\{$name}\Pdf\\{$name}ReportPdf;

    class {$class} extends TestCase
    {
        protected function tearDown(): void
        {
            Mockery::close();
            parent::tearDown();
        }

        public function test_execute_generates_report()
        {
            \$service = Mockery::mock({$name}ReportService::class);
            \$pdf = Mockery::mock({$name}ReportPdf::class);

            \$service->shouldReceive('process')
                ->once()
                ->with(['raw'])
                ->andReturn(['processed']);

            \$pdf->shouldReceive('generate')
                ->once()
                ->with(['processed'])
                ->andReturn('PDF_RESULT');

            \$useCase = new Generate{$name}ReportUseCase(
                \$service,
                \$pdf
            );

            \$result = \$useCase->execute(['raw']);

            \$this->assertEquals('PDF_RESULT', \$result);
        }
    }
    PHP;

        File::put("{$path}/{$class}.php", $content);
    }
    private function createServiceTest($name, $path)
    {
        $class = "{$name}ReportServiceTest";

        $content = <<<PHP
    <?php

    namespace Tests\Unit\Domains\Reports\\{$name};

    use Tests\TestCase;
    use Mockery;
    use App\Domains\Reports\\{$name}\Services\\{$name}ReportService;
    use App\Domains\Reports\\{$name}\Repositories\\{$name}ReportRepository;

    class {$class} extends TestCase
    {
        protected function tearDown(): void
        {
            Mockery::close();
            parent::tearDown();
        }

        public function test_process_returns_data()
        {
            \$repo = Mockery::mock({$name}ReportRepository::class);

            \$service = new {$name}ReportService(\$repo);

            \$data = ['amount' => 100];

            \$result = \$service->process(\$data);

            \$this->assertEquals(\$data, \$result);
        }
    }
    PHP;

        File::put("{$path}/{$class}.php", $content);
    }



}
