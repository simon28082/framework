<?php

namespace CrCms\Foundation\MicroService\Contracts;

use Exception;

/**
 * Interface ExceptionHandlerContract
 * @package CrCms\Foundation\MicroService\Contracts
 */
interface ExceptionHandlerContract
{
    public function report(Exception $e);

    /**
     * Render an exception into an micro service response.
     *
     * @param  ServiceContract  $service
     * @param  \Exception  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render(ServiceContract $service, Exception $e);

    /**
     * Render an exception to the console.
     *
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @param  \Exception  $e
     * @return void
     */
    public function renderForConsole($output, Exception $e);
}