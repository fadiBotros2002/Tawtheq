<?php

namespace App\Http\Controllers;

use App\Enums\DocumentType;
use App\Http\Requests\AttachDocumentFileRequest;
use App\Http\Requests\StoreDocumentRequest;
use App\Models\Document;
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
            ->with(['user', 'category'])
            ->when(! $request->user()->isAdmin(), fn ($query) => $query->where('user_id', $request->user()->id))
            ->latest()
            ->paginate(15);

        return view('documents.index', compact('documents'));
    }

    /**
     * Show the document registration form.
     */
    public function create(Request $request): View
    {
        $categories = $request->user()->categories()->orderBy('slug')->get();

        return view('documents.create', [
            'types' => DocumentType::cases(),
            'categories' => $categories,
        ]);
    }

    /**
     * Register a new document (with optional file).
     */
    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $document = $this->documentService->registerDocument(
            $request->validated(),
            $request->user(),
            $request->file('file')
        );

        $message = $document->isVerified()
            ? __('diwan.messages.document_verified')
            : __('diwan.messages.document_registered');

        return redirect()
            ->route('documents.show', $document)
            ->with('success', $message);
    }

    /**
     * Attach a file to a draft document.
     */
    public function attachFile(AttachDocumentFileRequest $request, Document $document): RedirectResponse
    {
        $this->authorizeDocumentAccess($request, $document);

        abort_unless($document->isDraft(), 422);

        $this->documentService->attachFile(
            $document,
            $request->user(),
            $request->file('file')
        );

        return redirect()
            ->route('documents.show', $document)
            ->with('success', __('diwan.messages.document_verified'));
    }

    /**
     * Display document details with verification URL and QR code.
     */
    public function show(Request $request, Document $document): View
    {
        $this->authorizeDocumentAccess($request, $document);

        $document->load(['user', 'category']);

        return view('documents.show', compact('document'));
    }

    /**
     * Stream a document file through Laravel without exposing S3 URLs.
     */
    public function stream(Request $request, Document $document): StreamedResponse
    {
        $this->authorizeDocumentAccess($request, $document);

        abort_unless($document->hasFile(), 404);
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
    public function verify(
        string $document_name,
        string $doctype,
        string $category,
        string $date,
        string $sequence
    ): View {
        $document = $this->findDocumentForVerify($document_name, $doctype, $category, $date, $sequence);
        $document->load(['user', 'category']);

        return view('public.verify', compact('document'));
    }

    /**
     * Public file stream for the verification page.
     */
    public function verifyStream(
        string $document_name,
        string $doctype,
        string $category,
        string $date,
        string $sequence
    ): StreamedResponse {
        $document = $this->findDocumentForVerify($document_name, $doctype, $category, $date, $sequence);

        abort_unless($document->hasFile(), 404);
        abort_unless(Storage::disk('s3')->exists($document->s3_path), 404);

        return Storage::disk('s3')->response(
            $document->s3_path,
            $document->original_filename,
            ['Content-Disposition' => 'inline']
        );
    }

    private function authorizeDocumentAccess(Request $request, Document $document): void
    {
        $user = $request->user();

        abort_unless(
            $user && ($user->isAdmin() || $document->user_id === $user->id),
            403
        );
    }

    private function findDocumentForVerify(
        string $document_name,
        string $doctype,
        string $category,
        string $date,
        string $sequence
    ): Document {
        abort_unless(in_array($doctype, ['inbound', 'outbound'], true), 404);
        abort_unless(preg_match('/^\d{8}$/', $date), 404);

        return Document::query()
            ->with(['user', 'category'])
            ->where('name_slug', $document_name)
            ->where('type', $doctype)
            ->whereHas('category', fn ($query) => $query->where('slug', $category))
            ->where('upload_date', $date)
            ->where('sequence', (int) ltrim($sequence, '0') ?: 0)
            ->firstOrFail();
    }
}
