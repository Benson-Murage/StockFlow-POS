<?php

namespace App\Http\Controllers;

use App\Models\MpesaPayment;
use App\Models\Sale;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    /**
     * Safaricom callback endpoint.
     */
    public function callback(Request $request)
    {
        $body = $request->input('Body.stkCallback', []);
        $checkoutRequestId = data_get($body, 'CheckoutRequestID');
        $merchantRequestId = data_get($body, 'MerchantRequestID');
        $resultCode = (string) data_get($body, 'ResultCode');
        $resultDesc = data_get($body, 'ResultDesc');

        $mpesaPayment = MpesaPayment::where('checkout_request_id', $checkoutRequestId)
            ->orWhere('merchant_request_id', $merchantRequestId)
            ->first();

        if (! $mpesaPayment) {
            Log::warning('MPesa callback received for unknown payment', ['checkoutRequestId' => $checkoutRequestId]);
            return response()->json(['message' => 'Payment not found'], 404);
        }

        // Idempotency: if already processed success, do nothing
        if ($mpesaPayment->status === 'success') {
            return response()->json(['message' => 'Already processed'], 200);
        }

        $mpesaPayment->result_code = $resultCode;
        $mpesaPayment->result_description = $resultDesc;
        $mpesaPayment->payload = $body;

        if ($resultCode !== '0') {
            $mpesaPayment->status = 'failed';
            $mpesaPayment->save();
            return response()->json(['message' => 'Payment failed'], 200);
        }

        DB::transaction(function () use ($mpesaPayment) {
            $mpesaPayment->status = 'success';
            $mpesaPayment->save();

            if ($mpesaPayment->sale_id) {
                $sale = Sale::lockForUpdate()->find($mpesaPayment->sale_id);

                if ($sale) {
                    // Record transaction if not already captured
                    $existing = Transaction::where('sales_id', $sale->id)
                        ->where('payment_method', 'MPesa')
                        ->where('amount', $mpesaPayment->amount)
                        ->first();

                    if (! $existing) {
                        Transaction::create([
                            'sales_id' => $sale->id,
                            'store_id' => $sale->store_id,
                            'contact_id' => $sale->contact_id,
                            'transaction_date' => $sale->sale_date,
                            'amount' => $mpesaPayment->amount,
                            'payment_method' => 'MPesa',
                            'transaction_type' => 'sale',
                            'note' => 'MPesa payment',
                        ]);
                    }

                    $sale->amount_received = ($sale->amount_received ?? 0) + $mpesaPayment->amount;
                    if ($sale->amount_received >= $sale->total_amount) {
                        $sale->payment_status = 'completed';
                        $sale->status = 'completed';
                    }
                    $sale->save();
                }
            }
        });

        return response()->json(['message' => 'Payment processed'], 200);
    }
}

