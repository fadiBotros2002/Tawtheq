<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * List the authenticated user's categories.
     */
    public function index(Request $request): View
    {
        $categories = $request->user()
            ->categories()
            ->withCount('documents')
            ->orderBy('slug')
            ->paginate(20);

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a category.
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Store a new category for the authenticated user.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $request->user()->categories()->create($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', __('diwan.messages.category_created'));
    }
}
