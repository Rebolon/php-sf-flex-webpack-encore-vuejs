<?php
namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;

/**
 * Class Ping
 * @ApiResource(
 *     attributes={"access_control"="is_granted('ROLE_USER')", "status_code"=403},
 *     itemOperations={
 *         "get"={"method"="GET", "access_control_message"="Only authenticated users can access this endpoint."}
 *     },
 *     collectionOperations={
 *          "get"={"method"="GET", "access_control_message"="Only authenticated users can access this endpoint."}
 *     }
 * )
 * @package App\Entity\PingSecured
 */
class PingSecured extends Ping
{
}
