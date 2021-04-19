<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FacebookController extends Controller
{

    public function authLoginFacebook(Request $request)
    {
        $fb = new \Facebook\Facebook([
            'app_id' => env('FACEBOOK_CLIENT_ID'),
            'app_secret' => env('FACEBOOK_CLIENT_SECRET'),
            'default_graph_version' => 'v2.10',
        ]);

        $helper = $fb->getJavaScriptHelper();

        if (isset($request->state)) {
            $helper->getPersistentDataHandler()->set('state', $_GET['state']);
        }

        try {
            $accessToken = $helper->getAccessToken();
        } catch (\Facebook\Exception\ResponseException $e) {

            return response()->json(['graph-error' => $e->getMessage()], 500);
            exit;
        } catch (\Facebook\Exception\SDKException $e) {
            // When validation fails or other local issues
            return response()->json(['sdk-facebook-error' => $e->getMessage()], 500);
            exit;
        }

        if (!isset($accessToken)) {
            return response()->json(['error-message' => 'No cookie set or no OAuth data could be obtained from cookie.']);
            exit;
        }
        //https://graph.facebook.com/USER_ID/picture

        $graph_response = $fb->get('/me?fields=id,name,email', $accessToken);
        $fb_user_info = $graph_response->getGraphUser();
        // dd($accessToken->getValue());
        $data = [
            'id' => $fb_user_info['id'],
            'name' => $fb_user_info['name'],
            'email' => $fb_user_info['email']
        ];
        return response()->json($data);
    }
}
