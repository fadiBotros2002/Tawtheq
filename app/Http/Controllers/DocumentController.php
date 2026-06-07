<?php

namespace App\Http\Controllers;

use App\Enums\DocumentType;
use App\Http\Requests\StoreDocumentRequest;
use App\Models\Document;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function __construct(
        protected DocumentService $documentService
    ) {}

    /**
     * Display a listing of the user's documents.
     */
    public function index(Request $request): View
    {
        $documents = Document::query()
            ->with('user')
            ->when(! $request->user()->isAdmin(), fn ($query) => $query->where('user_id', $request->user()->id))
            ->latest()
            ->paginate(15);

        return view('documents.index', compact('documents'));
    }

    /**
     * Show the upload form.
     */
    public function create(): View
    {
        return view('documents.create', [
            'types' => DocumentType::cases(),
        ]);
    }

    /**
     * Store a newly uploaded document.
     */
    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $document = $this->documentService->createDocument(
            $request->validated(),
            $request->user(),
            $request->file('file')
        );

        return redirect()
            ->route('documents.show', $document)
            ->with('success', __('diwan.messages.document_uploaded'));
    }

    /**
     * Display document details with verification URL and QR code.
     */
    public function show(Document $document): View
    {
        return view('documents.show', compact('document'));
    }

    /**
     * Stream a document file through Laravel without exposing S3 URLs.
     */
    public function stream(Document $document): StreamedResponse
    {
        abort_unless(Storage::disk('s3')->exists($document->s3_path), 404);

        return Storage::disk('s3')->response(
            $document->s3_path,
            $document->original_filename,
            ['Content-Disposition' => 'inline']
        );
    }

    /**
     * Public verification page with metadata and secure file preview.
     */
    public function verify(string $username, string $doctype, string $date, string $sequence): View
    {
        abort_unless(in_array($doctype, ['inbound', 'outbound'], true), 404);
        abort_unless(preg_match('/^\d{8}$/', $date), 404);

        $user = User::query()->where('username', $username)->firstOrFail();

        $document = Document::query()
            ->where('user_id', $user->id)
            ->where('type', $doctype)
            ->where('upload_date', $date)
            ->where('sequence', (int) ltrim($sequence, '0') ?: 0)
            ->firstOrFail();

        return view('public.verify', compact('document'));
    }

    /**
     * Public file stream for the verification page.
     */
    public function verifyStream(string $username, string $doctype, string $date, string $sequence): StreamedResponse
    {
        abort_unless(in_array($doctype, ['inbound', 'outbound'], true), 404);
        abort_unless(preg_match('/^\d{8}$/', $date), 404);

        $user = User::query()->where('username', $username)->firstOrFail();

        $document = Document::query()
            ->where('user_id', $user->id)
            ->where('type', $doctype)
            ->where('upload_date', $date)
            ->where('sequence', (int) ltrim($sequence, '0') ?: 0)
            ->firstOrFail();

        abort_unless(Storage::disk('s3')->exists($document->s3_path), 404);

        return Storage::disk('s3')->response(
            $document->s3_path,
            $document->original_filename,
            ['Content-Disposition' => 'inline']
        );
    }
}
