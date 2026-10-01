<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ContactReplyMail;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Throwable;

class ContactMessageController extends Controller
{
    /**
     * Liste : filtre par statut + recherche (nom, email, téléphone, sujet, n° de commande).
     */
    public function index(Request $request)
    {
        $status = (string) $request->query('status', '');
        $q      = trim((string) $request->query('q', ''));

        $messages = ContactMessage::with('order:id,reference')
            ->when(
                array_key_exists($status, ContactMessage::STATUSES),
                fn ($query) => $query->where('status', $status)
            )
            ->search($q)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/ContactMessages/Index', [
            'messages'     => $messages,
            'filters'      => ['status' => $status, 'q' => $q],
            'statuses'     => $this->statusOptions(),
            'statusCounts' => ContactMessage::selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
        ]);
    }

    /**
     * Détail : message, commande associée, historique des réponses.
     */
    public function show(ContactMessage $contactMessage)
    {
        $contactMessage->load([
            'order.items',
            'user:id,name,email',
            'replies.admin:id,name',
        ]);

        return Inertia::render('Admin/ContactMessages/Show', [
            'message'       => $contactMessage,
            'hasAttachment' => (bool) $contactMessage->attachment,
            'statuses'      => $this->statusOptions(),
        ]);
    }

    /**
     * Répond au client par e-mail, garde la réponse dans l'historique et passe le message à « Répondu ».
     */
    public function reply(Request $request, ContactMessage $contactMessage)
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'min:3', 'max:5000'],
        ]);

        // On envoie d'abord : si l'e-mail échoue, rien n'est enregistré et l'admin peut réessayer.
        try {
            Mail::to($contactMessage->email)->send(new ContactReplyMail($contactMessage, $data['body']));
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', "L'e-mail n'a pas pu être envoyé. Vérifiez la configuration mail et réessayez.");
        }

        $contactMessage->replies()->create([
            'admin_id' => $request->user()->id,
            'body'     => $data['body'],
        ]);

        $contactMessage->update([
            'status'      => 'answered',
            'answered_at' => now(),
        ]);

        return back()->with('success', 'Réponse envoyée à ' . $contactMessage->email . '.');
    }

    /**
     * Changer le statut (prendre en charge, marquer comme traité, clôturer, rouvrir).
     */
    public function updateStatus(Request $request, ContactMessage $contactMessage)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(ContactMessage::STATUSES))],
        ]);

        $contactMessage->update(['status' => $data['status']]);

        return back()->with('success', 'Statut mis à jour : ' . ContactMessage::STATUSES[$data['status']] . '.');
    }

    /**
     * Télécharge la pièce jointe (stockée sur le disque privé).
     */
    public function attachment(ContactMessage $contactMessage)
    {
        $disk = Storage::disk('local'); // disque privé (voir ContactController::store)

        abort_unless(
            $contactMessage->attachment && $disk->exists($contactMessage->attachment),
            404
        );

        $extension = pathinfo($contactMessage->attachment, PATHINFO_EXTENSION);

       

        return response()->download(
            $disk->path($contactMessage->attachment),
            "piece-jointe-message-{$contactMessage->id}." . $extension
        );
    }

    private function statusOptions(): array
    {
        return collect(ContactMessage::STATUSES)
            ->map(fn ($label, $value) => ['value' => $value, 'label' => $label])
            ->values()
            ->all();
    }
}
