<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Pengeluaran;
use Carbon\Carbon;

class FinanceController extends Controller
{
  protected function rangeForFilter(string $filter, ?string $baseDate = null)
  {
    $now = $baseDate ? Carbon::parse($baseDate) : Carbon::now();
    switch ($filter) {
      case 'today':
      case 'day':
        $start = $now->copy()->startOfDay();
        $end = $now->copy()->endOfDay();
        break;
      case '7':
      case 'week':
      case 'last7':
        $start = $now->copy()->subDays(6)->startOfDay();
        $end = $now->copy()->endOfDay();
        break;
      case '30':
      case 'month':
      case 'last30':
        $start = $now->copy()->subDays(29)->startOfDay();
        $end = $now->copy()->endOfDay();
        break;
      case 'year':
      case 'lastyear':
      case '365':
        $start = $now->copy()->subYear()->startOfDay();
        $end = $now->copy()->endOfDay();
        break;
      default:
        // default last 30 days
        $start = $now->copy()->subDays(29)->startOfDay();
        $end = $now->copy()->endOfDay();
        break;
    }
    return [$start, $end];
  }

  public function index(Request $request)
  {
    $filter = $request->query('filter', 'last30');
    $baseDate = $request->query('baseDate');
    [$start, $end] = $this->rangeForFilter($filter, $baseDate);

    // completed orders only
    $orders = Order::where('status', 'completed')->whereBetween('created_at', [$start, $end])->get();

    $gross = $orders->sum(function ($o) {
      return floatval($o->total_price ?? 0);
    });
    $ordersCount = $orders->count();
    $avgOrder = $ordersCount ? ($gross / $ordersCount) : 0;

    // filtered expenses for the chart, summary totals and table
    $expenses = Pengeluaran::whereBetween(
      'expenseDate',
      [$start->toDateString(), $end->toDateString()]
    )->get()
      ->map(function ($e) {
        $arr = $e->toArray();

        /*
     * expenseDate tetap menjadi tanggal transaksi yang dipilih admin.
     * created_at memberikan jam ketika pengeluaran dicatat.
     *
     * Contoh:
     * expenseDate = 2026-08-31
     * created_at  = 2026-08-31 18:43:25
     *
     * effectiveDate = 2026-08-31 18:43:25
     */
        if ($e->expenseDate) {
          $expenseDateTime = Carbon::parse($e->expenseDate)
            ->setTime(
              $e->created_at->hour,
              $e->created_at->minute,
              $e->created_at->second
            );

          $arr['effectiveDate'] = $expenseDateTime->toDateTimeString();
        } else {
          $arr['effectiveDate'] = $e->created_at->toDateTimeString();
        }

        return $arr;
      })->values();

    $totalExpenses = $expenses->sum(function ($e) {
      return floatval($e['expenseAmount'] ?? 0);
    });

    $net = $gross - $totalExpenses;

    // income vs expense time series depending on filter
    $series = [];
    $period = [];

    switch ($filter) {
      case 'today':
      case 'day':
        $bucket = $start->copy();
        for ($i = 0; $i < 8; $i++) {
          $key = $bucket->format('H');
          $period[$key] = [
            'income' => 0,
            'expense' => 0,
            'label' => $bucket->format('H:00'),
            'start' => $bucket->copy(),
            'end' => $bucket->copy()->addHours(3),
          ];
          $bucket->addHours(3);
        }

        foreach ($orders as $o) {
          $time = Carbon::parse($o->created_at);
          foreach ($period as $key => $bucket) {
            if ($time->gte($bucket['start']) && $time->lt($bucket['end'])) {
              $period[$key]['income'] += floatval($o->total_price ?? 0);
              break;
            }
          }
        }

        foreach ($expenses as $e) {
          $time = Carbon::parse($e['effectiveDate']);
          foreach ($period as $key => $bucket) {
            if ($time->gte($bucket['start']) && $time->lt($bucket['end'])) {
              $period[$key]['expense'] += floatval($e['expenseAmount'] ?? 0);
              break;
            }
          }
        }
        break;

      case 'year':
      case 'lastyear':
      case '365':
        $cursor = $start->copy()->startOfMonth();
        while ($cursor->lte($end)) {
          $key = $cursor->format('Y-m');
          $period[$key] = [
            'income' => 0,
            'expense' => 0,
            'label' => $cursor->format('M Y'),
            'start' => $cursor->copy()->startOfMonth(),
            'end' => $cursor->copy()->endOfMonth(),
          ];
          $cursor->addMonth();
        }

        foreach ($orders as $o) {
          $time = Carbon::parse($o->created_at);
          foreach ($period as $key => $bucket) {
            if ($time->gte($bucket['start']) && $time->lte($bucket['end'])) {
              $period[$key]['income'] += floatval($o->total_price ?? 0);
              break;
            }
          }
        }

        foreach ($expenses as $e) {
          $time = Carbon::parse($e['effectiveDate']);
          foreach ($period as $key => $bucket) {
            if ($time->gte($bucket['start']) && $time->lte($bucket['end'])) {
              $period[$key]['expense'] += floatval($e['expenseAmount'] ?? 0);
              break;
            }
          }
        }
        break;

      case 'week':
      case 'last7':
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
          $key = $cursor->format('Y-m-d');
          $period[$key] = [
            'income' => 0,
            'expense' => 0,
            'label' => $cursor->format('d M'),
            'start' => $cursor->copy()->startOfDay(),
            'end' => $cursor->copy()->endOfDay(),
          ];
          $cursor->addDay();
        }

        foreach ($orders as $o) {
          $time = Carbon::parse($o->created_at);
          $key = $time->format('Y-m-d');
          if (isset($period[$key])) {
            $period[$key]['income'] += floatval($o->total_price ?? 0);
          }
        }

        foreach ($expenses as $e) {
          $date = Carbon::parse($e['effectiveDate'])->format('Y-m-d');
          if (isset($period[$date])) {
            $period[$date]['expense'] += floatval($e['expenseAmount'] ?? 0);
          }
        }
        break;

      case 'month':
      case 'last30':
      default:
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
          $key = $cursor->format('Y-m-d');
          $period[$key] = [
            'income' => 0,
            'expense' => 0,
            'label' => $cursor->format('d M'),
            'start' => $cursor->copy()->startOfDay(),
            'end' => $cursor->copy()->endOfDay(),
          ];
          $cursor->addDay();
        }

