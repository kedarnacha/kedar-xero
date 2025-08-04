<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use League\OAuth2\Client\Provider\GenericProvider;
use GuzzleHttp\Client;
use XeroAPI\XeroPHP\Configuration;
use XeroAPI\XeroPHP\Api\AccountingApi;
use XeroAPI\XeroPHP\Api\IdentityApi;

class XeroAuthController extends Controller
{
    public function getProvider(): GenericProvider
    {
        return new GenericProvider([
            'clientId'                => env('XERO_CLIENT_ID'),
            'clientSecret'            => env('XERO_CLIENT_SECRET'),
            'redirectUri'             => env('XERO_REDIRECT_URI'),
            'urlAuthorize'            => 'https://login.xero.com/identity/connect/authorize',
            'urlAccessToken'          => 'https://identity.xero.com/connect/token',
            'urlResourceOwnerDetails' => 'https://api.xero.com/api.xro/2.0/Organisation',
            'scopes'                  => [
                'openid',
                'email',
                'profile',
                'offline_access',
                'accounting.transactions',
                'accounting.contacts',
                'accounting.settings',
                'accounting.reports.read'
            ]
        ]);
    }

    public function redirectToXero()
    {
        $queryParams = [
            'response_type' => 'code',
            'client_id'     => env('XERO_CLIENT_ID'),
            'redirect_uri'  => env('XERO_REDIRECT_URI'),
            'scope'         => implode(' ', [
                'openid',
                'email',
                'profile',
                'offline_access',
                'accounting.transactions',
                'accounting.contacts',
                'accounting.settings',
                'accounting.reports.read'
            ]),
            'state' => 'test123'
        ];

        Session::put('oauth2state', $queryParams['state']);

        $authorizationUrl = 'https://login.xero.com/identity/connect/authorize?' . http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986);

        return redirect()->away($authorizationUrl);
    }

    public function handleCallback(Request $request)
    {
        // Optional: log URI for debug
        logger('Redirect URI used: ' . env('XERO_REDIRECT_URI'));

        $provider = $this->getProvider();

        if (!$request->has('code') || $request->get('state') !== Session::get('oauth2state')) {
            return redirect('/')->withErrors('Invalid OAuth state');
        }

        try {
            $accessToken = $provider->getAccessToken('authorization_code', [
                'code' => $request->get('code'),
            ]);

            $config = Configuration::getDefaultConfiguration()->setAccessToken($accessToken->getToken());
            $identityApi = new IdentityApi(new Client(), $config);
            $connections = $identityApi->getConnections();
            $tenantId = $connections[0]->getTenantId();

            Session::put('xero_token', $accessToken->getToken());
            Session::put('xero_tenant_id', $tenantId);

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            logger()->error('Xero OAuth error: ' . $e->getMessage());
            return redirect('/')->withErrors('Xero Authentication failed.');
        }
    }

    public function showDashboard()
    {
        $token = Session::get('xero_token');
        $tenantId = Session::get('xero_tenant_id');

        if (!$token || !$tenantId) {
            return redirect('/auth/xero');
        }

        try {
            $config = Configuration::getDefaultConfiguration()->setAccessToken($token);
            $accountingApi = new AccountingApi(new Client(), $config);

            $org = $accountingApi->getOrganisations($tenantId)->getOrganisations()[0];
            $invoices = $accountingApi->getInvoices($tenantId, null, null, 'Date DESC', null, null, null, null, 5)->getInvoices();
            $contacts = $accountingApi->getContacts($tenantId, null, null, null, null, null, null, 5)->getContacts();
            $accounts = $accountingApi->getAccounts($tenantId)->getAccounts();
            $bankAccounts = collect($accounts)->where('type', 'BANK');

            return view('dashboard', compact('org', 'invoices', 'contacts', 'bankAccounts'));
        } catch (\Exception $e) {
            logger()->error('Error fetching data from Xero: ' . $e->getMessage());
            return redirect('/')->withErrors('Failed to fetch data from Xero.');
        }
    }
}
