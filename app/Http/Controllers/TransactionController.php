<?php

namespace App\Http\Controllers;

use App\Models\OxyApiToken;
use App\Models\Transaction;
use App\Services\External\Oxy\CustomerOxyService;
use App\Services\External\Oxy\ItemMasterOxyService;
use App\Services\External\Oxy\LocationOxyService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perpage          = $request->integer('perpage', 10);
            $id               = $request->input('id');
            $oxy_location_id  = $request->input('oxy_location_id');
            $fulfillment_type = $request->input('fulfillment_type');
            $status           = $request->input('status');
            $start_date       = $request->input('start_date');
            $end_date         = $request->input('end_date');

            $userSession = Auth::user();

            if ($userSession['role'] === 'CASHIER') {
                $oxy_location_id = $userSession['oxy_location_id'];
            }

            $query = Transaction::query();

            if (!empty($id)) {
                $query->where('id', 'like', "%{$id}%");
            }

            if (!empty($oxy_location_id)) {
                $query->where('oxy_location_id', $oxy_location_id);
            }

            if (!empty($fulfillment_type)) {
                $query->where('fulfillment_type', $fulfillment_type);
            }

            if (!empty($status)) {
                $query->where('status', $status);
            }

            // 🔹 Filter start & end date
            if (!empty($start_date)) {
                $query->whereDate(
                    'created_at',
                    '>=',
                    Carbon::parse($start_date)->startOfDay()
                );
            }

            if (!empty($end_date)) {
                $query->whereDate(
                    'created_at',
                    '<=',
                    Carbon::parse($end_date)->endOfDay()
                );
            }

            $transactions = $query
                ->latest()
                ->paginate($perpage)
                ->withQueryString();

            // Cache locations selama 2 menit
            $locations = Cache::remember('oxy_locations', 120, function () {
                $oxyAccessToken = OxyApiToken::getAccessToken();
                $locationsRes = LocationOxyService::getLocations(token: $oxyAccessToken);

                if (!$locationsRes['success']) {
                    throw new \Exception('Failed to fetch locations: ' . $locationsRes['message']);
                }

                return $locationsRes['data']['data'];
            });

            return Inertia::render('Backpage/Transaction/Index', [
                'title' => 'Transaction',
                'transactions' => $transactions,
                'filters' => [
                    'perpage'          => $perpage,
                    'id'               => $id,
                    'oxy_location_id'  => $oxy_location_id,
                    'fulfillment_type' => $fulfillment_type,
                    'status'           => $status,
                    'start_date'       => $start_date,
                    'end_date'         => $end_date,
                ],
                'locations' => $locations
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors([
                'message' => 'Failed to fetch transactions. ' . $e->getMessage(),
            ]);
        }
    }

    public function show(string $id)
    {
        try {
            $transaction = Transaction::query()
                ->with(['items', 'shipment', 'pickup'])
                ->where('id', $id)
                ->first();

            $oxyAccessToken = OxyApiToken::getAccessToken();

            $itemMasters = [];

            foreach ($transaction->items()->pluck('oxy_item_master_id') as $id) {
                $response = ItemMasterOxyService::getItemMasterDetail(
                    token: $oxyAccessToken,
                    itemMasterId: $id,
                    locationId: $transaction->oxy_location_id ?? null,
                    page: 0,
                    size: 1
                );

                //  guard ketat untuk response OXY
                if (
                    ($response['success'] ?? false) !== true ||
                    !isset($response['data']['data']) ||
                    empty($response['data']['data']) ||
                    !isset($response['data']['data'][0])
                ) {
                    // optional: log untuk monitoring
                    Log::warning('Failed get item master from OXY', [
                        'oxy_item_master_id' => $id,
                        'response' => $response,
                    ]);

                    continue; // skip item ini
                }

                $itemMasters[] = $response['data']['data'][0];
            }

            $locationsRes = LocationOxyService::getLocations(token: $oxyAccessToken,);

            $customerRes = CustomerOxyService::getCustomerById(token: $oxyAccessToken, oxyCustomerId: $transaction->oxy_customer_id);

            if (!$locationsRes['success']) {
                return back()->withErrors(['message' => 'Failed to fetch locations. ' . $locationsRes['message'],]);
            }

            if (!$customerRes['success']) {
                return back()->withErrors(['message' => 'Failed to fetch customer. ' . $customerRes['message'],]);
            }

            $locations = $locationsRes['data']['data'];

            $customer = $customerRes['data']['data'];

            return Inertia::render('Backpage/Transaction/Detail', [
                'title' => 'Transaction',
                'transaction' => $transaction,
                'itemMasters' => $itemMasters,
                'locations' => $locations,
                'customer' => $customer
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors([
                'message' => 'Failed to fetch transaction. ' . $e->getMessage(),
            ]);
        }
    }
}
