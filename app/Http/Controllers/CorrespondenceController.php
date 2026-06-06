<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCorrespondenceRequest;
use App\Http\Resources\CorrespondenceResource;
use App\Models\Correspondence;
use App\Services\CorrespondenceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CorrespondenceController extends Controller
{
    public function __construct(
        protected CorrespondenceService $correspondenceService
    ) {}

    /**
     * Display a listing of correspondences with filters.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['category', 'serial_number', 'status']);

        $correspondences = Correspondence::query()
            ->forRole($request->user())
            ->filter($filters)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $correspondenceResources = CorrespondenceResource::collection($correspondences);

        return view('correspondences.index', [
            'correspondences' => $correspondences,
            'correspondenceResources' => $correspondenceResources,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new correspondence.
     */
    public function create(): View
    {
        return view('correspondences.create');
    }

    /**
     * Store a newly created correspondence.
     */
    public function store(StoreCorrespondenceRequest $request): RedirectResponse
    {
        $correspondence = $this->correspondenceService->createCorrespondence(
            $request->validated(),
            $request->user()
        );

        return redirect()
            ->route('correspondences.show', $correspondence)
            ->with('success', 'Correspondence created successfully and is now pending review.');
    }

    /**
     * Display the specified correspondence.
     */
    public function show(Correspondence $correspondence): View
    {
        $correspondenceData = (new CorrespondenceResource($correspondence))->resolve();

        return view('correspondences.show', [
            'correspondence' => $correspondence,
            'correspondenceData' => $correspondenceData,
        ]);
    }

    /**
     * Download the correspondence attachment.
     */
    public function download(Correspondence $correspondence): StreamedResponse
    {
        abort_unless($correspondence->file_path, 404);
        abort_unless(Storage::disk('public')->exists($correspondence->file_path), 404);

        return Storage::disk('public')->download(
            $correspondence->file_path,
            basename($correspondence->file_path)
        );
    }

    /**
     * Approve the specified correspondence.
     */
    public function approve(Request $request, Correspondence $correspondence): RedirectResponse
    {
        abort_unless($request->user()->isChecker(), 403, 'Only checkers can approve correspondences.');

        if ($correspondence->isFrozen()) {
            return redirect()
                ->route('correspondences.show', $correspondence)
                ->with('error', 'This correspondence has already been approved.');
        }

        $this->correspondenceService->approveCorrespondence($correspondence);

        return redirect()
            ->route('correspondences.show', $correspondence)
            ->with('success', 'Correspondence approved and serial number issued successfully.');
    }

    /**
     * Public verification page (no authentication required).
     */
    public function verify(string $uuid): View
    {
        $correspondence = Correspondence::query()
            ->where('uuid', $uuid)
            ->firstOrFail();

        $correspondenceData = (new CorrespondenceResource($correspondence))->resolve();

        return view('public.verify', [
            'correspondence' => $correspondence,
            'correspondenceData' => $correspondenceData,
        ]);
    }
}
