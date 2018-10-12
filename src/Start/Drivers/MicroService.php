<?php

namespace CrCms\Foundation\Start\Drivers;

use CrCms\Foundation\StartContract;
use Illuminate\Contracts\Container\Container;
use CrCms\Foundation\MicroService\Server\Kernel as HttpKernelContract;
use Illuminate\Http\Request;
use Carbon\Carbon;
use CrCms\Foundation\Swoole\Server;
use Swoole\Async;
use Exception;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use function CrCms\Foundation\App\Helpers\array_merge_recursive_distinct;
use function CrCms\Foundation\App\Helpers\framework_config_path;
use CrCms\Foundation\Swoole\Server\ProcessManager;

/**
 * Class MicroService
 * @package CrCms\Foundation\Start\Drivers
 */
class MicroService extends Server\AbstractStart implements StartContract
{
}