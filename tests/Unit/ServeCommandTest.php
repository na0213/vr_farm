<?php

namespace Tests\Unit;

use App\Console\Commands\ServeCommand;
use Illuminate\Contracts\Console\Kernel;
use ReflectionMethod;
use Symfony\Component\Console\Input\ArrayInput;
use Tests\TestCase;

/**
 * php artisan serve は、動画を受け取れるようにアップロードの上限を上げて起動する。
 */
class ServeCommandTest extends TestCase
{
    public function test_serve_command_is_replaced_and_passes_upload_limits_to_php(): void
    {
        $command = $this->app->make(Kernel::class)->all()['serve'];
        $this->assertInstanceOf(ServeCommand::class, $command);

        $command->setLaravel($this->app);
        $command->setInput(new ArrayInput(['--host' => '127.0.0.1', '--port' => '8123'], $command->getDefinition()));
        $args = (new ReflectionMethod($command, 'serverCommand'))->invoke($command);

        $this->assertSame(['-d', 'upload_max_filesize=200M', '-d', 'post_max_size=210M', '-S', '127.0.0.1:8123'], array_slice($args, 1, 6));
    }
}
