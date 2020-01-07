<?php declare(strict_types=1);

namespace Hanaboso\Utils\Traits;

use Hanaboso\Utils\String\Json;
use Hanaboso\Utils\System\ControllerUtils;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Trait ControllerTrait
 *
 * @package Hanaboso\Utils\Traits
 */
trait ControllerTrait
{

    /**
     * @var LoggerInterface|null
     */
    protected ?LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param mixed   $data
     * @param int     $code
     * @param mixed[] $headers
     *
     * @return Response
     */
    protected function getResponse($data, int $code = 200, array $headers = []): Response
    {
        if (!is_string($data)) {
            $data = Json::encode($data);
        } else {
            if (!json_decode($data)) {
                $data = Json::encode($data);
            }
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

        if ($this->logger) {
            $this->logger->error($msg, ['exception' => $e]);
        }

        return $this->getResponse($msg, $code, $headers);
    }

}