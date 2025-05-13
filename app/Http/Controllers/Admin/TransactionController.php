<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
class TransactionController extends Controller
{
    public function index(Request $request){

         if ($request->ajax()) {
                
            $query = \App\Models\Transaction::with(['user'])->where('gym_id',auth()->user()->id);

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

            $data = $query->latest()->get();
            
            return DataTables::of($data)
                ->addIndexColumn() // Adds the iteration column
                ->addColumn('created_at_formatted', function ($row) {
                    return \Carbon\Carbon::parse($row->created_at)->format('D m, Y h:i:s');
                })
                ->editColumn('name', function ($row) {
                    $name = '<h2 class="table-avatar">
                        <a>
                            <span>' . htmlspecialchars($row->user->name, ENT_QUOTES, 'UTF-8') . '</span>
                        </a>
                    </h2>';

                    return $name;
                })
                ->addColumn('phone', function ($row) {
                    return $row->country_code ?? '+91' . ' ' . $row->user->phone;
                })
                ->addColumn('type', function ($row) {
                    $status = $row->type;
                    if($row->type == 'assign_package'){
                        $status = "Purchase Activity";
                    }elseif($row->type == 'assign_pt'){
                        $status = "Purchase PT";
                    }elseif($row->type == 'assign_plan'){
                        $status = "Purchase Plan";
                    }elseif($row->type == 'repayment'){
                        $status = "Payment Clearification";
                    }
                    return $status ?? 'N/A';
                })
                ->addColumn('payment_type', function ($row) {
                    return ucfirst($row->payment_type) ?? 'N/A';
                })
                ->addColumn('received', function ($row) {
                    return $row->received_amt ?? 'N/A';
                })
                ->addColumn('balance', function ($row) {
                    return $row->balance_amt ?? 'N/A';
                })
                ->addColumn('total', function ($row) {
                    return $row->total_amt ?? 'N/A';
                })
                ->addColumn('status', function ($row) {
                    $statusClass = $row->status == "pending" ? 'danger' : 'success';
                    $status = ucfirst($row->status);

                    return '<div class="action-label">
                                <a class="btn btn-white btn-sm btn-rounded" href="javascript:void(0);">
                                    <i class="fa-regular fa-circle-dot text-' . $statusClass . '"></i> ' . $status . '
                                </a>
                            </div>';
                })

                ->addColumn('action', function ($row) {
                    $encodedId = base64_encode($row->user->id);
                    $transactionRoute = route('admin.users.transactions', $encodedId);
                
                    return '<div class="dropdown dropdown-action">
                                <a href="javascript:void(0);" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="material-icons">more_vert</i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="' . $transactionRoute . '" class="dropdown-item"><i class="fa-solid fa-money-bill-wave m-r-5"></i> Pay</a>
                                    
                                </div>
                            </div>';
                })

                ->rawColumns(['name', 'status', 'total', 'balance', 'received', 'phone','action'])
                ->make(true);
        }

        $pendingBalanceSum = \App\Models\Transaction::where('gym_id', auth()->user()->id)
            ->sum('balance_amt');

        $users = \App\Models\User::where('added_by', auth()->user()->id)->where('salary',NULL)->get();

        return view('admin.pages.transactions.index',compact('pendingBalanceSum','users'));
    }
}
