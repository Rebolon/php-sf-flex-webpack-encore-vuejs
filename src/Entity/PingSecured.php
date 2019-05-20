<?php
namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;

/**
 * Class PingSecured
 * @ApiResource(
 *     attributes={"access_control"="is_granted('ROLE_USER', 'ROLE_API_READ')"},
 *     itemOperations={
 *         "get"={"access_control"="is_granted('ROLE_USER', 'ROLE_API_READ')", "access_control_message"="Only authenticated users can access this endpoint."}
 *     },
 *     collectionOperations={
 *         "get"={"access_control"="is_granted('ROLE_USER', 'ROLE_API_READ')", "access_control_message"="Only authenticated users can access this endpoint."}
 *     }
 * )
 * @package App\Entity\PingSecured
 */
class PingSecured extends Ping
{
}
