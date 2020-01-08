<?php declare(strict_types=1);

namespace UtilsTests\Unit\Traits;

use Hanaboso\Utils\System\ControllerUtils;
use Hanaboso\Utils\Traits\ControllerTrait;
use Hanaboso\Utils\Traits\UrlBuilderTrait;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class TestTraits
 *
 * @package UtilsTests\Unit\Traits
 */
final class TestTraits
{

    use ControllerTrait;
    use UrlBuilderTrait;

    /**
     * @param string $host
     *
     * @return TestTraits
     */
    public function setHost(string $host): TestTraits
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @param mixed   $data
     * @param int     $code
     * @param mixed[] $headers
     *
     * @return Response
     */
    public function getResponseForTest($data, int $code = 200, array $headers = []): Response
    {
        return $this->getResponse($data, $code, $headers);
    }

    /**
     * @param Throwable $e
     * @param int       $code
     * @param string    $status
     * @param mixed[]   $headers
     *
     * @return Response
     */
    public function getErrorResponseForTest(
        Throwable $e,
        int $code = 500,
        string $status = ControllerUtils::INTERNAL_SERVER_ERROR,
        array $headers = []
    ): Response
    {
        return $this->getErrorResponse($e, $code, $status, $headers);
    }

    /**
     * @param string $part
     * @param string ...$nestedPart
     *
     * @return string
     */
    public function getUrlForTest(string $part, string ...$nestedPart): string
    {
        return $this->getUrl($part, ...$nestedPart);
    }

}