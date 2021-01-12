<?php
namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;

/**
 * Class Ping
 * @ApiResource(
 *     attributes={"security"="is_granted('ROLE_USER')", "status_code"=403},
 *     itemOperations={
 *         "get"={"method"="GET", "securityMessage"="Only authenticated users can access this endpoint."}
 *     },
 *     collectionOperations={
 *          "get"={"method"="GET", "securityMessage"="Only authenticated users can access this endpoint."}
 *     }
 * )
 * @package App\Entity\PingSecured
 */
class PingSecured extends Ping
{
}
