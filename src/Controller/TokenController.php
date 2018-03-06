<?php

namespace App\Controller;

use App\Exception\BadRequestException;
use FOS\RestBundle\Controller\FOSRestController as Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\ViewHandlerInterface;
use Http\Message\MessageFactory;
use Http\Message\StreamFactory;
use Symfony\Component\HttpFoundation\Request;
use Http\Client\Curl\Client;

class TokenController extends Controller
{
    /**
     * @Rest\Post("/api/tokens", name="createToken")
     *
     * @param Request $request
     * @param ViewHandlerInterface $viewHandler
     * @param MessageFactory $messageFactory
     * @param StreamFactory $streamFactory
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws BadRequestException
     */
    public function index(
        Request $request,
        ViewHandlerInterface $viewHandler,
        MessageFactory $messageFactory,
        StreamFactory $streamFactory
    )
    {

        $params = [
//            'client_id' => $this->getParameter('hwi_oauth.resource_owners.hwi_github.client_id'),
            'client_id' => $this->getParameter('client_id'),
            'client_secret' => $this->getParameter('client_secret'),
            'code' => $request->get('code'),
            'state' => $request->get('state'),
        ];

        $requestMessage = $messageFactory->createRequest('POST', 'https://github.com/login/oauth/access_token');
        $options = [
            CURLOPT_POSTFIELDS => $params,
        ];
        $client = new Client($messageFactory, $streamFactory, $options);
        $responseMessage =  $client->sendRequest($requestMessage);

        $body = $responseMessage->getBody()->getContents();
        parse_str($body, $output);
        if (empty($output['access_token'])) {
            $message = "Error: " . (!empty($output['error_description']) ? $output['error_description'] : 'bad parameters or code missing');
            throw new BadRequestException($message);
        }

        $view = $this->view([
            'accessToken' => $output['access_token'],
            'tokenType' => $output['token_type']
        ]);

        return $viewHandler->handle($view);
    }
}
