<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NewsletterSubscriberController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $subscribers = NewsletterSubscriber::query()
            ->when($q !== '', fn ($query) => $query->where('email', 'like', '%' . addcslashes($q, '\\%_') . '%'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Newsletter/Index', [
            'subscribers' => $subscribers,
            'filters'     => ['q' => $q],
            'activeCount' => NewsletterSubscriber::whereNull('unsubscribed_at')->count(),
        ]);
    }

    /**
     * Export CSV des inscrits actifs (compatible Excel : BOM UTF-8, séparateur « ; »).
     */
    public function export()
    {
        $rows = NewsletterSubscriber::whereNull('unsubscribed_at')->orderBy('created_at')->get(['email', 'created_at']);

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['email', 'inscrit_le'], ';');

            foreach ($rows as $row) {
                fputcsv($out, [$row->email, $row->created_at->format('Y-m-d H:i')], ';');
            }

            fclose($out);
        }, 'newsletter-inscrits-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function destroy(NewsletterSubscriber $subscriber)
    {
        $subscriber->delete();

        return back()->with('success', 'Inscrit supprimé.');
    }
}
