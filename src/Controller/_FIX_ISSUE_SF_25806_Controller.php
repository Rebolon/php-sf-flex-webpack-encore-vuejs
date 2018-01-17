<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * This class is just here to reproduce the behavior described in this issue:
 * https://github.com/symfony/symfony/issues/25806
 */
class _FIX_ISSUE_SF_25806_Controller extends Controller
{
    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route(
     *     "/demo/login/json/issue/sf-25806",
     *     )
     * @Method({"GET"})
     */
    public function reproductionForIssueSF28506()
    {
        return new JsonResponse("data");
    }
}
