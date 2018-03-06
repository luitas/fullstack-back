<?php
/**
 * Created by PhpStorm.
 * User: liutri
 * Date: 2018-03-01
 * Time: 13:07
 */

namespace App\Service;

use \Github\Api\Search;
use \Github\Api\Issue;
use \Github\Client as GithubClient;


class IssueService
{

    protected $tokenStorage;

    /**
     * @var \Github\Api\Issue
     */
    protected $issuesApi;

    /**
     * @var \Github\Api\Search
     */
    protected $searchApi;


    /**
     * @var \Github\Client
     */
    protected $client;

    /**
     * IssueService constructor.
     */
    public function __construct(
    )
    {

        $client = new GithubClient();
        $this->client = $client;

        $this->searchApi = $this->client->api('search');
        $this->issuesApi = $this->client->api('issue');
    }


    public function setAuthorization(string $token) {
        $this->client->authenticate($token, GithubClient::AUTH_HTTP_TOKEN );
    }

    /**
     * @param null|string $state
     * @return array
     */
    public function getIssues(string $state = null) {
        $query = [
            'assignee' => $this->getUsername(),
        ];

        if (!empty($state)) {
            $query['state'] = $state;
        }

        return $this->searchApi->issues($this->createQuery($query));
    }

    /**
     * @param string $owner
     * @param string $repo
     * @param int $number
     *
     * @return array
     */
    public function getComments(string $owner, string $repo, int $number) {
        return $this->issuesApi->comments()->all($owner, $repo, $number);
    }

    /**
     * @param string $owner
     * @param string $repo
     * @param int $number
     * @return array
     */
    public function getIssue(string $owner, string $repo, int $number) {
        return $this->issuesApi->show($owner, $repo, $number);
    }

    /**
     * @param null $state
     * @return mixed
     */
    public function getIssuesCount($state = null) {
        $issues = $this->getIssues($state);

        return $issues['total_count'];
    }


    /**
     * @param array $query
     * @return string
     */
    protected function createQuery(array $query) {
        $queryArray = [];
        foreach ($query as $key => $item) {
            $queryArray[] = "$key:$item";
        }
        $queryString = implode(" ", $queryArray);

        return $queryString;
    }

    /**
     * @return string
     */
    protected function getUsername() {
        $user = $this->client->api('me')->show();
        return $user['login'];
    }

}