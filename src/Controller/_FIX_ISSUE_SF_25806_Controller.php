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
     * Disable annotation Security to try the denyAccessUnlessGranted and identify if there is a conflict between SensioFrameworkExtraBundle and handlers access_denied_handler or failure_handler
     * @ _Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route(
     *     "/demo/login/json/issue/sf-25806",
     *     defaults={"_format": "json"},
     *     )
     * @Method({"GET"})
     */
    public function reproductionForIssueSF28506()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'Unable to access this page!');

        return new JsonResponse("data");
    }
}
