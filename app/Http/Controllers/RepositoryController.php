<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resources; // We only use this now
use Illuminate\Support\Facades\Auth;

class RepositoryController extends Controller
{
    // ==========================================
    // 1. NOTES SECTION (Public Resources)
    // ==========================================

    // Show list of notes
    public function indexNotes() {
        // We look for resources that are 'notes' and 'public'
        $notes = Resources::where('type', 'note')
                          ->where('is_public', true)
                          ->latest()
                          ->get();
                          
        return view('repository.notes.index', compact('notes'));
    }

    // Show upload form
    public function createNote() {
        return view('repository.notes.create');
    }

    // Save the note to DB
    public function storeNote(Request $request) {
        $request->validate([
            'title' => 'required',
            'subject_tag' => 'required',
            'file' => 'required|mimes:pdf,docx|max:10240'
        ]);

        // 1. Handle File Upload (Professional Way)
        if ($request->hasFile('file')) {
            // This stores the file in 'storage/app/public/notes'
            $path = $request->file('file')->store('notes', 'public');
        } else {
            return back()->with('error', 'File upload failed.');
        }

        // 2. Save to Database
        Resources::create([
            'teacher_id' => Auth::id(),
            'title' => $request->title,
            'subject_tag' => $request->subject_tag,
            'type' => 'note',           // Explicitly set type
            'file_url' => $path,        // UPDATED: 'file_path' is now 'file_url'
            'is_public' => true,
            'group_id' => 1,            // Default or catch-all group
            'subject_id' => 1,          // Default subject (you can update this logic later)
        ]);

        return redirect()->route('repo.notes.index')->with('success', 'Note Uploaded!');
    }
    
    // Delete a note
    public function destroyNote($id) {
        $note = Resources::findOrFail($id);
        
        // Optional: Delete the actual file from storage to save space
        // Storage::disk('public')->delete($note->file_url);
        
        $note->delete();
        return back()->with('success', 'Note deleted.');
    }

    // ==========================================
    // EXAM SECTION REMOVED
    // We deleted the 'exam_papers' table, so we deleted the code here.
    // We will use QuizController for assessments now.
    // ==========================================
}