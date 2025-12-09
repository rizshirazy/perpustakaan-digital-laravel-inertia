<?php

namespace App\Http\Controllers;

use App\Enums\FinePaymentStatus;
use App\Enums\ReturnBookStatus;
use App\Models\ReturnBook;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Response;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;

        $params = [
            'transaction_details' => [
                'order_id'     => $request->order_id,
                'gross_amount' => $request->gross_amount,
            ],
            'customer_details' => [
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'email'      => $request->email,
            ]
        ];

        try {
            $snap = Snap::getSnapToken($params);

            return response()->json([
                'snap_token' => $snap,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function callback(Request $request): JsonResponse
    {
        $serverKey    = config('services.midtrans.server_key');
        $signatureKey = signatureMidtrans($request->order_id, $request->status_code, $request->gross_amount, $serverKey);

        if ($request->signature_key !== $signatureKey) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }

        $returnBook = ReturnBook::where('return_code', $request->order_id)->first();

        if (!$returnBook) {
            return response()->json([
                'error' => 'Transaksi pengembalian tidak ditemukan'
            ], 404);
        }

        if (!$returnBook->fine) {
            return response()->json([
                'error' => 'Denda tidak ditemukan'
            ], 404);
        }

        switch ($request->transaction_status) {
            case 'settlement':
                $returnBook->fine->payment_status = FinePaymentStatus::SUCCESS->value;
                $returnBook->fine->save();

                $returnBook->status = ReturnBookStatus::RETURNED->value;
                $returnBook->save();

                return response()->json([
                    'message' => 'Berhasil melakukan pembayaran'
                ]);
                break;

            case 'capture':
                $returnBook->fine->payment_status = FinePaymentStatus::SUCCESS->value;
                $returnBook->fine->save();

                $returnBook->status = ReturnBookStatus::RETURNED->value;
                $returnBook->save();

                return response()->json([
                    'message' => 'Berhasil melakukan pembayaran'
                ]);
                break;

            case 'pending':
                $returnBook->fine->payment_status = FinePaymentStatus::PENDING->value;
                $returnBook->fine->save();

                return response()->json([
                    'message' => 'Pembayaran tertunda'
                ]);
                break;

            case 'expire':
                $returnBook->fine->payment_status = FinePaymentStatus::FAILED->value;
                $returnBook->fine->save();

                return response()->json([
                    'message' => 'Pembayaran kadaluarsa'
                ]);
                break;

            case 'cancel':
                $returnBook->fine->payment_status = FinePaymentStatus::FAILED->value;
                $returnBook->fine->save();

                return response()->json([
                    'message' => 'Pembayaran dibatalkan'
                ]);
                break;

            default:
                return response()->json([
                    'message' => 'Status transaksi tidak diketahui'
                ], 400);
                break;
        }
    }

    public function success(): Response
    {
        return inertia('Payments/Success');
    }
}
