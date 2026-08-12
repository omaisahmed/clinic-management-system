<?php

declare(strict_types=1);

namespace Modules\Documents\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Modules\Audit\Facades\Audit;
use Modules\Documents\Models\Document;
use Modules\Patients\Models\Patient;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('documents.view');

        $documents = Document::query()
            ->search($request->query('q'))
            ->category($request->query('category'))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('documents::index', [
            'documents' => $documents,
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('documents.create');

        return view('documents::create', [
            'patients' => $this->patientOptions(),
            'patientId' => request('patient'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('documents.create');

        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'title' => ['required', 'string', 'max:150'],
            'category' => ['nullable', 'string', 'max:80'],
            'notes' => ['nullable', 'string'],
            'file' => ['required', 'file', 'max:20480', 'mimes:pdf,jpeg,jpg,png,doc,docx,xls,xlsx'],
        ]);

        $file = $request->file('file');

        $document = Document::create([
            ...$validated,
            'clinic_id' => current_clinic()?->id,
            'file_path' => $file->store('documents', 'public'),
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
        ]);

        Audit::record('Document Uploaded', 'documents', $document, [
            'patient_id' => $document->patient_id,
        ]);

        return redirect()
            ->route('documents.show', $document)
            ->with('toast', [['type' => 'success', 'message' => 'Document uploaded.']]);
    }

    public function show(Document $document): View
    {
        Gate::authorize('documents.view');

        $document->load('patient', 'uploader');

        return view('documents::show', [
            'document' => $document,
        ]);
    }

    public function edit(Document $document): View
    {
        Gate::authorize('documents.update');

        return view('documents::edit', [
            'document' => $document,
            'patients' => $this->patientOptions(),
        ]);
    }

    public function update(Request $request, Document $document): RedirectResponse
    {
        Gate::authorize('documents.update');

        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'title' => ['required', 'string', 'max:150'],
            'category' => ['nullable', 'string', 'max:80'],
            'notes' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:20480', 'mimes:pdf,jpeg,jpg,png,doc,docx,xls,xlsx'],
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $validated['file_path'] = $file->store('documents', 'public');
            $validated['original_name'] = $file->getClientOriginalName();
            $validated['mime_type'] = $file->getMimeType();
            $validated['file_size'] = $file->getSize();
        }

        $document->update($validated);

        Audit::record('Document Updated', 'documents', $document, [
            'patient_id' => $document->patient_id,
        ]);

        return redirect()
            ->route('documents.show', $document)
            ->with('toast', [['type' => 'success', 'message' => 'Document updated.']]);
    }

    public function download(Document $document): StreamedResponse
    {
        Gate::authorize('documents.download');

        return Storage::disk('public')->download($document->file_path, $document->original_name);
    }

    public function destroy(Document $document): RedirectResponse
    {
        Gate::authorize('documents.delete');

        Storage::disk('public')->delete($document->file_path);

        $document->delete();

        Audit::record('Document Deleted', 'documents', $document, [
            'patient_id' => $document->patient_id,
        ]);

        return redirect()
            ->route('documents.index')
            ->with('toast', [['type' => 'success', 'message' => 'Document removed.']]);
    }

    /**
     * @return array<string, string>
     */
    private function patientOptions(): array
    {
        return Patient::query()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get()
            ->mapWithKeys(fn (Patient $patient): array => [$patient->id => $patient->full_name])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function categoryOptions(): array
    {
        return Document::query()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->filter()
            ->mapWithKeys(fn (string $category): array => [$category => $category])
            ->all();
    }
}