        foreach ($orders as $o) {
          $time = Carbon::parse($o->created_at);
          $key = $time->format('Y-m-d');
          if (isset($period[$key])) {
            $period[$key]['income'] += floatval($o->total_price ?? 0);
          }
        }

        foreach ($expenses as $e) {
          $date = Carbon::parse($e['effectiveDate'])->format('Y-m-d');
          if (isset($period[$date])) {
            $period[$date]['expense'] += floatval($e['expenseAmount'] ?? 0);
          }
        }
        break;
    }

    $series = array_values(array_map(function ($k) use ($period) {
      return ['date' => $k, 'income' => $period[$k]['income'], 'expense' => $period[$k]['expense'], 'label' => $period[$k]['label']];
    }, array_keys($period)));

    // expense by category
    $byCategory = $expenses->groupBy('expenseCategory')->map(function ($group, $cat) {
      return ['category' => $cat ?: 'Uncategorized', 'amount' => $group->sum('expenseAmount')];
    })->values()->all();

    $incomeRows = $orders->map(function ($o) {
      return [
        'date' => Carbon::parse($o->created_at)->toDateString(),
        'name' => $o->nama_customer ?: ($o->customer_no_hp ?: 'Guest'),
        'order_id' => $o->id,
        'total' => floatval($o->total_price ?? 0),
      ];
    });

    // expense rows for table follow the currently selected filter
    $expenseRows = $expenses;

    return response()->json([
      'gross' => round($gross, 2),
      'totalExpenses' => round($totalExpenses, 2),
      'net' => round($net, 2),
      'avgOrder' => round($avgOrder, 2),
      'ordersCount' => $ordersCount,
      'expensesCount' => $expenseRows->count(),
      'series' => $series,
      'byCategory' => $byCategory,
      'expenses' => $expenseRows,
      'incomeRows' => $incomeRows,
      'filter' => $filter,
      'start' => $start->toDateString(),
      'end' => $end->toDateString(),
    ]);
  }
}
