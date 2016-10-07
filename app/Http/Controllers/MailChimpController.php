<?php
/**
 * @file
 *
 * This controller contain actions related to create, list, update MailChimp via api..
 */

namespace App\Http\Controllers;

use App\Http\Requests\ListMemberRequest;
use App\Http\Requests\ListRequest;
use App\Http\Requests\MemberRequest;
use Illuminate\Http\Request;
use Flash, Redirect, Response;
use App\Http\Requests;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\RequestException;

class MailChimpController extends Controller
{
    protected $client = null;

    /**
     * MailChimpController constructor.
     */
    public function __construct()
    {
        $this->middleware('web');
        $this->client = new GuzzleHttpClient();
    }

    /**
     * Get all the lists via sending reuqest to the MailChimp api..
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $apikey = config('constants.MAILCHIMP_API_KEY');
            $dataCenter = $this->getMailChimpAccountDataCenter();
            // send api call to mailchimp
            $apiRequest = $this->client->request('GET', 'http://' . $dataCenter . '.api.mailchimp.com/3.0/lists',
                                ['auth' => [config('constants.MAILCHIMP_USERNAME'), $apikey]]);

            $content = json_decode($apiRequest->getBody()->getContents());
            $returnData['status'] = $apiRequest->getStatusCode();
            $returnData['data'] = $content;
        } catch (RequestException $re) {
            $returnData['status'] = config('constants.STATUS_ERROR');
            $returnData['reason'] = $re->getMessage();
        }
        return Response::json($returnData);
    }

    /**
     * Add a newly created list via sending API request to MailChimp.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ListRequest $request)
    {
        try {
            $apikey = config('constants.MAILCHIMP_API_KEY');
            $dataCenter = $this->getMailChimpAccountDataCenter();
            $apiRequest = $this->client->request('POST', 'http://' . $dataCenter . '.api.mailchimp.com/3.0/lists', [
                'body' => $request->all(),
                'auth' => [config('constants.MAILCHIMP_USERNAME'), $apikey]
            ]);
            $response = $apiRequest->send();
            $returnData['status'] = $response->getStatusCode();
            $returnData['data'] = $response->getBody();

        } catch (RequestException $e) {
            $returnData['status'] = config('constants.STATUS_ERROR');
            $returnData['message'] = $e->getMessage();
        }
        return Response::json($returnData);
    }


    /*
     * Batch subscribe or unsubscribe list members.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string $listId , The unique id for the list
     *  @return \Illuminate\Http\Response
     */
    public function addListMember(ListMemberRequest $request, $listId)
    {
        try {
            if (empty($listId)) {
                // If no list_id passed through return error.
                $returnData['status'] = config('constants.STATUS_ERROR');
                $returnData['message'] = 'No list id send!';
                return Response::json($returnData);
            }
            $apikey = config('constants.MAILCHIMP_API_KEY');
            $dataCenter = $this->getMailChimpAccountDataCenter();

            $apiRequest = $this->client->request('POST', 'http://' . $dataCenter . '.api.mailchimp.com/3.0/lists/'. $listId .'/members', [
                'body' => $request->all(),
                'auth' => [config('constants.MAILCHIMP_USERNAME'), $apikey]
            ]);
            $response = $apiRequest->send();
            $returnData['status'] = $response->getStatusCode();
            $returnData['data'] = $response->getBody();

        } catch (RequestException $e) {
            $returnData['status'] = config('constants.STATUS_ERROR');
            $returnData['message'] = $e->getMessage();
        }
        return Response::json($returnData);
    }

    /**
     * Update a list member.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string $listId The unique id for the list
     * @param  string $subscriberHash The MD5 hash of the lowercase version of the list member’s email address.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateListMember(ListMemberRequest $request, $listId, $subscriberHash)
    {
        if (empty($listId) ) {
            // If no list_id passed througn return error.
            $returnData['status'] = config('constants.STATUS_ERROR');
            $returnData['message'] = 'No list id provided!';
            return Response::json($returnData);
        }

        if (empty($subscriberHash) ) {
            // If no subscriber hash passed through return error.
            $returnData['status'] = config('constants.STATUS_ERROR');
            $returnData['message'] = 'No subscriber hash provided!';
            return Response::json($returnData);
        }
        try {
            $apikey = config('constants.MAILCHIMP_API_KEY');
            $dataCenter = $this->getMailChimpAccountDataCenter();

            $apiRequest = $this->client->request('PUT', 'http://' . $dataCenter . '.api.mailchimp.com/3.0/lists/'. $listId .'/members/' .$subscriberHash, [
                'body' => $request->all(),
                'auth' => [config('constants.MAILCHIMP_USERNAME'), $apikey]
            ]);
            $response = $apiRequest->send();
            $returnData['status'] = $response->getStatusCode();
            $returnData['data'] = $response->getBody();
        } catch (RequestException $e) {
            $returnData['status'] = config('constants.STATUS_ERROR');
            $returnData['message'] = $e->getMessage();
        }
        return Response::json($returnData);

    }

    /**
     * Get the  data center for your MailChimp account.
     * $return string
     */
    private function getMailChimpAccountDataCenter()
    {
        return substr(config('constants.MAILCHIMP_API_KEY'), strpos(config('constants.MAILCHIMP_API_KEY'), '-') + 1);
    }
}
