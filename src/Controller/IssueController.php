<?php
/**
 * Created by PhpStorm.
 * User: liutri
 * Date: 2018-03-06
 * Time: 12:11
 */

namespace App\Controller;

use FOS\RestBundle\Controller\FOSRestController as Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\ViewHandlerInterface;

use App\Service\IssueService;
use Symfony\Component\HttpFoundation\Request;


class IssueController extends Controller
{

    /**
     * @Rest\Get("/api/issues/{state}" , name="issues_list")
     *
     * @param IssueService $issueService
     * @param ViewHandlerInterface $viewHandler
     * @param Request $request
     * @param string $state
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function issuesList(
        IssueService $issueService,
        ViewHandlerInterface $viewHandler,
        Request $request,
        string $state
    ) {

        $token = $request->headers->get('Authorization');
        if ($token) {
            $issueService->setAuthorization($token);
        }

        $issues = $issueService->getIssues($state);
        $total = $issueService->getIssuesCount($state);

        $view = $this->view([
            'total' => $total,
            'issues' => $issues['items'],
        ]);

        return $viewHandler->handle($view);

    }

    /**
     * @Rest\Get("/api/issues/{repo}/{user}/{number}" , name="issue")
     *
     * @param IssueService $issueService
     * @param ViewHandlerInterface $viewHandler
     * @param Request $request
     * @param string $repo
     * @param string $user
     * @param int $number
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function issuesDetail(
        IssueService $issueService,
        ViewHandlerInterface $viewHandler,
        Request $request,
        string $repo,
        string $user,
        int $number
    ) {
        $token = $request->headers->get('Authorization');
        if ($token) {
            $issueService->setAuthorization($token);
        }

        $issue = $issueService->getIssue($user, $repo, $number);
        $comments = $issueService->getComments($user, $repo, $number);

        $view = $this->view([
            'issue' => $issue,
            'comments' => $comments,
        ]);

        return $viewHandler->handle($view);

    }

}