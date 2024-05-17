<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Notification;
use App\Models\Trip;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class IncomeSummaryController extends Controller
{
    public function incomeSummary($type)
    {
        $incomes = Transaction::all();

        $totalTopup = Notification::all()->sum('amount');

        $totalCommission = $incomes->where('income_outcome', 'outcome')->reduce(function ($carry, $item) {
            return $carry + $item->amount;
        }, 0);

        if ($type == 'month') {
            $collection = collect($incomes);
            $incomes = $collection->groupBy(function ($income) {
                return Carbon::parse($income->created_at)->format('Y-m');
            })->map(function ($group) {
                $commission = $group->where('income_outcome', 'outcome')->reduce(function ($carry, $item) {
                    return $carry + $item->amount;
                }, 0);
                $topup = $group->where('income_outcome', 'income')->reduce(function ($carry, $item) {
                    return $carry + $item->amount;
                }, 0);
                return [
                    'date' => $group->first()->created_at->format('F, Y'),
                    'commission' => $commission,
                    'topup' => $topup
                ];
            });
        } elseif ($type == 'year') {
            $collection = collect($incomes);
            $incomes = $collection->groupBy(function ($income) {
                return Carbon::parse($income->created_at)->format('Y');
            })->map(function ($group) {
                $commission = $group->where('income_outcome', 'outcome')->reduce(function ($carry, $item) {
                    return $carry + $item->amount;
                }, 0);
                $topup = $group->where('income_outcome', 'income')->reduce(function ($carry, $item) {
                    return $carry + $item->amount;
                }, 0);
                return [
                    'date' => $group->first()->created_at->format('Y'),
                    'commission' => $commission,
                    'topup' => $topup
                ];
            });
        } else {
            $collection = collect($incomes);
            $incomes = $collection->groupBy(function ($income) {
                return Carbon::parse($income->created_at)->format('Y-m-d');
            })->map(function ($group) {
                $commission = $group->where('income_outcome', 'outcome')->reduce(function ($carry, $item) {
                    return $carry + $item->amount;
                }, 0);
                $topup = $group->where('income_outcome', 'income')->reduce(function ($carry, $item) {
                    return $carry + $item->amount;
                }, 0);
                return [
                    'date' => $group->first()->created_at->format('F j, Y'),
                    'commission' => $commission,
                    'topup' => $topup
                ];
            });
        }

        $perPage = 25;
        $currentPage = request()->query('page', 1);
        $pagedIncomes = new LengthAwarePaginator(
            $incomes->forPage($currentPage, $perPage),
            $incomes->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url()]
        );

        $totalUsers = User::role('user')->count();
        $totalTrips = Trip::count();
        return view('backend.incomeSummary', ['incomes' => $pagedIncomes, 'totalComission' => $totalCommission, 'totalTopup' => $totalTopup, 'totalUsers' => $totalUsers, 'totalTrips' => $totalTrips]);
    }


}
