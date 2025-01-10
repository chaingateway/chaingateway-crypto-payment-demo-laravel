<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\PaymentSession;
use App\Models\Transaction;
use App\Models\Wallet;

class PaymentController extends Controller
{
    private $apiUrl;
    private $apiKey;
    private $network;
    private $coldWallet;

    public function __construct()
    {
        $this->apiUrl = config('app.chaingateway.api_url');
        $this->apiKey = config('app.chaingateway.api_key');
        $this->network = config('app.chaingateway.network');
        $this->coldWallet = config('app.chaingateway.cold_wallet');
    }

    public function showPaymentPage()
    {
        return view('payment');
    }

    /**
     * Start payment session
     * 
     * This Function will create a new wallet address and webhook in Chaingateway.
     * 
     */

    public function startPaymentSession(Request $request)
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
            'X-Network' => $this->network,
        ])->post("{$this->apiUrl}/tron/addresses");

        if ($response->successful()) {
            $walletData = $response->json()['data'];

            $wallet = Wallet::create([
                'address' => $walletData['address'],
                'private_key' => $walletData['privateKey'],
            ]);

            $wbhookUrl = route('handleWebhook');
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
                'X-Network' => $this->network,
            ])->post("{$this->apiUrl}/tron/webhooks", [
                'to' => $wallet->address,
                'url' => $wbhookUrl,
            ]);
            $webhookData = $response->json()['data'];

            $paymentSession = PaymentSession::create([
                'wallet_id' => $wallet->id,
                'webhook_id' => $webhookData['id'],
                'status' => 'Pending',
            ]);


            return redirect()->route('showPaymentSession', ['id' => $paymentSession->id]);
        }

        return back()->withErrors(['error' => 'Failed to create payment session.']);
    }

    public function showPaymentSession($id)
    {
        $paymentSession = PaymentSession::with('wallet')->findOrFail($id);
        return view('payment-session', compact('paymentSession'));
    }

    public function handleWebhook(Request $request)
    {
        $transactionData = $request->all();

        $wallet = Wallet::where('address', $transactionData['to'])->first();
        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        $paymentSession = PaymentSession::where('wallet_id', $wallet->id)->first();
        if (!$paymentSession) {
            return response()->json(['error' => 'Payment session not found'], 404);
        }

        /**
         * We always should check the transaction receipt if the transaction really was successful
         */
        $receiptResponse = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
            'X-Network' => $this->network,
        ])->get("{$this->apiUrl}/tron/transactions/{$transactionData['txid']}/receipt/decoded");

        if ($receiptResponse->successful() && $receiptResponse->json()['data']['status'] == 'SUCCESS') {
            $paymentSession->status = 'Completed';
            $paymentSession->received_amount = $transactionData['amount'];

            /**
             * you should check if the amount received is the same as the amount requested
             * if not, you should refund the user or do something else.
             * Amounts could vary, for example, due to the transaction fee. You should consider that by adding a margin.
             * You should also check if the transaction is a TRC20 token transaction and the contract is the same as the one you are expecting.
             * 
             */
            $amountDifference = abs($paymentSession->amount - $transactionData['amount']);
            $allowedDifference = $paymentSession->amount * 0.10; // 10% of the payment session amount

            if ($amountDifference >= $allowedDifference) {
                // Allow a difference of up to 10%, update the contract address
                // If the amount is overpaid or underpaid by more than 10%
                if ($transactionData['amount'] > $paymentSession->amount) {
                    $paymentSession->status = 'overpaid';
                } else {
                    $paymentSession->status = 'underpaid';
                }
            }

            $paymentSession->contract_address = $transactionData['contractaddress'];
            if($paymentSession->currency == 'JST' && $transactionData['contractaddress'] != 'TF17BgPaZYbz8oxbjhriubPDsA7ArKoLX3'){
                $paymentSession->status = 'Wrong currency received';
            }

            /**
             * Do this if you want to move your funds to an cold wallet only. 
             * You can also send the funds to another wallet or do nothing.
             * In case of TRC20 tokens, you need to ensure you have enough TRX to pay for the transaction fee.
             * You can also use the Chaingateway Tron Paymaster feature so you dont need to handle the fees.
             */
            $endpoint = $transactionData['contractaddress']
                ? "{$this->apiUrl}/tron/transactions/trc20"
                : "{$this->apiUrl}/tron/transactions";

            Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
                'X-Network' => $this->network,
            ])->post($endpoint, [
                'amount' => $transactionData['amount'],
                'privatekey' => $wallet->private_key,
                'to' => $this->coldWallet,
                'from' => $transactionData['to'],
                'contractaddress' => $transactionData['contractaddress'],
            ]);
        } else {
            $paymentSession->status = 'Failed';
        }

        /**
         * Delete Webhook in Chaingateway
         * Only do that if you will not use the address again
        */
         $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
            'X-Network' => $this->network,
        ])->delete("{$this->apiUrl}/tron/webhooks/{$paymentSession->webhook_id}");

        $paymentSession->save();
        return response()->json(['status' => 'success']);
    }
}
