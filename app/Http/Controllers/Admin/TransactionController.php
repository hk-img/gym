<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
class TransactionController extends Controller
{
    public function index(Request $request){

        if ($request->ajax()) {
            $query = Transaction::with('user')
                ->where('gym_id', auth()->user()->id)
                ->orderBy('created_at', 'asc'); // chronological order for balance calc

            if ($request->month) {
                $query->whereMonth('created_at', Carbon::parse($request->month)->month)
                    ->whereYear('created_at', Carbon::parse($request->month)->year);
            }

            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $transactions = $query->get();

            // Group transactions by user
            $grouped = $transactions->groupBy('user_id');
            $formattedData = [];

            foreach ($grouped as $userId => $userTransactions) {
                $runningOpening = 0;

                foreach ($userTransactions as $row) {
                    $received = floatval($row->received_amt ?? 0);
                    $paymentType = $row->payment_status; // 'Cr' or 'Dr'
                    $totalAmt = floatval($row->total_amt ?? 0);
                    $balanceAmt = floatval($row->balance_amt ?? 0);

                    // Calculate closing
                    if ($paymentType === 'Cr') {
                        $closing = $runningOpening + $received;
                    } elseif ($paymentType === 'Dr') {
                        $received = floatval($row->total_amt ?? 0); // override
                        $closing = $runningOpening - $received;
                    } else {
                        $closing = $runningOpening;
                    }

                    $encodedId = base64_encode($row->user->id);
                    $transactionRoute = route('admin.users.transactions', $encodedId);

                    $formattedData[] = [
                        'id' => $row->id,
                        'user' => $row->user->name ?? 'N/A',
                        'phone' => $row->user->phone ?? 'N/A',
                        'created_at_formatted' => Carbon::parse($row->created_at)->format('d D m, Y h:i:s'),
                        'type' => match($row->type) {
                            'assign_package' => 'Purchase Activity',
                            'assign_pt' => 'Purchase PT',
                            'assign_plan' => 'Purchase Plan',
                            'repayment' => 'Payment Clearification',
                            default => ucfirst($row->type) ?? 'N/A'
                        },
                        'opening' => number_format($runningOpening, 2),
                        'amount' => number_format($received, 2),
                        'closing' => number_format($closing, 2),
                        'total_amt' => number_format($totalAmt, 2),
                        'balance_amt' => number_format($balanceAmt, 2),
                        'payment_status' => '<div class="action-label">
                            <a class="btn btn-white btn-sm btn-rounded" href="javascript:void(0);">
                                <i class="fa-regular fa-circle-dot text-' . ($paymentType == 'Dr' ? 'danger' : 'success') . '"></i> ' . ucfirst($paymentType) . '
                            </a>
                        </div>',
                        'status' => '<div class="action-label">
                            <a class="btn btn-white btn-sm btn-rounded" href="javascript:void(0);">
                                <i class="fa-regular fa-circle-dot text-' . ($row->status == 'pending' ? 'danger' : 'success') . '"></i> ' . ucfirst($row->status) . '
                            </a>
                        </div>',
                        'action' => '<div class="dropdown dropdown-action">
                            <a href="javascript:void(0);" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="material-icons">more_vert</i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="' . $transactionRoute . '" class="dropdown-item"><i class="fa-solid fa-money-bill-wave m-r-5"></i> Pay</a>
                            </div>
                        </div>',
                    ];

                    $runningOpening = $closing; // update for next transaction
                }
            }

            $formattedData = array_reverse($formattedData);

            // Calculate pending balance
            $pendingBalanceSum = $transactions->where('payment_status', 'Cr')->sum('balance_amt');

            // Build response
            $response = DataTables::of($formattedData)
                ->addIndexColumn()
                ->rawColumns(['payment_status', 'status', 'action'])
                ->make(true)
                ->getData(true); // get array

            $response['pendingBalanceSum'] = number_format($pendingBalanceSum, 2);

            return response()->json($response);
        }

        $pendingBalanceSum = \App\Models\Transaction::where('gym_id', auth()->user()->id)
            ->where("payment_status",'Cr')
            ->sum('balance_amt');

        $users = \App\Models\User::where('added_by', auth()->user()->id)->where('salary',NULL)->get();

        return view('admin.pages.transactions.index',compact('pendingBalanceSum','users'));
    }
}