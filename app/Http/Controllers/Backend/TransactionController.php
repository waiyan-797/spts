<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Notification;
use App\Models\Topup;
use App\Models\Transaction;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class TransactionController extends Controller
{
    public function index()
    {
        // Use the query builder to construct the SQL query with pagination and count
        $usersQuery = User::role('user');
        $users = $usersQuery->paginate(25);

        // Get the total count of users from the paginator
        $userCount = $usersQuery->count();

        return view('backend.transaction.index', compact('users', 'userCount'));
    }


    public function topup(Request $request)
    {
        // Validate the input
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|min:1',
        ]);

        // Get the user and add the top-up amount to their balance
        $user = User::find($request->user_id);
        $user->balance += $request->amount;
        $user->save();

        // Create a new transaction record
        Transaction::create([
            'user_id' => $request->user_id,
            'staff_id' => Auth::id(),
            'amount' => $request->amount,
            'income_outcome' => 'income',
        ]);

        $url = 'https://fcm.googleapis.com/fcm/send';

        $FcmToken = [$user->device_token];

        $serverKey = "AAAAsebg8eM:APA91bFGPk22SqABJrOpFgGzbOVd5L_Qt6_BbfZAhmJLUZsfqHtsPyNghEiREIhI6juPZsRRVDIy8Qm8Y03ER04t3w-wkQqSrJXcR83ooYqGFP-Zm7-CF6Sj9UsS8qPgNJKsuEvQImru";

        $data = [
            "registration_ids" => $FcmToken,
            "notification" => [
                "title" => 'Top Up',
                "body" => 'You recieved ' . $request->amount . 'MMK',
            ]
        ];
        $encodedData = json_encode($data);

        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt(
            $ch,
            CURLOPT_URL,
            $url
        );
        curl_setopt(
            $ch,
            CURLOPT_POST,
            true
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);
        // Redirect back to the top-up form with a success message
        return redirect()->back()->with('success', 'Top-up successful.');
    }

    public function transactionHistory()
    {
        // Use the query builder to construct the SQL query with pagination and count
        $transactionQuery = Transaction::where('income_outcome', 'income')
            ->whereIn('user_id', function ($query) {
                $query->select('id')->from(with(new User)->getTable())->whereIn('id', Role::where('name', 'user')->firstOrFail()->users->pluck('id'));
            })
            ->orderBy('created_at', 'desc');

        $transactions = $transactionQuery->paginate(25);

        // Get the total count of transactions from the paginator
        $transactionCount = $transactions->total();

        // Return the transaction history view with the transactions and count data
        return view('backend.transaction.history', compact('transactions', 'transactionCount'));
    }



    public function search(Request $request)
    {
        $key = $request->input('key');
        $users = User::role('user')->where(function ($query) use ($key) {
            $query->where('name', 'LIKE', "%$key%")
                ->orWhere('email', 'LIKE', "%$key%")
                ->orWhere('phone', 'LIKE', "%$key%");
        })->paginate(25);
        $userCount = $users->total();
        return view('backend.transaction.index', compact('users', 'userCount'));
    }


    public function historySearch(Request $request)
    {
        $key = $request->input('key');

        $transactions = Transaction::query();

        if ($key) {
            $transactions->leftJoin('users', 'transactions.user_id', '=', 'users.id')
                ->where(function ($query) use ($key) {
                    $query->where('users.name', 'LIKE', "%$key%")
                        ->orWhere('users.email', 'LIKE', "%$key%")
                        ->orWhere('users.phone', 'LIKE', "%$key%")
                        ->orWhereDate('transactions.created_at', $key);
                });
        }

        $transactionCount = $transactions->count();
        $transactions = $transactions->paginate(25);

        return view('backend.transaction.history', compact('transactions', 'transactionCount'));
    }


    public function notification()
    {
        $notificationsQuery = Notification::latest();
        $notifications = $notificationsQuery->paginate(25);
        $notificationsCount = $notificationsQuery->count();
        $notificationsUnread = $notificationsQuery->where('status', 'unread')->count();
        return view('backend.transaction.notification', compact('notifications', 'notificationsCount', 'notificationsUnread'));
    }
    
    public function notification_show($id)
    {
        $notification = Notification::find($id);
        return response()->json(['screenshot' => $notification->screenshot]);
    }

    public function topupDone($notification)
    {
        $notification = Notification::find($notification);
        $notification->status = 'read';
        $notification->update();
        return redirect()->back();
    }

        public function notificationDestroy(Request $request)
    {
        $notifications = json_decode($request->input('notifications'));

        if (!is_null($notifications)) {
            foreach ($notifications as $notificationId) {
                $notification = Notification::find($notificationId);

                if ($notification) {
                    if ($notification->screenshot) {
                        Storage::delete('uploads/images/screenshots/' . $notification->screenshot);
                    }

                    $notification->delete();
                }
            }
        }

        return redirect()->route('topup.notification');
    }
    
    public function transactionFxi()
    {
        // $transactions = Transaction::all();
        // $commissions = $transactions->where('income_outcome', 'outcome');
        // $topups = $transactions->where('income_outcome', 'income');
        // $tripNullCounter = 0;
        // $commissions->map(function ($commission) use (&$tripNullCounter) {
        //     $trip = Trip::where('created_at', $commission->created_at)->first();
        //     if($trip == null){
        //         $tripNullCounter++;
        //         Commission::create([
        //         'user_id' => $commission->user_id,
        //         'amount' => $commission->amount,
        //     ]);
        //     }else{
        //         Commission::create([
        //         'user_id' => $commission->user_id,
        //         'trip_id' => $trip->id,
        //         'amount' => $commission->amount,
        //         'created_at' => $trip->created_at,
        //         'updated_at' => $trip->updated_at
        //     ]);
        //     }
        // });

        // $topups->map(function ($topup) {
        //     if ($topup->amount < 0) {
        //         $income_outcome = 'outcome';
        //     } else {
        //         $income_outcome = 'income';
        //     }
        //     Topup::create([
        //         'user_id' => $topup->user_id,
        //         'staff_id' => 1,
        //         'amount' => abs($topup->amount),
        //         'income_outcome' => $income_outcome,
        //         'created_at' => $topup->created_at,
        //         'updated_at' => $topup->updated_at
        //     ]);
        // });

        return [
            // 'tripNullCounter' => $tripNullCounter,
            'topup_in' => Topup::all()->where('income_outcome', 'income')->sum('amount'),
            'topup_in_count' => Topup::all()->where('income_outcome', 'income')->count(),
            'topup_out' => Topup::all()->where('income_outcome', 'outcome')->sum('amount'),
            'topup_out_count' => Topup::all()->where('income_outcome', 'outcome')->count(),
            'commission' => Commission::all()->sum('amount'),
            'commission_count' => Commission::all()->count(),
            'trips' => Trip::all()->count()
        ];

    }
}
