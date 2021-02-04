<?php

declare(strict_types=1);

/**
 * @author   Pixel Group
 * @license  MIT
 *
 * @see      https://github.com/pixel-group/google-play-scraper
 */

namespace Pixelgroup\GPlay\Scraper;

use Pixelgroup\GPlay\Model\GoogleImage;
use Pixelgroup\GPlay\Model\Permission;
use Pixelgroup\HttpClient\ResponseHandlerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
class PermissionScraper implements ResponseHandlerInterface
{
    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return Permission[]
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response)
    {
        $contents = substr($response->getBody()->getContents(), 5);
        $json = \GuzzleHttp\json_decode($contents, true);
        $data = \GuzzleHttp\json_decode($json[0][2], true);

        $permissionMapFn = static function (array $v): string {
            return (string) $v[1];
        };

        $permissions = [];

        foreach (\array_slice($data, 0, 2) as $items) {
            if ($items === null) {
                continue;
            }

            foreach ($items as $values) {
                $permissionName = $values[0];
                $permissions[$permissionName] = [
                    'name' => $permissionName,
                    'icon' => new GoogleImage($values[1][3][2]),
                    'permissions' => array_map($permissionMapFn, $values[2]),
                ];
            }
        }

        if (isset($data[2])) {
            end($permissions);
            $lastKey = key($permissions);
            $permissions[$lastKey]['permissions'] = array_merge(
                array_map($permissionMapFn, $data[2]),
                $permissions[$lastKey]['permissions']
            );
        }

        return array_map(
            static function (array $data) {
                return new Permission($data['name'], $data['icon'], $data['permissions']);
            },
            $permissions
        );
    }
}
