<?php declare(strict_types=1);

namespace Hanaboso\Utils\Traits;

use Hanaboso\Utils\String\Json;
use Hanaboso\Utils\System\ControllerUtils;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Trait ControllerTrait
 *
 * @package Hanaboso\Utils\Traits
 */
trait ControllerTrait
{

    use LoggerTrait;

    /**
     * @param mixed   $data
     * @param int     $code
     * @param mixed[] $headers
     *
     * @return Response
     */
    protected function getResponse(mixed $data, int $code = 200, array $headers = []): Response
    {
        if (!is_string($data)) {
            $data = Json::encode($data);
        }

        return new Response($data, $code, $headers);
    }

    /**
     * @param Throwable $e
     * @param int       $code
     * @param string    $status
     * @param mixed[]   $headers
     *
     * @return Response
     */
    protected function getErrorResponse(
        Throwable $e,
        int $code = 500,
        string $status = ControllerUtils::INTERNAL_SERVER_ERROR,
        array $headers = []
    ): Response
    {
        $msg     = ControllerUtils::createExceptionData($e, $status);
        $headers = ControllerUtils::createHeaders($headers, $e);

        $this->logger->error($msg, ['exception' => $e]);

        return $this->getResponse($msg, $code, $headers);
    }

}
